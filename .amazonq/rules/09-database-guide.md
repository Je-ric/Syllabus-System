# Database Guide

## Schema Rules

- Every relationship has a foreign key constraint.
- Never auto-delete data — no unguarded cascade deletes on tables with historical/audit significance. Prefer soft deletes for those.
- Schema changes go through migrations only — never manual DB edits.
- Normalize appropriately; flag denormalization explicitly when chosen for performance, with a comment explaining why.
- Add unique constraints where needed — including composite unique constraints wherever a "duplicate row" would be a data integrity error, not just a UX inconvenience.
- Always include `created_at` and `updated_at` timestamps.
- Display data with a counter or generated reference — never expose raw sequential IDs to the user where avoidable.

(See `04-naming-conventions.md` for table/column/foreign-key naming.)

---

## Queries

- Keep queries close to where they're needed.
- Extract queries into model scopes/services when they are reused, become difficult to understand, or contain business rules.
- Avoid N+1 queries — always eager load relationships when appropriate.
- Prevent duplicate inserts before writing — check existing records or use `firstOrCreate()` / `updateOrCreate()`.

---

## Transactions

Wrap multi-table operations in DB transactions:

```php
DB::beginTransaction();

try {
    // ... perform DB operation(s)
    DB::commit();
} catch (\Throwable $e) {
    DB::rollBack();
    // handle, log, or rethrow with context
}
```

---

## Common Pitfalls to Check For in a Schema Review

- Missing composite unique constraints where a combination of columns (not a single column) determines uniqueness.
- Cascade deletes on ownership/authorship foreign keys (e.g. `user_id` on content someone authored) — deleting the user shouldn't silently delete their historical work. Prefer `restrict`/`set null` + soft delete instead.
- Nullable foreign key or identifying columns that quietly undermine a unique index (a unique constraint with a nullable column allows multiple "duplicate" rows because `NULL != NULL` in most databases).
- Schema duplication — the same piece of information stored in two tables that can drift out of sync instead of being derived or referenced.

---

## Checklist Before Merging a Migration/Query Change

- [ ] Foreign keys defined and types match on both sides (e.g. both `unsignedBigInteger`)
- [ ] Soft deletes used for anything with audit/historical significance
- [ ] Unique constraints cover every case where a duplicate is a data error
- [ ] No unbounded queries — pagination used for list endpoints
- [ ] Relationships eager loaded, not N+1
- [ ] Destructive queries scoped precisely (no accidental mass update/delete)