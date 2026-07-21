# Authentication, OTP, and Account Status

Beginner-friendly summary of what happens in registration, login, OTP (password changes), and admin approval.

## Why CAIS + Admin Approval (Design Rationale)

### Why CAIS Login?
- The system integrates with the CLSU CAIS (LMS) API for primary authentication.
- On login, CAIS is contacted first to verify credentials.
- If CAIS responds, the user is authenticated via CAIS and a local user row is auto-created if needed.
- If CAIS is unavailable or rejects the credentials, the system falls back to local password authentication.
- This ensures faculty can log in with their existing CLSU credentials.

### Why OTP for Password Changes?
- OTP verifies the user owns their email before allowing a password change.
- OTP is issued via `OtpService` for purpose `password_change`.
- OTP expires in 10 minutes. Users can resend from the profile page.
- If mail sending fails, the OTP record is still saved in DB — user can resend manually.

### Why Admin Approval After Registration?
- Registration creates an account with `pending` status and `email_verified_at = now()` directly.
- Admin approval is the human gate that confirms the registrant is an actual faculty member.
- Only after admin approval does the account become `active` and receive the `Faculty` role.
- This ensures only verified, authorized faculty access the system.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/AuthController.php`
  - `app/Http/Controllers/AccountApprovalController.php`
  - `app/Http/Controllers/UserController.php`
- Services
  - `app/Services/CaisApiService.php`
  - `app/Services/OtpService.php`
  - `app/Services/AccountApprovalService.php`
- Models
  - `app/Models/User.php`
  - `app/Models/UserAssignment.php`
- Mail
  - `app/Mail/AccountStatusUpdated.php`
- Views
  - `resources/views/Authentication/auth.blade.php` — Login + Register (single page, Alpine tab switch)
  - `resources/views/Authentication/waiting-approval.blade.php` — Post-registration holding screen
  - `resources/views/Authentication/viewDetails.blade.php` — User profile + password change
- CSS
  - `resources/css/auth.css` — `.auth-input`, `.auth-primary`, `.auth-secondary`, `.green-grad`
- Routes
  - `routes/web.php` (auth, profile, approval routes)

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
- Uses `.auth-input`, `.auth-secondary` (login button), `.auth-primary` (register button), `.green-grad` (right panel).

### waiting-approval.blade.php
- Shown after successful registration.
- Displays a 3-step "what happens next" list.
- Explains that approved accounts receive the Faculty role by default.

### viewDetails.blade.php (password fields)
- All three password fields (current, new, confirm) have Alpine show/hide toggles.
- Uses `x-bind:type` to switch between `password` and `text`.
- Password change requires OTP verification via email before the update is processed.

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
    - `email_verified_at = now()`
  - Then record an AuditLog entry for the registration.
  - Then redirect to `waiting.approval` with a success message.
  - No OTP is issued for registration.

### Login (CAIS Flow)

- If login is attempted:
  - Then first try CAIS API authentication:
    - If CAIS responds:
      - Then find or create a local user row for that email.
      - If user exists and is not `active`: force `active` status.
      - Then store `cais_token` in session.
      - Then call `syncFromCais()` to update local user profile from CAIS data.
      - Then log in the user via `Auth::login()`.
      - Then record AuditLog entry for CAIS login.
      - Then redirect to `syllabus.index`.
    - If CAIS is unavailable or credentials are rejected:
      - Then fall back to local authentication.
- If local auth fails:
  - Then redirect to `auth.show` with error toast.

### Login (Local Fallback — Conditional Flow)

- If local credentials are invalid:
  - Then login is rejected with error toast.
- If local credentials are valid:
  - Then session is regenerated.
  - If `account_status` is `pending`:
    - Then logout, invalidate session, redirect to `waiting.approval` with info toast.
  - If `account_status` is `rejected`:
    - Then logout, invalidate session, redirect to `auth.show` with error toast.
  - If `account_status` is `disabled`:
    - Then logout, invalidate session, redirect to `auth.show` with error toast.
  - If `account_status` is `active`:
    - Then redirect to `syllabus.index`.

### Password Change (OTP Flow)

- If user requests a password change:
  - Then current password is validated first.
  - If valid:
    - Then issue OTP via `OtpService` for purpose `password_change`.
    - Then send OTP email.
    - Then return an OTP session token (stored in session).
    - Then user must submit OTP via `verifyPasswordOtp()`.
- If verifying OTP:
  - Then `otp` must be exactly 6 digits.
  - Then OTP record must exist for the user.
  - Then OTP must not be expired.
  - Then OTP hash must match.
  - If valid:
    - Then update password.
    - Then clear OTP record.
    - Then redirect with success toast.
- If resending OTP:
  - Then issue a fresh OTP via `OtpService`.
  - Then send a new OTP email.

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

### Consultation Hours (UserController)

- User can manage their own consultation hours via profile.
- `POST /profile/consultation-hours` — store a consultation hour entry.
- `DELETE /profile/consultation-hours/{hour}` — delete a consultation hour entry.
- These are stored on the user record, not per-syllabus.

## Sequences (Typical Flow)

### New User Lifecycle

1. User registers with CLSU email → account created with `pending` status, email auto-verified.
2. User redirected to waiting-approval page.
3. User waits for admin approval (account still `pending`).
4. Admin approves user → account becomes `active`, `faculty` role attached.
5. Admin assigns additional roles and organizational assignments.
6. User can log in and use the system.

### Login (CAIS-first)

1. User submits email + password.
2. System contacts CAIS API.
3. If CAIS responds:
   - User authenticated via CAIS.
   - Local user created or updated from CAIS profile.
   - Redirect to syllabus index.
4. If CAIS unavailable:
   - Fall back to local password check.
   - If valid, check account status → redirect or block accordingly.

### Password Change with OTP

1. User requests password change via profile page.
2. System validates current password.
3. System issues OTP to user's email.
4. User enters OTP on profile page.
5. System verifies OTP → updates password.
6. User can log in with new password.

### Disable / Reject Lifecycle

1. Admin disables or rejects a user.
2. System sets account status.
3. System deletes all `user_assignments` for that user.
4. User is removed from all dean/chair/faculty hierarchy positions.
5. Status email is sent.
