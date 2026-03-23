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

## Conditions (If / Then)

### View Profile

- If the user opens the profile page:
  - Then load the authenticated user with `roles`, `assignments.department.college`, `assignments.college`.
  - Then display name, email, phone, office, roles, and organizational assignments.

### Update Profile (UserController::update)

- If a user submits a profile update:
  - Then the update always applies to `Auth::id()` only (cannot update another user's profile here).
  - If the user has role `admin`:
    - Then update is blocked with a warning toast.
    - Then admin profiles are not editable through this endpoint.
  - If the user is not admin:
    - Then `name` is required, max 255.
    - Then `email` is required, valid email, max 255, unique in `users` excluding own id.
    - Then `phone_number` is optional, max 30.
    - Then `office` is optional, max 255.
    - If all validations pass:
      - Then update the user's profile fields.
      - Then redirect to profile page with success toast.

### Change Password (UserController::changePassword)

- If a user submits a password change:
  - Then the change always applies to `Auth::id()` only.
  - If the user has role `admin`:
    - Then change is blocked with a warning toast.
  - If the user is not admin:
    - Then `current_password` is required.
    - Then `password` is required, minimum 8 chars, must be confirmed, must differ from `current_password`.
    - If `current_password` does not match the stored hash:
      - Then return error: "Current password is incorrect."
    - If all checks pass:
      - Then issue OTP for `password_change` purpose via `OtpService`.
      - Then store pending password hash in session `password_change_otp`.
      - Then redirect to profile page with info toast to enter OTP.

### Verify Password OTP (UserController::verifyPasswordOtp)

- If a user submits the OTP to confirm password change:
  - Then check session `password_change_otp` exists and matches `Auth::id()`.
  - If session is missing or mismatched:
    - Then redirect to profile with warning toast.
  - Then migrate legacy OTP if needed.
  - Then `otp` must be exactly 6 digits.
  - Then validate OTP via `OtpService` for `password_change` purpose.
  - If OTP is invalid or expired:
    - Then return field error on `otp`.
  - If OTP is valid:
    - Then apply the pending password hash to the user.
    - Then clear OTP for `password_change` purpose.
    - Then clear session `password_change_otp`.
    - Then redirect to profile with success toast.

### Resend Password OTP (UserController::resendPasswordOtp)

- If a user requests OTP resend:
  - If the user has role `admin`:
    - Then blocked with warning toast.
  - If session `password_change_otp` is missing or mismatched:
    - Then redirect to profile with warning toast.
  - If all checks pass:
    - Then issue a new OTP for `password_change` purpose.
    - Then redirect to profile with success toast.

## Sequences (Typical Flow)

### Update Profile

1. User edits name, email, phone, or office.
2. System validates (email uniqueness excludes own id).
3. System updates the user row.
4. User is redirected to profile page with success toast.

### Change Password

1. User enters current password and new password.
2. System verifies current password hash.
3. System issues OTP and stores pending hash in session.
4. User enters OTP.
5. System validates OTP.
6. System applies new password hash.
7. OTP and session are cleared.
