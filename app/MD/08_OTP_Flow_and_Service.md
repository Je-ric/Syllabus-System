# OTP Flow and Service

Beginner-friendly reference for OTP in CSMS: what it is, where it’s stored, and the full conditional flow.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/AuthController.php`
  - `app/Http/Controllers/OTPController.php`
  - `app/Http/Controllers/UserController.php` (password-change OTP flow)
- Service
  - `app/Services/OtpService.php`
- Models
  - `app/Models/UserOtp.php`
- Mail
  - `app/Mail/OtpMail.php`
- Routes
  - `routes/web.php` (auth + otp routes)

## What OTP Means Here

- OTP = One-Time Password (6-digit code).
- Used for:
  - Email verification after registration.
  - Password-change verification (profile flow).

## Storage Model

- OTPs are stored in `user_otps` (one row per `user_id` + `purpose`).
- Stored fields include:
  - `user_id`
  - `purpose`
  - `otp` (hashed)
  - `otp_expires_at`
- Legacy support:
  - If an old OTP exists in `users.otp`, `OtpService::migrateLegacyOtp()` moves it to `user_otps`.

## OTP Purposes

From `OtpService`:

- `PURPOSE_EMAIL_VERIFICATION = email_verification`
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

### Registration → Email OTP

1. User submits registration.
2. System validates inputs and creates user as `pending` with `email_verified_at = null`.
3. System issues OTP for `email_verification`.
4. System stores verification context in session `verify_email`.
5. User is redirected to the OTP page.

### Verify Email OTP

1. User submits the 6-digit OTP.
2. System reads email from request or session.
3. System migrates legacy OTP (if needed).
4. System validates OTP via `OtpService`.
5. If valid:
   - Set `email_verified_at = now()`.
   - Clear OTP for email verification.
   - Clear session `verify_email`.
   - Redirect to waiting approval page.

### Resend Email OTP

1. User submits email.
2. System checks:
   - Email is present and valid.
   - User exists.
   - Email is not already verified.
3. System issues a new `email_verification` OTP.
4. System sets session `verify_email` and redirects to OTP page.

### Password Change OTP (Profile)

1. Logged-in user requests password change.
2. System issues OTP for `password_change`.
3. User submits OTP to confirm password change.
4. System validates OTP using the same `OtpService` rules.

## Security Notes

- OTP is never stored in plaintext.
- OTP is purpose-specific (email verification vs password change).
- OTP is one-time use (cleared after success).
- OTP expiry is enforced by timestamp check.
