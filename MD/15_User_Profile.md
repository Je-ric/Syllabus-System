# User Profile

Rules for viewing and updating a user's own profile, managing consultation hours, and changing password.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/UserManagement/UserController.php` — index, update, changePassword, verifyPasswordOtp, resendPasswordOtp, storeConsultationHour, destroyConsultationHour
- Service
  - `app/Services/Authentication/OtpService.php` — issueForUser, validate, clear, migrateLegacyOtp
- Models
  - `app/Models/User.php`
  - `app/Models/UserOtp.php`
  - `app/Models/UserConsultationHour.php`
  - `app/Models/AuditLog.php` (recent activity)
- Views
  - `resources/views/Authentication/viewDetails.blade.php`
- Routes
  - `routes/web.php` (profile routes — authenticated only)

## UI Layout

Two-column layout:
- **Left**: Profile card (avatar, name, office, role badges, email verified badge, contact info, assignments, action buttons).
- **Right**: Profile information form, consultation hours, recent activity, password change.
- **Security**: All profile operations are self-only (user can only modify their own profile), with admin restrictions on certain actions.

## Security Implementation

### Authorization
- **Self-Only Access**: All profile operations are restricted to the authenticated user (`Auth::id()`) only.
- **Role-Based Restrictions**: Admin users are blocked from profile editing and password changes for security reasons.
- **Ownership Validation**: Consultation hours operations verify ownership (403 abort if not owner).

### Input Validation
- **Profile Update Validation**:
  - Name: Required, max 255 chars, letters/spaces only, no injection attempts
  - Email: Required, valid email format, unique excluding own id, no injection attempts
  - Phone: Optional, max 30 chars, phone format only (digits, spaces, standard phone characters)
  - Office: Optional, max 255 chars, letters/numbers/spaces/basic punctuation only, no injection attempts
- **Password Validation**: Minimum 8 chars, must include uppercase, lowercase, and number, must differ from current password
- **Consultation Hours Validation**: Day restricted to Monday-Friday, time max 100 chars.

### OTP Security
- **Double Verification**: Password changes require OTP verification via email before committing changes.
- **Session-Based Security**: Pending password changes stored in session with user_id verification.
- **OTP Validation**: 6-digit OTP with 10-minute expiry, validated against hashed value.
- **Session Mismatch Protection**: Verifies session data matches authenticated user before processing.
- **Legacy Migration**: Support for migrating OTP from legacy columns to secure OTP table.

### Transaction Safety
- **Atomic Operations**: Password changes occur atomically after OTP validation.
- **Session Cleanup**: OTP records and session keys are cleared immediately after successful password change.

### Audit Logging
- **Comprehensive Logging**: All profile updates and password changes record AuditLog entries.
- **Recent Activity**: Last 20 audit log entries displayed to user for transparency.

### Rate Limiting
- **Current Status**: Rate limiting is not currently implemented on profile endpoints.
- **Recommended Enhancement**: Add rate limiting to password change OTP verification to prevent OTP brute force attempts.

## Conditions (If / Then)

### View Profile

- If the user opens the profile page:
  - Then load the authenticated user with `roles`, `assignments.department.college`, `assignments.college`, `consultationHours`.
  - Then load the last 20 audit log entries for that user (ordered by timestamp DESC).
  - Then display name, email, phone, office, roles, assignments, consultation hours, and recent activity.

- If user is admin, dean, or chair:
  - Then show "Organizational Hierarchy" button linking to `route('organizational.hierarchy')`.

### Update Profile (UserController::update)

- If a user submits a profile update:
  - Then the update always applies to `Auth::id()` only.
  - If the user has role `admin`: blocked with warning toast ("Admin profile details cannot be edited here.").
  - If not admin:
    - `name` required, max 255 chars, letters/spaces only, no injection attempts.
    - `email` required, valid email, unique excluding own id, no injection attempts.
    - `phone_number` optional, max 30 chars, phone format only (digits, spaces, standard phone characters).
    - `office` optional, max 255 chars, letters/numbers/spaces/basic punctuation only, no injection attempts.
    - If valid: update user, redirect with success toast.

### Consultation Hours Management

- **Add** (`storeConsultationHour`):
  - `day` required, must be `Monday`–`Friday`.
  - `time` required, max 100 chars.
  - Creates a `UserConsultationHour` record.
  - Redirects back with success toast.

- **Remove** (`destroyConsultationHour`):
  - Verifies ownership (abort 403 if not owner).
  - Deletes the record.

### Change Password (UserController::changePassword)

- If a user submits a password change:
  - If admin: blocked with warning toast.
  - If not admin:
    - `current_password` required, validated against stored hash.
    - `password` required, min 8 chars, must be confirmed (`password_confirmation`), must differ from `current_password`, complex requirements (uppercase, lowercase, number).
    - If `current_password` does not match stored hash: field error "Current password is incorrect."
    - If valid:
      - Issue OTP for `password_change` via `Authentication\OtpService::issueForUser()`.
      - Store pending user_id + password hash in session under `password_change_otp`.
      - Redirect to profile with info toast asking user to enter OTP.

### Verify Password OTP (UserController::verifyPasswordOtp)

- If a user submits the OTP to confirm password change:
  - Then check session `password_change_otp` exists and matches `Auth::id()`.
  - If session missing or mismatched: redirect with warning toast ("No pending password change request found.").
  - Then migrate legacy OTP if needed (`Authentication\OtpService::migrateLegacyOtp()`).
  - `otp` must be exactly 6 digits.
  - Validate OTP via `Authentication\OtpService::validate()` for `password_change` purpose.
  - If invalid or expired: field error on `otp`.
  - If valid:
    - Apply pending password hash via `forceFill`.
    - Clear OTP for `password_change` via `Authentication\OtpService::clear()`.
    - Clear session `password_change_otp`.
    - Redirect with success toast.

### Resend Password OTP (UserController::resendPasswordOtp)

- If admin: blocked with warning toast.
- If session `password_change_otp` missing or mismatched: redirect with warning toast.
- If valid: issue new OTP for `password_change`, redirect with success toast.

## OTP Service (OtpService)

Used for password change flow.

Constants:
- `PURPOSE_PASSWORD_CHANGE = 'password_change'`

Default expiry: 10 minutes.

Methods:
- `issueForUser(User, purpose, expiryMinutes)` — generates 6-digit OTP, hashes it, stores in `user_otps`, sends email via `OtpMail`. Returns `bool` (whether email was sent successfully).
- `validate(User, otp, purpose)` — checks existence, expiry, hash match. Returns `null` on success or error string.
- `clear(User, purpose)` — deletes OTP record.
- `migrateLegacyOtp(User, purpose)` — migrates OTP from legacy `users.otp` column to `user_otps` table, then clears legacy fields.

**Security Features**:
- **Hashed Storage**: OTPs are stored as hashes, not plaintext, to prevent database compromise exposure.
- **Purpose-Based Isolation**: OTPs are isolated by purpose (password_change) to prevent cross-purpose replay attacks.
- **Time-Based Expiry**: 10-minute expiry limits window for OTP brute force attempts.
- **Session Binding**: Password changes require both OTP validation and session data matching.

## UI Notes (viewDetails.blade.php)

### Profile Card (Left)
- Gradient green banner top.
- Avatar showing first letter of name.
- Name, office, role badges (`status-indicator` component).
- Email verified badge (green/amber based on `email_verified_at`).
- Info list: email, phone, joined date, account status.
- Assignment details section: Dean/Chair/Faculty assignments listed with department/college context.
- "Organizational Hierarchy" button (admin/dean/chair only).

### Profile Information Form
- 2-column grid: name, email, phone, office, email verified (read-only).
- Admin notice banner: "Admin profile editing is disabled on this page."
- All input fields disabled for admin accounts.

### Consultation Hours
- List of existing hours showing day abbreviation, day name, time range.
- Delete button per row with confirmation.
- "Add" button opens modal dialog with day select and time input.
- Empty state: "No consultation hours added yet."

### Recent Activity
- Last 20 audit log entries in a scrollable container.
- Each entry shows: action icon (color-coded), action text with description, module name, timestamp.
- Icons mapped for: login, logout, register, otp_verify, profile_update, password_change.

### Change Password (Security)
- **Step 1** — Set New Password:
  - Three fields: Current Password, New Password, Confirm New Password.
  - Each field has Alpine show/hide toggle (`bx-show`/`bx-hide`).
  - Submit button: "Send OTP".
  - Description text explaining the OTP flow.
- **Step 2** — Enter Verification Code:
  - Shown only when session `password_change_otp` exists.
  - OTP input with large tracking (`tracking-[0.4em]`), max 6 chars.
  - "Confirm Password Change" button.
  - "Resend OTP" link below.
  - Green panel to distinguish from Step 1.

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
