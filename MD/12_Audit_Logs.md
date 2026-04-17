# Audit Logs

How Audit Logs filtering, search, sorting, and pagination work.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/AuditLogController.php`
- Model
  - `app/Models/AuditLog.php`
- Routes
  - `routes/web.php` (audit logs route)

## Access

- Route: `GET /audit-logs`
- Middleware: authenticated + `role:admin`

## Conditions (If / Then)

Each filter is optional and applied only when the request input is present.

- If `user_id` is provided:
  - Then filter by `user_id`.
- If `module` is provided:
  - Then filter by exact `module`.
- If `action` is provided:
  - Then filter by exact `action`.
- If `reference_id` is provided:
  - Then filter by `reference_id`.
- If `date_from` is provided:
  - Then filter `timestamp >= date_from`.
- If `date_to` is provided:
  - Then filter `timestamp <= date_to`.
- If `q` is provided:
  - Then perform `LIKE` search on:
    - `description`
    - `module`
    - `action`

## Result Behavior

- Sorting:
  - `timestamp DESC`
  - then `id DESC`
- Pagination: `20` rows per page.
- Query string is preserved across pagination links.

## UI Support Data

- Users list is loaded (`id`, `name`) for the user filter dropdown.
- Distinct `module` values are loaded for module filter.
- Distinct `action` values are loaded for action filter.

## Sequences (Typical Flow)

1. Admin opens Audit Logs page.
2. Controller builds query.
3. Controller conditionally applies filters/search.
4. Controller sorts and paginates.
5. View renders results and preserves query params during navigation.
