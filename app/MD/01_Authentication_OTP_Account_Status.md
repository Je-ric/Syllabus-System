# Authentication, OTP, and Account Status Rules

Beginner-friendly summary of what currently happens in login, registration, verification, and admin approval.

## Source of Truth

- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/OTPController.php`
- `app/Http/Controllers/AccountApprovalController.php`
- `app/Services/OtpService.php`
- `app/Models/UserOtp.php`

## Registration: Conditions

- `name` is required.
- `phone_number` is required.
- `office` is required.
- `email` is required, valid, and unique in `users`.
- Email must end with `@clsu.edu.ph` or `@clsu2.edu.ph`.
- `password` is required, minimum 6 chars, and must match confirmation.

## Registration: Behavior

- A new user is created with:
- `account_status = pending`
- `email_verified_at = null`
- OTP is issued using `OtpService` for purpose `email_verification`.
- OTP is stored in `user_otps` (hashed), not as plain text.
- OTP expiry defaults to `10 minutes`.
- Verification context is saved in session key `verify_email`.
- User is redirected to `otp.show`.

## Login: Conditional Flow

- If credentials are invalid, login is rejected.
- If credentials are valid, session is regenerated.
- If `email_verified_at` is empty:
- User is logged out immediately.
- Session is invalidated + token regenerated.
- Login is blocked with verification error.
- If email is verified, account status is checked:
- If `active`, redirect to `syllabus.index`.
- If `pending`, redirect to `waiting.approval`.
- If `rejected`, logout and block login.
- If `disabled`, logout and block login.
- If unknown status, logout and block login.

## OTP Screen Access Rule

- `otp.show` requires `session('verify_email')`.
- If missing, user is redirected to `auth.show`.

## OTP Verification: Conditions

- `otp` must be exactly 6 digits.
- Email source is `request('email')` or `session('verify_email')`.
- If no email is available, redirect to login with error.
- User must exist for the selected email.
- `OtpService::migrateLegacyOtp()` is called first (for backward compatibility from old `users.otp` data).
- OTP record must exist in `user_otps`.
- OTP must not be expired.
- OTP hash must match the entered OTP.

## OTP Verification: Success Behavior

- `email_verified_at` is set to `now()`.
- OTP record for `email_verification` purpose is deleted.
- Session key `verify_email` is removed.
- User is redirected to `waiting.approval`.

## OTP Resend: Conditions and Behavior

- Email is required and must be valid.
- User with that email must exist.
- Email must not already be verified.
- If all checks pass, a fresh OTP is issued via `OtpService`.
- Session `verify_email` is set and user is redirected to `otp.show`.

## Admin Account Status Actions

- `approve`: sets user to `active`, ensures `faculty` role exists, sends status email.
- `reject`: sets user to `rejected`, sends status email.
- `restore`: sets user to `pending`.
- `disable`: sets user to `disabled`, sends status email.

## Role Assignment Rules (Admin)

- Roles can be assigned only when account is `active`.
- Allowed roles: `admin`, `chair`, `dean`, `faculty`.
- `faculty` is always forced into role set.
- Roles are synced (unselected roles are removed).
- If `dean` removed, dean assignments are deleted.
- If `chair` removed, chair assignments are deleted.
