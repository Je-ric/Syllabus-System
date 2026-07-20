# API Standards

## Standard JSON Response Structure

Always return a consistent shape regardless of success or failure.

```json
{
  "success": true,
  "message": "User created successfully.",
  "data": { ... },
  "errors": null
}
```

```json
{
  "success": false,
  "message": "Validation failed.",
  "data": null,
  "errors": {
    "email": ["The email field is required."],
    "name": ["The name field must not exceed 255 characters."]
  }
}
```

---

## HTTP Status Codes

| Code | Meaning | When to use |
|---|---|---|
| 200 | OK | Successful GET, PUT, PATCH |
| 201 | Created | Successful POST (resource created) |
| 204 | No Content | Successful DELETE (nothing to return) |
| 400 | Bad Request | Malformed request, missing params |
| 401 | Unauthorized | Not logged in |
| 403 | Forbidden | Logged in but no permission |
| 404 | Not Found | Resource does not exist |
| 422 | Unprocessable Entity | Validation errors |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Server Error | Unexpected server-side failure |

---

## HTTP Methods

| Method | Purpose | Example |
|---|---|---|
| GET | Read / fetch | `GET /api/users` |
| POST | Create | `POST /api/users` |
| PUT | Full update | `PUT /api/users/1` |
| PATCH | Partial update | `PATCH /api/users/1` |
| DELETE | Delete | `DELETE /api/users/1` |

---

## Route Naming (REST)

| Action | Method | Route | Route Name |
|---|---|---|---|
| List all | GET | `/api/users` | `users.index` |
| Show one | GET | `/api/users/{id}` | `users.show` |
| Create | POST | `/api/users` | `users.store` |
| Update | PUT/PATCH | `/api/users/{id}` | `users.update` |
| Delete | DELETE | `/api/users/{id}` | `users.destroy` |

---

## Request Validation Rules (Laravel)

Always use Form Request classes for validation. Never validate inside the controller directly.

```
required         — field must be present and not empty
nullable         — field can be null
string           — must be a string
integer          — must be an integer
numeric          — must be a number
boolean          — must be true/false
email            — must be valid email format
max:255          — max character length
min:1            — min value or length
in:active,inactive — must be one of listed values
exists:table,col — must exist in DB
unique:table,col — must be unique in DB
mimes:jpg,png    — allowed file types
max:2048         — max file size in KB
```

---

## Rules

- Always validate on the server — never trust client input
- Always return the standard response shape
- Never expose raw DB errors to the client
- Never expose IDs in public-facing responses if not needed
- Use pagination for list endpoints — never return all records unbounded
- Use queues for emails and heavy operations — never block the response
- Rate limit sensitive endpoints (login, OTP, password reset)
