# OTP Flow and Service

Beginner-friendly reference for OTP in CSMS: what it is, where it's stored, and the full conditional flow.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/UserManagement/UserController.php` (password-change OTP flow)
- Service
  - `app/Services/Authentication/OtpService.php`
- Models
  - `app/Models/UserOtp.php`
- Mail
  - `app/Mail/OtpMail.php`
- Routes
  - `routes/web.php` (profile routes)

## Security Implementation

### Input Validation
- **OTP Format Validation**: OTPs must be 6-digit numeric codes only.
- **Purpose Validation**: OTP purpose must be one of the valid purposes (e.g., `password_change`).
- **User Validation**: OTP operations validate that the user exists and is active.

### Authorization
- **User-Specific OTPs**: OTPs are scoped to specific user IDs and purposes.
- **No Cross-User Access**: Users can only verify OTPs issued to their own account.
- **Password-Change Context**: OTP verification requires the user to be authenticated and in the password-change flow.

### Rate Limiting
- **OTP Issuing**: Recommend implementing rate limiting on OTP issuance to prevent OTP spamming.
- **OTP Verification**: Recommend implementing rate limiting on OTP verification attempts to prevent brute force attacks.
- **Lockout Mechanism**: Recommend implementing temporary lockout after multiple failed verification attempts.

### Transaction Safety
- **Atomic Operations**: OTP creation and replacement happen atomically via `updateOrCreate`.
- **Expiry Enforcement**: OTP expiry is enforced at verification time, preventing use of expired codes.

### Audit Logging
- **OTP Issuance**: Recommend logging OTP issuance events for security monitoring.
- **OTP Verification**: Recommend logging successful and failed verification attempts.
- **Migration Events**: Legacy OTP migration from `users.otp` to `user_otps` should be logged.

### Cryptographic Security
- **Hashed Storage**: OTPs are hashed before storage, never stored in plaintext.
- **One-Time Use**: OTPs are designed for single-use verification.
- **Expiry Time**: OTPs expire after 10 minutes by default (configurable).

### Network Security
- **Email Transmission**: OTPs sent via email using `OtpMail` - recommend using encrypted email transmission.
- **TLS Enforcement**: Recommend enforcing HTTPS for all OTP-related endpoints.

## What OTP Means Here

- OTP = One-Time Password (6-digit code).
- Used only for: **Password-change verification** (profile flow).
- Registration does NOT use OTP — `email_verified_at` is set to `now()` directly on account creation.

## Storage Model

- OTPs are stored in `user_otps` (one row per `user_id` + `purpose`).
- Stored fields include:
  - `user_id`
  - `purpose`
  - `otp` (hashed)
  - `otp_expires_at`
- Legacy support:
  - If an old OTP exists in `users.otp`, `Authentication\OtpService::migrateLegacyOtp()` moves it to `user_otps`.

## OTP Purposes

From `OtpService`:

- `PURPOSE_PASSWORD_CHANGE = password_change`

## Conditions (If / Then)

### OTP Issuing (`issueForUser($user, $purpose)`)

- If OTP is issued:
  - Then generate a random 6-digit code.
  - Then hash OTP before saving.
  - Then replace existing OTP for the same `(user_id, purpose)` via `updateOrCreate`.
  - Then set expiry to `now + 10 minutes` by default.
  - Then send email using `OtpMail`.

### OTP Validation (`validate($user, $otp, $purpose)`)

- If no record exists for `(user_id, purpose)`:
  - Then return error: invalid or already used.
- If record exists but is expired:
  - Then return error: expired.
- If hash does not match:
  - Then return error: invalid OTP.
- If all checks pass:
  - Then return `null` (meaning valid).

### OTP Clear (`clear($user, $purpose)`)

- If clear is called:
  - Then delete OTP rows for that user and purpose.

## Sequences (Flows)

### Password Change OTP (Profile)

1. Logged-in user requests password change.
2. System validates current password, issues OTP for `password_change`, stores pending hash in session.
3. User submits OTP to confirm password change.
4. System validates OTP via `OtpService`.
5. If valid: applies new password hash, clears OTP and session, redirects with success toast.

### Resend Password OTP

1. User clicks "Resend OTP" on profile page.
2. System checks session for pending password change.
3. If valid: issues new OTP for `password_change`, sends email, redirects with success toast.

## Security Notes

- OTP is never stored in plaintext.
- OTP is purpose-specific (`password_change` only).
- OTP is one-time use (cleared after success).
- OTP expiry is enforced by timestamp check.
