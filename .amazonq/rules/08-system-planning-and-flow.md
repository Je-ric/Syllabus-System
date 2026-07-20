# System Planning & Flow

## Foundation / General Principles

- Make it as simple as possible.
- Know the problem first before writing any code.
- Always document conditions, sequence, and approach in an MD before coding.
- User-friendly — design for the least technical user (idiot proof).

## Planning Reminders

- List all possible considerations and factors before starting (incidents, edge cases, tools, roles).
- Define who can do what before building any feature.
- Identify what needs to be logged, notified, and validated upfront.
- Sketch the system flow: login → dashboard → action → audit log → notification.

## New Feature Planning Template

Before building any feature, answer these:

1. Who can access this feature? (roles)
2. What are the conditions to use it? (prerequisites)
3. What does it create/update/delete in the DB?
4. What needs to be validated?
5. What needs to be logged?
6. Who gets notified?
7. What happens on failure?
8. What does the user see on success?

---

## Core System Features (Must Have)

- Index — default landing page
- Search
- Filter / Sort
- Pagination
- Auto-refresh (AJAX / Livewire / polling)
- Session checks — always verify who is logged in
- Logged-in user identifier visible in UI
- Notifications (in-app + email)
- Toast / alert on every action (success, error, warning)
- Confirmation modal before: logout, add, update, delete, archive
- Bulk import / upload to avoid manual data entry

---

## Audit Logging

- Always implement audit logs: who, what, when, where.
- Log at minimum: login, logout, failed login, create, update, delete, archive, errors.
- Mask sensitive values in audit log entries (see `06-security-guide.md`).

---

## Standard Web App Flow

```
User visits site
  └── Public pages (no auth required)
  └── Protected pages → redirect to login if not authenticated

Login
  └── Validate credentials
  └── Check account status (active / pending / disabled / rejected)
  └── Start session
  └── Redirect to dashboard
  └── Audit log: login recorded

Dashboard
  └── Show summary / stats relevant to user role
  └── Show notifications

CRUD Action (Create / Update / Delete / Archive)
  └── User fills form
  └── Confirmation modal → user confirms
  └── Client-side validation (immediate feedback)
  └── Server-side validation (always)
  └── DB transaction begins
      └── Perform DB operation(s)
      └── Commit on success / Rollback on failure
  └── Audit log recorded
  └── Notification sent (if applicable)
  └── Email sent (if applicable)
  └── Toast shown to user (success / error)
  └── UI updated (redirect or refresh)

Logout
  └── Confirmation modal
  └── Session destroyed
  └── Audit log: logout recorded
  └── Redirect to login
```

---

## Multi-Stage Approval Workflow Pattern

Use this shape for any feature where a record must pass through sequential reviewers before final approval (e.g. submission → peer review → supervisor review → final sign-off):

```
Record submitted
  └── Status: pending_review (stage 1)
  └── Assigned reviewer(s) for stage 1 notified

Each stage
  └── Reviewer views record + any prior stage recommendations
  └── Reviewer records: decision (approve / reject / request changes), timestamp, optional notes
  └── Audit log recorded
  └── On approve → advance to next stage, notify next reviewer(s)
  └── On reject / request changes → return to submitter, notify submitter, status reset accordingly
  └── On final stage approve → status: approved, notify submitter

Design notes
  └── Store each stage's decision as its own row (reviewer, role, timestamp, recommendation) — never overwrite a prior stage's decision
  └── Track current stage explicitly on the record (don't infer it from decision rows alone)
  └── Checklist-style review criteria, if any, are their own table keyed to the review stage
```

---

## Role-Based Access Flow

```
Request comes in
  └── Auth middleware → is user logged in?
      └── No → redirect to login
      └── Yes → role middleware → does user have required role?
          └── No → 403 Forbidden
          └── Yes → proceed to controller
```

---

## File Upload Flow

```
User selects file
  └── Client-side: check type and size before upload
  └── Server-side validation:
      └── Allowed MIME types
      └── Max file size
      └── Malicious content check
  └── Rename file (uuid or timestamp prefix)
  └── Store in correct storage directory
  └── Save path to database
  └── Return success + file URL
```

---

## Notification Flow

```
Action occurs (e.g., task assigned, status updated)
  └── Event fired
  └── Listener handles event
      └── Create in-app notification record
      └── Send email notification (queued)
  └── User sees notification badge
  └── User clicks → redirected to relevant page
```

---

## Standard API Request Flow

```
Client sends request
  └── Auth check (Sanctum / session)
  └── Request validation (Form Request)
  └── Controller calls Service
      └── Service performs business logic
      └── DB transaction if multi-table
  └── Return standard JSON response:
      {
        "success": true/false,
        "message": "...",
        "data": {...} or null,
        "errors": {...} or null
      }
```

(Full JSON response spec in `07-api-standards.md`.)