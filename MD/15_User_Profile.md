# User Profile

Rules for viewing and updating a user's own profile and password.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/UserController.php`
- Service
  - `app/Services/OtpService.php`
- Models
  - `app/Models/User.php`
  - `app/Models/UserOtp.php`
- Views
  - `resources/views/Authentication/viewDetails.blade.php`
- Routes
  - `routes/web.php` (profile routes — authenticated only)

## UI Notes

### viewDetails.blade.php

- Profile info card: avatar initial, name, role badges, optional "Organizational Hierarchy" button (admin/dean/chair only).
- Assignment details grid: Faculty, Chair, Dean assignments shown with department/college context.
- Admin notice: admins see a warning banner; profile edit and password change are disabled for admin accounts.
- Profile form: name, email, phone, office, email-verified-at (read-only). Disabled for admins.
- Password change (Step 1): current password, new password, confirm — all three fields have Alpine show/hide toggles (`bx-show`/`bx-hide`). Minimum 8 characters.
- Password change (Step 2): shown only when `session('password_change_otp')` exists. OTP input with large tracking. Resend OTP link below.

## Conditions (If / Then)

### View Profile

- If the user opens the profile page:
  - Then load the authenticated user with `roles`, `assignments.department.college`, `assignments.college`.
  - Then display name, email, phone, office, roles, and organizational assignments.

### Update Profile (UserController::update)

- If a user submits a profile update:
  - Then the update always applies to `Auth::id()` only.
  - If the user has role `admin`: blocked with warning toast.
  - If not admin:
    - `name` required, max 255.
    - `email` required, valid, unique excluding own id.
    - `phone_number` optional, max 30.
    - `office` optional, max 255.
    - If valid: update user, redirect with success toast.

### Change Password (UserController::changePassword)

- If a user submits a password change:
  - Then applies to `Auth::id()` only.
  - If admin: blocked with warning toast.
  - If not admin:
    - `current_password` required.
    - `password` required, minimum 8 chars, must be confirmed, must differ from `current_password`.
    - If `current_password` does not match stored hash: error "Current password is incorrect."
    - If valid:
      - Issue OTP for `password_change` via `OtpService`.
      - Store pending password hash in session `password_change_otp`.
      - Redirect to profile with info toast to enter OTP.

### Verify Password OTP (UserController::verifyPasswordOtp)

- If a user submits the OTP to confirm password change:
  - Then check session `password_change_otp` exists and matches `Auth::id()`.
  - If session missing or mismatched: redirect to profile with warning toast.
  - Then migrate legacy OTP if needed.
  - `otp` must be exactly 6 digits.
  - Validate OTP via `OtpService` for `password_change` purpose.
  - If invalid or expired: field error on `otp`.
  - If valid:
    - Apply pending password hash.
    - Clear OTP for `password_change`.
    - Clear session `password_change_otp`.
    - Redirect to profile with success toast.

### Resend Password OTP (UserController::resendPasswordOtp)

- If admin: blocked with warning toast.
- If session `password_change_otp` missing or mismatched: redirect with warning toast.
- If valid: issue new OTP for `password_change`, redirect with success toast.

## Sequences (Typical Flow)

### Update Profile

1. User edits name, email, phone, or office.
2. System validates (email uniqueness excludes own id).
3. System updates the user row.
4. User is redirected to profile page with success toast.

### Change Password

1. User enters current password and new password (all fields have show/hide toggle).
2. System verifies current password hash.
3. System issues OTP and stores pending hash in session.
4. Step 2 panel appears — user enters OTP.
5. System validates OTP.
6. System applies new password hash.
7. OTP and session are cleared.
