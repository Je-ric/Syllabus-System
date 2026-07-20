# Debugging Guide

## Step 1 — Read the Error Carefully

- Read the full error message before doing anything
- Note the file name and line number
- Note the error type (TypeError, QueryException, 404, 500, etc.)

---

## Step 2 — Check the Common Minor Issues First

These cause 80% of bugs:

- Typo in variable name (`$userId` vs `$userid`)
- Wrong parameter order in function call
- Missing `return` statement
- Wrong comparison operator (`=` instead of `==` or `===`)
- Null / undefined value being accessed
- Wrong data type (`"1"` vs `1`)
- Case sensitivity (`User` vs `user`, `GET` vs `get`)
- Missing comma or bracket in array/object
- Wrong array key name
- A listener firing the same event it's listening for (infinite update loop in reactive/auto-save components)

---

## Step 3 — Use the Right Tool

### PHP / Laravel

| Tool | How |
|---|---|
| `dd($var)` | Dump and die — stops execution and shows value |
| `dump($var)` | Dump without stopping |
| `Log::info('message', ['data' => $var])` | Write to log file |
| `storage/logs/laravel.log` | Check the log file |
| `php artisan tinker` | Run PHP interactively |

### JavaScript / Frontend

| Tool | How |
|---|---|
| `console.log(var)` | Print value |
| `console.table(array)` | Print array as table |
| `console.error(msg)` | Print error in red |
| `debugger;` | Pause execution at that line |

### Browser DevTools (F12)

| Tab | What to check |
|---|---|
| Elements | Actual rendered DOM — check if HTML is correct |
| Console | JS errors and logs |
| Network | Failed requests, status codes, request/response payloads |
| Sources | Set breakpoints, step through JS |
| Application | localStorage, sessionStorage, cookies |
| Performance | Slow scripts, rendering bottlenecks |
| Security | HTTPS / certificate issues |

---

## Step 4 — Check the Environment

- Is `.env` correct? (DB credentials, APP_URL, mail settings)
- Is the correct `.env` being used? (local vs production)
- Did you run `php artisan config:clear` after changing `.env`?

---

## Step 5 — Check Compatibility

- Framework version mismatch
- Package version conflict
- Node / npm version mismatch
- PHP version mismatch
- Browser compatibility issue
- Different database versions

---

## Step 6 — Check the Database

- Is the migration run? (`php artisan migrate:status`)
- Is the table/column name correct?
- Is there a null value where a value is expected?
- Is the relationship defined correctly?
- Is the query returning empty when it shouldn't?
- Are foreign key constraints blocking the operation?

---

## Step 7 — Check the Request / API

- Is the endpoint URL correct?
- Is the HTTP method correct? (GET vs POST vs PUT vs DELETE)
- Is the request payload correct? (check Network tab)
- Is the response what you expect? (check Network tab → Response)
- Is the CSRF token included?
- Is the auth token included?

---

## Common Error Messages

| Error | Likely Cause |
|---|---|
| `SQLSTATE[23000]: Integrity constraint violation` | Duplicate entry or FK constraint failed |
| `SQLSTATE[42S22]: Column not found` | Wrong column name or missing migration |
| `Class not found` | Missing import / wrong namespace |
| `419 Page Expired` | Missing `@csrf` or session expired |
| `403 Forbidden` | Missing role/permission |
| `404 Not Found` | Wrong route or missing record |
| `500 Server Error` | PHP exception — check `laravel.log` |
| `TokenMismatchException` | CSRF token mismatch |
| `Call to a member function on null` | Variable is null — check query result |
| `Undefined variable` | Variable not passed to view or not declared |
| `Mass assignment` | Field not in `$fillable` |

(For fixes to specific errors already encountered, see `11-common-errors-log.md`.)

---

## Checklist When Stuck

- [ ] Read the full error message
- [ ] Check the log file
- [ ] `dd()` the variable you suspect
- [ ] Check Network tab for the actual request/response
- [ ] Check if the DB has the expected data
- [ ] Check `.env` values
- [ ] Clear all caches (`config:clear`, `cache:clear`, `view:clear`)
- [ ] Google the exact error message
- [ ] Isolate — comment out code until the error disappears, then narrow down