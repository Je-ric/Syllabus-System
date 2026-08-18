# Security Guide

## Output / Rendering

| Syntax | Safe? | Use when |
|---|---|---|
| `{{ $var }}` | ✅ Safe | Always — auto-escapes HTML |
| `{!! $var !!}` | ❌ Dangerous | Only for trusted, sanitized HTML |
| `v-html` (Vue) | ❌ Dangerous | Avoid — use only if sanitized |

---

## SQL Injection

| Approach | Safe? |
|---|---|
| Eloquent ORM | ✅ Safe |
| Query Builder (`->where()`) | ✅ Safe |
| Raw SQL with bindings (`DB::select('... where id = ?', [$id])`) | ✅ Safe |
| Raw SQL + string concat (`"where id = " . $id`) | ❌ Dangerous |

---

## File Uploads

Checklist for every file upload:

- [ ] Validate MIME type server-side (not just extension)
- [ ] Set max file size limit
- [ ] Rename file on save — never use the original filename
- [ ] Store outside public root or use private storage
- [ ] Never execute uploaded files
- [ ] Scan for malicious content if possible

```
Allowed image types:  jpg, jpeg, png, webp
Allowed doc types:    pdf
Max size:             2MB (2048 KB)
Rename pattern:       {uuid}.{ext} or {timestamp}_{random}.{ext}
```

---

## Authentication & Authorization

- Always check **authorization** (can this user do this?), not just authentication (is the user logged in?)
- Use middleware for role checks — never check roles only inside the controller
- Never expose admin routes without role middleware
- Use CSRF protection on all forms (Laravel does this by default with `@csrf`)
- Use `bcrypt` / `Hash::make()` for passwords — never store plain text
- Invalidate sessions on logout
- Implement account lockout or rate limiting on login attempts

---

## Input Sanitization

- Use `strip_tags()` for plain text fields that should not contain HTML
- Use HTML Purifier for rich text / WYSIWYG content
- Trim and normalize whitespace on all string inputs
- Reject or sanitize special characters in filenames

---

## Sensitive Data

- Never log passwords, tokens, or payment details
- Never expose `.env` values in responses or views
- Never commit `.env` to version control
- Mask sensitive fields in audit logs (e.g., show `password changed` not the actual value)
- Use HTTPS in production — never serve over HTTP

---

## CSRF

- Laravel includes CSRF protection by default for web routes
- Always include `@csrf` in every form
- API routes using Sanctum use token-based auth — no CSRF needed there

---

## Rate Limiting

Apply rate limiting to:
- Login endpoint
- OTP / password reset endpoint
- Any endpoint that sends emails
- Any public-facing form submission

---

## Common Vulnerabilities Checklist

- [ ] XSS — escape all output, sanitize rich text input
- [ ] SQL Injection — use Eloquent / query builder, never raw concat
- [ ] CSRF — `@csrf` on all forms
- [ ] Broken access control — role middleware on all protected routes
- [ ] Sensitive data exposure — no plain text passwords, no `.env` in responses
- [ ] File upload abuse — validate type, size, rename, store safely
- [ ] Mass assignment — use `$fillable` or `$guarded` on all models
- [ ] Open redirect — validate redirect URLs


Block and Validate not Sanitize and save
