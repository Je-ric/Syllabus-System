# Authentication, OTP, and Account Status

Beginner-friendly summary of what happens in registration, verification (OTP), login, and admin approval.

## Why OTP + Admin Approval (Design Rationale)

### Why OTP Email Verification?
- Registration is restricted to `@clsu.edu.ph` and `@clsu2.edu.ph` only.
- OTP confirms the registrant actually owns that CLSU email address.
- Without OTP, anyone who knows the email format could register with a fake CLSU email.
- OTP expires in 10 minutes. Users can resend from the OTP page or the resend page.
- If mail sending fails, the OTP record is still saved in DB — user can resend manually.

### Why Admin Approval After OTP?
- Verifying the email only proves ownership of a CLSU email — not faculty status.
- CLSU students also have `@clsu.edu.ph` emails. Email verification alone cannot distinguish faculty from students.
- Admin approval is the human gate that confirms the registrant is an actual faculty member.
- Only after admin approval does the account become `active` and receive the `Faculty` role.
- This two-step process (OTP → Admin Approval) ensures only verified, authorized faculty access the system.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/AuthController.php`
  - `app/Http/Controllers/OTPController.php`
  - `app/Http/Controllers/AccountApprovalController.php`
  - `app/Http/Controllers/UserController.php`
- Service
  - `app/Services/AccountApprovalService.php`
  - `app/Services/OtpService.php`
- Models
  - `app/Models/User.php`
  - `app/Models/UserOtp.php`
  - `app/Models/UserAssignment.php`
- Mail
  - `app/Mail/OtpMail.php`
  - `app/Mail/AccountStatusUpdated.php`
- Views
  - `resources/views/Authentication/auth.blade.php` — Login + Register (single page, Alpine tab switch)
  - `resources/views/Authentication/verifyOTP.blade.php` — OTP entry screen
  - `resources/views/Authentication/resendOTP.blade.php` — Resend OTP by email
  - `resources/views/Authentication/waiting-approval.blade.php` — Post-verification holding screen
  - `resources/views/Authentication/viewDetails.blade.php` — User profile + password change
- CSS
  - `resources/css/auth.css` — `.auth-input`, `.auth-primary`, `.auth-secondary`, `.green-grad`
- Routes
  - `routes/web.php` (auth, otp, approval routes)

Related docs:
- `app/MD/08_OTP_Flow_and_Service.md`
- `app/MD/09_Roles_Assignment.md`
- `app/MD/15_User_Profile.md`

## UI Notes

### auth.blade.php
- Single page with Alpine `mode` toggle between `login` and `register`.
- `_mode` hidden input preserves which tab was active on validation error redirect.
- All password fields have a show/hide toggle (Alpine `show` flag, `bx-show`/`bx-hide` icon).
- Right panel displays the 4-step onboarding flow and an RBAC summary card.
- Uses `.auth-input`, `.auth-secondary` (login button), `.auth-primary` (register button), `.green-grad` (right panel).

### verifyOTP.blade.php
- Standalone page (no layout extension) matching the auth card style.
- Shows the destination email from `session('verify_email')`.
- OTP input: centered, large tracking, `autocomplete="one-time-code"`.
- Inline resend form + "use a different email" link.

### resendOTP.blade.php
- Standalone page for users who lost their OTP email.
- Single email field; only works if email is not yet verified.

### waiting-approval.blade.php
- Shown after successful OTP verification.
- Displays a 3-step "what happens next" list.
- Explains that approved accounts receive the Faculty role by default.

### viewDetails.blade.php (password fields)
- All three password fields (current, new, confirm) have Alpine show/hide toggles.
- Uses `x-bind:type` to switch between `password` and `text`.

## Conditions (If / Then)

### Registration (Validation)

- If registering:
  - Then `name` is required.
  - Then `phone_number` is required.
  - Then `office` is required.
  - Then `email` is required, valid, and unique in `users`.
  - Then email must end with `@clsu.edu.ph` or `@clsu2.edu.ph`.
  - Then `password` is required, minimum 8 chars, and must match confirmation.

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
      - If `active`: redirect to `syllabus.index`.
      - If `pending`: redirect to `waiting.approval`.
      - If `rejected`: logout and block login.
      - If `disabled`: logout and block login.
      - If unknown status: logout and block login.

### OTP Screen Access

- If route `otp.show` is opened:
  - Then it requires `session('verify_email')`.
  - If session key is missing: redirect to `auth.show`.

### OTP Verification (Conditions)

- If verifying OTP:
  - Then `otp` must be exactly 6 digits.
  - Then email source is `request('email')` or `session('verify_email')`.
  - If no email is available: redirect to login with error.
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

- If `approve`: set `active`, attach `faculty` role, send email.
- If `reject`: set `rejected`, delete all `user_assignments`, send email.
- If `restore`: set `pending`, no assignment cleanup.
- If `disable`: set `disabled`, delete all `user_assignments`, send email.

### Admin Edit User (AccountApprovalController)

- Acting user must have role `admin`.
- `name` required, max 255.
- `email` required, valid, unique excluding target user's own id.
- `phone_number` optional, max 30.
- `office` optional, max 255.

### User Self-Update (UserController)

- Update always applies to `Auth::id()` only.
- If user has role `admin`: blocked with warning toast.
- `name` required, max 255.
- `email` required, valid, unique excluding own id.
- `phone_number` optional, max 30.
- `office` optional, max 255.

### Role Assignment (Admin)

- User account must be `active`.
- Allowed roles: `admin`, `chair`, `dean`, `faculty`.
- `faculty` is always forced into role set.
- Roles are synced (unselected roles removed).
- If new role set contains both `dean` and `chair`: blocked with 422.
- If `dean` removed: delete all `user_assignments` with `context = dean`.
- If `chair` removed: delete all `user_assignments` with `context = chair`.

## Sequences (Typical Flow)

### New User Lifecycle

1. User registers with CLSU email.
2. System issues OTP for email verification.
3. User verifies OTP → email becomes verified → redirected to waiting-approval page.
4. User waits for admin approval (account still `pending`).
5. Admin approves user → account becomes `active`, `faculty` role attached.
6. Admin assigns additional roles and organizational assignments.
7. User can log in and use the system.

### Disable / Reject Lifecycle

1. Admin disables or rejects a user.
2. System sets account status.
3. System deletes all `user_assignments` for that user.
4. User is removed from all dean/chair/faculty hierarchy positions.
5. Status email is sent.
