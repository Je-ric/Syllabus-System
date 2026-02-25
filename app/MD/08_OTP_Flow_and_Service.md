# OTP Flow and Service (Beginner-Friendly)

This document focuses only on OTP in CSMS: what it is, where it is saved, and all conditional rules.

## Source of Truth

- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/OTPController.php`
- `app/Http/Controllers/UserController.php` (password-change OTP flow)
- `app/Services/OtpService.php`
- `app/Models/UserOtp.php`
- `app/Mail/OtpMail.php`

## What OTP Means Here

- OTP = One-Time Password (6-digit code).
- It is used for:
- Email verification after registration.
- Password-change verification (for logged-in profile flow).

## Storage Model

- OTP data is stored in `user_otps` table (one row per user + purpose).
- Stored fields include:
- `user_id`
- `purpose`
- `otp` (hashed)
- `otp_expires_at`
- Legacy support:
- If old OTP exists in `users.otp`, `OtpService::migrateLegacyOtp()` moves it to `user_otps`.

## OTP Purposes (Constants)

From `OtpService`:

- `PURPOSE_EMAIL_VERIFICATION = email_verification`
- `PURPOSE_PASSWORD_CHANGE = password_change`

## OTP Issuing Rules

When `issueForUser($user, $purpose)` is called:

- A random 6-digit code is generated.
- OTP is hashed before saving.
- Existing OTP for same `(user_id, purpose)` is replaced (`updateOrCreate`).
- Expiry is set to now + 10 minutes by default.
- Email is sent using `OtpMail`.

## OTP Validation Rules

When `validate($user, $otp, $purpose)` is called:

- If no record exists for user+purpose:
- Return error: invalid or already used.
- If record exists but expired:
- Return error: expired.
- If hash does not match:
- Return error: invalid OTP.
- If all checks pass:
- Return `null` (means valid).

## OTP Clear Rule

- `clear($user, $purpose)` deletes OTP rows for that user and purpose.
- This is called after successful verification.

## Registration OTP Flow

1. User submits registration.
2. If validation passes, user is created as `pending` + unverified email.
3. OTP is issued for `email_verification`.
4. Session stores `verify_email`.
5. User is redirected to OTP page.

## Verify Email OTP Flow

1. User submits 6-digit OTP.
2. System reads email from request or session.
3. System migrates legacy OTP (if needed).
4. System validates OTP via `OtpService`.
5. If valid:
- user email marked verified (`email_verified_at = now()`),
- OTP cleared for email verification,
- session `verify_email` cleared,
- redirected to waiting approval page.

## Resend Email OTP Flow

1. User submits email.
2. If email missing/invalid, fail.
3. If user not found, fail.
4. If already verified, fail.
5. Otherwise issue new email-verification OTP and redirect to OTP page.

## Password Change OTP Flow (Profile)

- Logged-in user requests password change.
- OTP is issued using `password_change` purpose.
- User submits OTP to confirm password change.
- Verification is purpose-specific, so email-verification OTP cannot be reused here.

## Security Notes

- OTP is never stored in plaintext in DB.
- OTP expires automatically by timestamp check.
- OTP is one-purpose-at-a-time (email verification vs password change).
- OTP is one-time use (record deleted after successful verification).
