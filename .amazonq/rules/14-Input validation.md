# Input Validation Guide

Purpose: a framework-agnostic standard for what gets validated, where, and how the result is communicated back to the user. Applies to any stack — the specific validation-rule syntax for this project's framework lives in `07-api-standards.md`; this file is the discipline behind it.

---

## 1. Validate at Every Layer

- **Client-side validation** is for UX only — immediate feedback, fewer round trips. It is never trusted as the source of truth.
- **Server-side validation is always authoritative.** Every request is validated server-side regardless of what the client already checked (see `06-security-guide.md` — never trust client input).
- If there's a service layer between the controller and the database, don't assume the controller's validation is enough — re-check anything the service can also be called from elsewhere (jobs, commands, other services).

---

## 2. What to Validate

For every input field, check all of the following that apply:

- **Presence** — is it required, and is it actually there?
- **Type** — string, number, boolean, date, etc. — matches what's expected.
- **Format** — email, URL, phone, date format, regex pattern.
- **Range / length** — min/max length for strings, min/max value for numbers, min/max date for dates.
- **Allowed values** — must be one of a fixed set (enum/status), not just "any string."
- **Uniqueness** — must not collide with an existing record, where relevant.
- **Referential integrity** — a referenced ID must actually exist and be accessible to this user.
- **Business-rule validity** — passes domain-specific rules beyond raw format (e.g. an end date must be after a start date; a quantity can't exceed available stock).
- **Cross-field consistency** — fields that must agree with each other (password + confirmation, date ranges, dependent dropdowns).

---

## 3. Validation Error Response Shape

Keep this consistent everywhere so the frontend can handle any validation error the same way — see the standard error shape in `07-api-standards.md`:

```json
{
  "success": false,
  "message": "Validation failed.",
  "data": null,
  "errors": {
    "field_name": ["Specific, actionable message."]
  }
}
```

- Errors are keyed by field name so the frontend can map each message to its input.
- Messages are specific enough to act on ("Email must be a valid address") — never a bare "Invalid input."
- Messages never leak internal details (stack traces, table/column names, query fragments).
- A field can have more than one error; return all of them at once, not just the first one found — nothing is more frustrating than fixing one error only to be told about the next.

---

## 4. Frontend Validation Display Rules

- **Inline, per-field errors** appear directly next to (or under) the field they belong to — never only in a toast or a top-of-page summary alone.
- **Timing:** validate on blur/change for immediate feedback where cheap to do so; always re-validate on submit regardless of earlier checks.
- **Submit button** is disabled (or clearly blocked) while the form is in an invalid state, but don't hide *why* — still show the errors, don't just silently disable.
- **Loading vs error vs empty are three different states** — a failed validation is not the same as "still loading" or "no data"; each needs its own visual treatment.
- **Toast/alert** confirms the overall action failed ("Could not save — please fix the errors below"), while the field-level messages carry the specific detail. Use both together, not one instead of the other.

---

## 5. Null / Missing / Wrong-Type Input Matrix

What the user sees for each case — decide this explicitly per field, don't leave it to default framework behavior:

| Input state | Required field | Optional field |
|---|---|---|
| Missing entirely | "X is required." | Treated as not provided — proceeds normally |
| Present but `null`/empty | "X is required." | Stored as empty/null — no error |
| Wrong type (e.g. text where a number is expected) | "X must be a number." | Same — type errors apply regardless of required/optional |
| Out of allowed range/length | "X must be between A and B." | Same |
| References something that doesn't exist (invalid foreign key/ID) | "Selected X does not exist." | Same, if a value was provided at all |
| Fails a cross-field rule | Message names both fields ("End date must be after start date.") | Same |

---

## 6. Pre-Ship Checklist

- [ ] Every field a form/endpoint accepts has been checked against the "What to Validate" list in §2.
- [ ] Server-side validation exists independent of whatever the client does.
- [ ] Error response follows the standard shape and is keyed by field.
- [ ] Frontend shows inline field errors, not just a generic toast.
- [ ] The null/missing/wrong-type matrix has been worked through for every required field.
- [ ] No validation message exposes internal implementation detail.

Cross-reference: `06-security-guide.md` (input sanitization, never trust client input), `07-api-standards.md` (response shape, framework validation rule syntax), `13-condition-completeness-and-error-handling.md` (missing/null data handling beyond just validation).