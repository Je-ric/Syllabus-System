# Audit Logs (Current Behavior)

## Source of Truth

- `app/Http/Controllers/AuditLogController.php`
- `app/Models/AuditLog.php`

## Access

- Route: `GET /audit-logs`
- Middleware: authenticated + `role:admin`

## Filter Conditions

Each filter is optional and only applied when request input is present:

- If `user_id` is provided, query filters by `user_id`.
- If `module` is provided, query filters by exact `module`.
- If `action` is provided, query filters by exact `action`.
- If `reference_id` is provided, query filters by `reference_id`.
- If `date_from` is provided, query filters `timestamp >= date_from`.
- If `date_to` is provided, query filters `timestamp <= date_to`.
- If `q` is provided, query performs `LIKE` search on:
- `description`
- `module`
- `action`

## Result Behavior

- Results are sorted by latest first:
- `timestamp DESC`
- then `id DESC`
- Pagination is `20` rows per page.
- Query string is preserved across pagination links.

## UI Support Data

- Users list is loaded (`id`, `name`) for user filter dropdown.
- Distinct `module` values are loaded for module filter.
- Distinct `action` values are loaded for action filter.
