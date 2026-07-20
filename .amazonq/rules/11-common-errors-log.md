# Common Errors & Fixes

A running log of errors encountered and how they were resolved.
Add new entries at the top.

---

## Format

```
### [Error Title]
- **Context:** What were you doing when this happened?
- **Error:** The exact error message or symptom
- **Cause:** What caused it
- **Fix:** What resolved it
- **Prevention:** How to avoid it next time
```

---

## Laravel / PHP

### 419 Page Expired on Form Submit
- **Context:** Submitting a form
- **Error:** `419 | Page Expired`
- **Cause:** Missing `@csrf` directive in the form
- **Fix:** Add `@csrf` inside the `<form>` tag
- **Prevention:** Always include `@csrf` in every form

---

### Call to a member function on null
- **Context:** Accessing a relationship or method on a model
- **Error:** `Call to a member function X() on null`
- **Cause:** The query returned null — the record doesn't exist
- **Fix:** Add a null check before accessing: `if ($model) { ... }` or use `findOrFail()`
- **Prevention:** Use `findOrFail()` for single record lookups; always check for null

---

### Mass Assignment Exception
- **Context:** Using `Model::create([...])` or `$model->fill([...])`
- **Error:** `Add [field] to fillable property`
- **Cause:** The field is not listed in the model's `$fillable` array
- **Fix:** Add the field to `$fillable` in the model
- **Prevention:** Keep `$fillable` updated whenever adding new columns

---

### Class Not Found
- **Context:** Using a class in a controller or service
- **Error:** `Class "App\Services\SomeService" not found`
- **Cause:** Wrong namespace or missing `use` import
- **Fix:** Add the correct `use` statement at the top of the file
- **Prevention:** Use IDE autocompletion to import classes

---

### SQLSTATE[23000]: Integrity Constraint Violation
- **Context:** Inserting or updating a record
- **Error:** `Integrity constraint violation: 1062 Duplicate entry`
- **Cause:** Trying to insert a duplicate value in a unique column
- **Fix:** Check for existing record before inserting; use `firstOrCreate()` or `updateOrCreate()`
- **Prevention:** Add unique validation rule in Form Request

---

### Undefined Variable in Blade
- **Context:** Rendering a Blade view
- **Error:** `Undefined variable $variableName`
- **Cause:** Variable not passed from controller to view
- **Fix:** Add the variable to the `return view('...', ['variableName' => $value])`
- **Prevention:** Always pass all required variables explicitly

---

## JavaScript / Frontend

### Self-Triggered Auto-Save Loop
- **Context:** A component auto-saves a field and also listens for a "changed" event to trigger re-renders/side effects
- **Error:** No thrown error — symptom is repeated/runaway save requests or flickering UI
- **Cause:** The save action emits the same event the component is listening for, so saving triggers another save
- **Fix:** Break the loop with a guard flag during the save, or restructure to a one-directional (deferred push) pattern instead of a listen/emit cycle
- **Prevention:** When a component both listens for and emits a signal, trace the full cycle before shipping — ask "can this fire itself?"

---

### Rapid-Delete 404 / Record Already Gone
- **Context:** User double-clicks delete, or deletes the same row from two tabs
- **Error:** `404 Not Found` or `ModelNotFoundException` from `findOrFail()`
- **Cause:** The record was already deleted by the first request; the second request can't find it
- **Fix:** Use a null-guarded `find()` (or check existence first) instead of `findOrFail()` for actions where "already gone" is an expected outcome, not an error — treat it as a no-op success rather than throwing
- **Prevention:** Reserve `findOrFail()` for lookups where a missing record genuinely indicates a bug (e.g. loading a detail page from a valid link); use guarded lookups for destructive actions

---

### Cannot read properties of undefined
- **Context:** Accessing a property on a JS object
- **Error:** `Cannot read properties of undefined (reading 'x')`
- **Cause:** The object is undefined — API returned null or data not loaded yet
- **Fix:** Add optional chaining: `obj?.property` or check before accessing
- **Prevention:** Always handle loading and empty states

---

### CORS Error on API Request
- **Context:** Frontend calling a backend API
- **Error:** `Access to fetch at '...' from origin '...' has been blocked by CORS policy`
- **Cause:** Backend not configured to allow the frontend origin
- **Fix:** Add the frontend URL to `config/cors.php` allowed origins in Laravel
- **Prevention:** Configure CORS properly before starting frontend-backend integration

---

## Database / MySQL

### Cannot Add Foreign Key Constraint
- **Context:** Running a migration with a foreign key
- **Error:** `SQLSTATE[HY000]: General error: 1215 Cannot add foreign key constraint`
- **Cause:** Referenced table doesn't exist yet, or column types don't match
- **Fix:** Ensure the referenced table is created first; ensure column types match exactly (both `unsignedBigInteger`)
- **Prevention:** Create migrations in dependency order; use `unsignedBigInteger` consistently

---

### Table Already Exists
- **Context:** Running `php artisan migrate`
- **Error:** `SQLSTATE[42S01]: Table already exists`
- **Cause:** Migration was partially run or table was created manually
- **Fix:** Drop the table manually or use `php artisan migrate:fresh` (dev only — destroys all data)
- **Prevention:** Never manually create tables that have migrations

---

## Deployment

### .env Changes Not Taking Effect
- **Context:** Updated `.env` on server
- **Cause:** Laravel caches config — old values still in cache
- **Fix:** Run `php artisan config:clear` and `php artisan cache:clear`
- **Prevention:** Always clear cache after any `.env` change

---

### Storage Files Not Accessible (404)
- **Context:** Uploaded files returning 404
- **Cause:** Storage symlink not created
- **Fix:** Run `php artisan storage:link`
- **Prevention:** Include `storage:link` in deployment checklist