# Authentication, OTP, and Account Status

Beginner-friendly summary of what happens in registration, login, OTP (password changes), and admin approval.

## Why CAIS + Admin Approval (Design Rationale)

### Why CAIS Login?
- The system integrates with the CLSU CAIS (LMS) API for primary authentication.
- On login, CAIS is contacted first to verify credentials.
- If CAIS responds, the user is authenticated via CAIS and a local user row is auto-created if needed.
- If CAIS is unavailable or rejects the credentials, the system falls back to local password authentication.
- This ensures faculty can log in with their existing CLSU credentials without a separate password.

### Why OTP for Password Changes?
- OTP verifies the user owns their email before allowing a password change.
- OTP is issued via `OtpService` for purpose `password_change`.
- OTP expires in 10 minutes. Users can resend from the profile page.
- If mail sending fails, the OTP record is still saved in DB — user can resend manually.

### Why Admin Approval After Registration?
- Registration creates an account with `active` status and `email_verified_at = now()` directly.
- Admin approval is the human gate that confirms the registrant is an actual faculty member.
- After admin approval, the account receives the `Faculty` role.
- This ensures only verified, authorized faculty access the system.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/Authentication/AuthController.php` — Login, register, logout
  - `app/Http/Controllers/Authentication/AccountApprovalController.php` — Admin account approval, role assignment, user editing
  - `app/Http/Controllers/UserManagement/UserController.php` — User profile updates, consultation hours
  - `app/Http/Controllers/UserManagement/UserAssignmentsController.php` — User assignments management
- Services
  - `app/Services/CaisApiService.php`
  - `app/Services/OtpService.php`
  - `app/Services/AccountApprovalService.php`
- Models
  - `app/Models/User.php`
  - `app/Models/UserOtp.php`
  - `app/Models/UserAssignment.php`
  - `app/Models/Role.php`
- Mail
  - `app/Mail/AccountStatusUpdated.php`
  - `app/Mail/OtpMail.php`
- Views
  - `resources/views/Authentication/auth.blade.php` — Login + Register (single page, Alpine tab switch)
  - `resources/views/Authentication/waiting-approval.blade.php` — Post-registration holding screen
  - `resources/views/Authentication/viewDetails.blade.php` — User profile + password change
  - `resources/views/AccountApproval/index.blade.php` — Admin account approval page
  - `resources/views/AccountApproval/modals/` — Approval, role assignment, and edit user modals
- Routes
  - `routes/web.php`
    - `GET /auth` — show login/register page
    - `POST /login` — login
    - `POST /register` — register
    - `POST /logout` — logout
    - `GET /waiting-approval` — post-registration holding screen (public)
    - `GET /profile` — view profile (auth)
    - `PUT /profile` — update profile details (auth)
    - `POST /profile/password` — initiate password change (auth)
    - `POST /profile/password/verify-otp` — verify OTP and commit password (auth)
    - `POST /profile/password/resend-otp` — resend OTP (auth)
    - `POST /profile/consultation-hours` — add consultation hour (auth)
    - `DELETE /profile/consultation-hours/{hour}` — remove consultation hour (auth)
  - `routes/web.php` (account approval routes — `role:admin`)
    - `GET /account-approval` — index
    - `POST /account-approval/approve` — approve account
    - `POST /account-approval/reject` — reject account
    - `POST /account-approval/restore` — restore account
    - `POST /account-approval/disable` — disable account
    - `POST /account-approval/edit-user` — edit user details
    - `POST /account-approval/assign-roles` — assign roles

Related docs:
- `MD/08_OTP_Flow_and_Service.md`
- `MD/09_Roles_Assignment.md`
- `MD/15_User_Profile.md`

## UI Notes

### auth.blade.php
- Single page with Alpine `mode` toggle between `login` and `register`.
- `_mode` hidden input preserves which tab was active on validation error redirect.
- All password fields have a show/hide toggle (Alpine `show` flag, `bx-show`/`bx-hide` icon).
- Right panel displays the 4-step onboarding flow and an RBAC summary card.

### waiting-approval.blade.php
- Shown after successful registration.
- Displays a 3-step "what happens next" list.
- Explains that approved accounts receive the Faculty role by default.

### viewDetails.blade.php (password fields)
- All three password fields (current, new, confirm) have Alpine show/hide toggles.
- Uses `x-bind:type` to switch between `password` and `text`.
- Password change requires OTP verification via email before the update is processed.
- Includes consultation hours management (add/remove consultation hours).

### AccountApproval views
- `index.blade.php` — Admin dashboard for account approval and management
- `modals/approvalModal.blade.php` — Approve/reject account modal
- `modals/assignRolesModal.blade.php` — Role assignment modal
- `modals/editUserModal.blade.php` — Edit user details modal

## Conditions (If / Then)

### Registration (Validation)

- If registering:
  - Then `name` is required.
  - Then `phone_number` is required, max 20 chars.
  - Then `office` is required, max 255 chars.
  - Then `email` is required, valid, and unique in `users`.
  - Then email must end with `@clsu.edu.ph` or `@clsu2.edu.ph`.
  - Then `password` is required, minimum 6 chars, and must match confirmation.

### Registration (Behavior)

- If registration succeeds:
  - Then create user with:
    - `account_status = active`
    - `email_verified_at = now()`
    - `phone_number` and `office` from form
  - Then record an AuditLog entry for the registration.
  - Then redirect to `waiting.approval` with a success message.
  - No role is assigned at registration — only after admin approval.
  - No OTP is issued for registration.

### Login (CAIS Flow)

- If login is attempted:
  - Then first try CAIS API authentication via `CaisApiService::verifyUser()`:
    - If CAIS responds with a valid result:
      - Then find or create a local user row for that email.
      - Then store `cais_token` in session.
      - Then call `User::syncFromCais()` to update local profile from CAIS data:
        - Updates `cais_user_id`, `cais_employee_id`, `name` if changed.
        - Forces `account_status = active`.
        - Ensures `faculty` role is attached.
      - Then log in the user via `Auth::login()` with remember-me if checked.
      - Then regenerate session.
      - Then record AuditLog entry for CAIS login.
      - Then redirect to `syllabus.index`.
    - If CAIS is unavailable or credentials are rejected:
      - Then fall back to local authentication.

### Login (Local Fallback)

- If local credentials (email + password hash) do not match:
  - Then redirect to `auth.show` with error toast.
- If credentials match:
  - If `account_status` is `pending`:
    - Then redirect to `waiting.approval` with info toast.
  - If `account_status` is `rejected`:
    - Then redirect to `auth.show` with error toast.
  - If `account_status` is `disabled`:
    - Then redirect to `auth.show` with error toast.
  - If `account_status` is `active`:
    - Then log in, regenerate session, record AuditLog, redirect to `syllabus.index`.

### Logout

- If user logs out:
  - Then record AuditLog entry for the logout.
  - Then call `Auth::logout()`.
  - Then forget `cais_token` from session.
  - Then invalidate and regenerate session token.
  - Then redirect to `auth.show`.

### Password Change (OTP Flow)

- If user requests a password change:
  - If user has role `admin`: blocked with warning toast (admin cannot change password from this page).
  - Then validate current password via `Hash::check()`.
  - Then validate new password: minimum 8 chars, must match confirmation, must be different from current password.
  - If current password is wrong: return field-level error.
  - If valid:
    - Then issue OTP via `OtpService::issueForUser()` for purpose `password_change`.
    - Then store `['user_id', 'password_hash']` in session under `password_change_otp` key.
    - Then redirect to profile with info toast (or warning if mail failed).
- If verifying OTP:
  - Then `otp` must be exactly 6 digits.
  - Then session must have a pending change for this user.
  - Then `OtpService::validate()` checks: record exists, not expired, hash matches.
  - If valid:
    - Then update password to the hashed value stored in session.
    - Then clear OTP record via `OtpService::clear()`.
    - Then clear session key.
    - Then redirect to profile with success toast.
  - If invalid: return field-level error on `otp`.
- If resending OTP:
  - If user has role `admin`: blocked with warning toast.
  - If no pending session change exists: redirect with warning toast.
  - Then issue a fresh OTP via `OtpService::issueForUser()`.
  - Then redirect to profile with success or warning toast.

### Admin Account Status Actions

| Action | Status set | Assignments cleared | Email sent |
|---|---|---|---|
| `approve` | `active` | — | Yes (AccountStatusUpdated) |
| `reject` | `rejected` | All `user_assignments` deleted | Yes |
| `restore` | `pending` | — | No |
| `disable` | `disabled` | All `user_assignments` deleted | Yes |

- On `approve`: `faculty` role is attached if not already present.
- On `approve`: `email_verified_at` is set to `now()` if null.

### Admin Edit User (AccountApprovalController — Authentication)

- Acting user must have role `admin` (checked explicitly, not just route middleware).
- `name` required, max 255.
- `email` required, valid, unique excluding target user's own id.
- `phone_number` optional, max 30.
- `office` optional, max 255.
- Records an AuditLog entry after update.

### User Profile Update (UserController — UserManagement)

- User cannot have role `admin` (blocked with warning toast).
- `name` required, max 255.
- `email` required, valid, unique excluding user's own id.
- `phone_number` optional, max 30.
- `office` optional, max 255.
- Records an AuditLog entry after update.

### Consultation Hours (UserController — UserManagement)

- User can add consultation hours:
  - `day` required, must be one of: Monday, Tuesday, Wednesday, Thursday, Friday.
  - `time` required, max 100 chars.
- User can delete their own consultation hours (aborts if hour doesn't belong to user).

### Role Assignment (Admin — AccountApprovalController — Authentication)

- User account must be `active`.
- Allowed roles: `admin`, `chair`, `dean`, `faculty`, `ovpaa`.
- `faculty` is always forced into the role set (cannot be removed via this action).
- Roles are synced — roles not in the submitted set are removed.
- If submitted set contains both `dean` and `chair`: blocked with error toast (controller-level guard) and 422 (service-level guard).
- If `dean` role removed: all `user_assignments` with `context = dean` are deleted.
- If `chair` role removed: all `user_assignments` with `context = chair` are deleted.
- If `faculty` role removed: all `user_assignments` with `context = faculty` are deleted.
- Notifications sent via `SystemRoleChangedNotification` for roles added or removed (admin role excluded).

## Sequences (Typical Flow)

### New User Lifecycle

1. User registers with CLSU email → account created with `active` status, email auto-verified.
2. User redirected to waiting-approval page.
3. Admin approves user → `faculty` role attached, approval email sent.
4. Admin assigns additional roles (including `ovpaa`) and organizational assignments as needed.
5. User can log in and use the system.

### Login (CAIS-first)

1. User submits email + password.
2. System contacts CAIS API via `CaisApiService::verifyUser()`.
3. If CAIS responds: user authenticated, local profile synced, redirect to syllabus index.
4. If CAIS unavailable: fall back to local password check → check account status → redirect or block.

### Password Change with OTP

1. User navigates to profile page and fills password change form.
2. System validates current password.
3. System issues OTP to user's email and stores pending hash in session.
4. User enters 6-digit OTP on profile page.
5. System verifies OTP → updates password → clears OTP record and session key.
6. User can log in with new password.

### Disable / Reject Lifecycle

1. Admin disables or rejects a user via Account Approval page.
2. System sets account status.
3. System deletes all `user_assignments` for that user.
4. User is removed from all dean/chair/faculty hierarchy positions.
5. Status email sent via `AccountStatusUpdated` mailable.
