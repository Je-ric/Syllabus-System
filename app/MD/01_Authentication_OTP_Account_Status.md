# Authentication, OTP, and Account Status Rules

This document summarizes the implemented conditions for authentication and OTP verification.

## Source Controllers

- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/OTPController.php`
- `app/Http/Controllers/AccountApprovalController.php`

## Registration Conditions

- Name is required.
- Phone number is required.
- Office is required.
- Email is required, must be valid format, and must be unique.
- Allowed email domains:
- `@clsu.edu.ph`
- `@clsu2.edu.ph`
- Password is required, minimum 6 chars, and must match confirmation.

## Registration Behavior

- New users are created with:
- `account_status = pending`
- `email_verified_at = null`
- OTP hash stored in `users.otp`
- OTP expiry set to `now + 10 minutes`
- OTP is sent by email (`OtpMail`).
- Verification email is stored in session (`verify_email`) for OTP flow.

## Login Conditions and Flow

- Credentials must be valid email + password.
- Session is regenerated after successful credentials.
- If email is not verified:
- User is logged out immediately.
- Session is invalidated and token regenerated.
- Login is blocked with verification message.

## Account Status Gate on Login

- `active`: user can proceed to syllabus area.
- `pending`: redirected to waiting approval page.
- `rejected`: login blocked, user logged out.
- `disabled`: login blocked, user logged out.
- Any unknown status: login blocked, user logged out.

## OTP Verification Conditions

- OTP input must be exactly 6 digits.
- Email to verify comes from request email or session `verify_email`.
- If no email context, user is redirected back to login.
- OTP must exist (not null).
- OTP must not be expired.
- OTP must match hash (`Hash::check`).

## OTP Success Behavior

- `email_verified_at` is set to current timestamp.
- `otp` and `otp_expires_at` are cleared.
- Session key `verify_email` is removed.
- User is redirected to waiting approval page.

## OTP Resend Conditions

- Email is required and must be valid.
- Email must exist in users table.
- Email must not already be verified.
- New OTP is generated and expires in 10 minutes.

## Admin Account Status Operations

From `AccountApprovalController`:

- Approve: sets `account_status = active`.
- Reject: sets `account_status = rejected`.
- Restore: sets `account_status = pending`.
- Disable: sets `account_status = disabled`.
- Status update emails are sent for approve/reject/disable.

## Role Assignment Conditions (Account Approval)

- Roles can be assigned only if account status is `active`.
- Allowed input roles: `admin`, `chair`, `dean`, `faculty`.
- `faculty` is always forced into assigned roles.
- If `dean` role is removed, all dean assignments are deleted.
- If `chair` role is removed, all chair assignments are deleted.

