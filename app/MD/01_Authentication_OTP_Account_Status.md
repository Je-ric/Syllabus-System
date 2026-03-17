# Authentication, OTP, and Account Status

Beginner-friendly summary of what happens in registration, verification (OTP), login, and admin approval.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/AuthController.php`
  - `app/Http/Controllers/OTPController.php`
  - `app/Http/Controllers/AccountApprovalController.php`
- Service
  - `app/Services/OtpService.php`
- Models
  - `app/Models/User.php`
  - `app/Models/UserOtp.php`
- Mail
  - `app/Mail/OtpMail.php`
  - `app/Mail/AccountStatusUpdated.php`
- Routes
  - `routes/web.php` (auth, otp, approval routes)

Related docs:
- `app/MD/08_OTP_Flow_and_Service.md`
- `app/MD/09_Roles_Assignment.md`

## Conditions (If / Then)

### Registration (Validation)

- If registering:
  - Then `name` is required.
  - Then `phone_number` is required.
  - Then `office` is required.
  - Then `email` is required, valid, and unique in `users`.
  - Then email must end with `@clsu.edu.ph` or `@clsu2.edu.ph`.
  - Then `password` is required, minimum 6 chars, and must match confirmation.

### Registration (Behavior)

- If registration succeeds:
  - Then create user with:
    - `account_status = pending`
    - `email_verified_at = null`
  - Then issue OTP via `OtpService` for purpose `email_verification`.
  - Then store OTP in `user_otps` hashed (not plaintext).
  - Then set OTP expiry (default 10 minutes).
  - Then save verification context in session `verify_email`.
  - Then redirect to `otp.show`.

### Login (Conditional Flow)

- If credentials are invalid:
  - Then login is rejected.
- If credentials are valid:
  - Then session is regenerated.
  - If `email_verified_at` is empty:
    - Then user is logged out immediately.
    - Then session is invalidated + token regenerated.
    - Then login is blocked with verification error.
  - If email is verified:
    - Then account status is checked:
      - If `active`:
        - Then redirect to `syllabus.index`.
      - If `pending`:
        - Then redirect to `waiting.approval`.
      - If `rejected`:
        - Then logout and block login.
      - If `disabled`:
        - Then logout and block login.
      - If unknown status:
        - Then logout and block login.

### OTP Screen Access

- If route `otp.show` is opened:
  - Then it requires `session('verify_email')`.
  - If session key is missing:
    - Then redirect to `auth.show`.

### OTP Verification (Conditions)

- If verifying OTP:
  - Then `otp` must be exactly 6 digits.
  - Then email source is `request('email')` or `session('verify_email')`.
  - If no email is available:
    - Then redirect to login with error.
  - Then user must exist for that email.
  - Then `OtpService::migrateLegacyOtp()` runs first (backward compatibility).
  - Then OTP record must exist in `user_otps`.
  - Then OTP must not be expired.
  - Then OTP hash must match.

### OTP Verification (Success Behavior)

- If OTP is valid:
  - Then set `email_verified_at = now()`.
  - Then delete OTP record for `email_verification` purpose.
  - Then remove session key `verify_email`.
  - Then redirect to `waiting.approval`.

### OTP Resend

- If resending OTP:
  - Then email is required and valid.
  - Then user must exist.
  - Then email must not already be verified.
  - If all checks pass:
    - Then issue a fresh OTP via `OtpService`.
    - Then set session `verify_email`.
    - Then redirect to `otp.show`.

### Admin Account Status Actions

- If `approve`:
  - Then set user to `active`.
  - Then ensure `faculty` role exists.
  - Then send status email.
- If `reject`:
  - Then set user to `rejected` and send status email.
- If `restore`:
  - Then set user to `pending`.
- If `disable`:
  - Then set user to `disabled` and send status email.

### Role Assignment (Admin)

- If assigning roles:
  - Then user account must be `active`.
  - Then allowed roles are: `admin`, `chair`, `dean`, `faculty`.
  - Then `faculty` is always forced into role set.
  - Then roles are synced (unselected roles removed).
  - If `dean` is removed:
    - Then dean assignments are deleted.
  - If `chair` is removed:
    - Then chair assignments are deleted.

## Sequences (Typical Flow)

### New User Lifecycle

1. User registers.
2. System issues OTP for email verification.
3. User verifies OTP → email becomes verified.
4. User waits for admin approval (account still `pending`).
5. Admin approves user → account becomes `active`.
6. Admin assigns roles and organizational assignments.
7. User can log in and use the system.
