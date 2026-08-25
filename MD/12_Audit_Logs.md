# Audit Logs

How Audit Logs filtering, search, sorting, and pagination work.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/System/AuditLogController.php`
- Model
  - `app/Models/AuditLog.php`
- Routes
  - `routes/web.php` (audit logs route)

## Access

- Route: `GET /audit-logs`
- Middleware: authenticated + `role:admin`
- **Security**: Admin-only access prevents unauthorized viewing of sensitive system activity logs.

## Security Implementation

### Authorization
- **Role-Based Access Control**: Audit logs are restricted to admin users only via `role:admin` middleware.
- **Authentication Requirement**: All access requires valid authentication session.
- **Admin-Only Design**: Prevents non-admin users from viewing system-wide activity patterns.

### Input Validation
- **Parameter Validation**: All filter inputs are validated before query construction:
  - `user_id`: Must exist in users table
  - `reference_id`: Numeric validation
  - `date_from`/`date_to`: Date format validation
- **SQL Injection Prevention**: All queries use Laravel's Eloquent ORM with parameter binding.
- **Search Injection Prevention**: LIKE search uses parameter binding to prevent SQL injection.

### Data Protection
- **Sensitive Data**: Audit logs may contain sensitive operational data; admin-only access prevents exposure.
- **Query Preservation**: Query strings are preserved across pagination links but are URL-encoded for safety.
- **No Data Modification**: Audit log viewing is read-only; no modification endpoints exist.

### Performance & Availability
- **Pagination**: Limited to 20 rows per page to prevent resource exhaustion.
- **Query Optimization**: Filters are applied conditionally to optimize query performance.
- **Index Usage**: Sorted by timestamp DESC with secondary id DESC for efficient pagination.

### Rate Limiting
- **Current Status**: Rate limiting is not currently implemented on audit log endpoints.
- **Recommended Enhancement**: Add rate limiting to audit log viewing to prevent automated log scraping.

## Conditions (If / Then)

Each filter is optional and applied only when the request input is present.

- If `user_id` is provided:
  - Then validate that user_id exists in users table.
  - Then filter by `user_id`.
- If `module` is provided:
  - Then validate against whitelist of allowed modules.
  - Then filter by exact `module`.
- If `action` is provided:
  - Then validate against whitelist of allowed actions.
  - Then filter by exact `action`.
- If `reference_id` is provided:
  - Then validate as numeric value.
  - Then filter by `reference_id`.
- If `date_from` is provided:
  - Then validate as valid date format.
  - Then filter `timestamp >= date_from`.
- If `date_to` is provided:
  - Then validate as valid date format.
  - Then filter `timestamp <= date_to`.
- If `q` is provided:
  - Then sanitize search string to prevent SQL injection.
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
