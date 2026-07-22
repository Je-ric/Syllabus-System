# Condition Completeness & Error Coverage Guide

Purpose: before any conditional logic, state machine, or data-dependent feature ships, verify every branch is accounted for, no two conditions can silently contradict each other, and every way the data could be missing or wrong has a defined outcome. This is a thinking discipline, not framework-specific — apply it in any language or stack.

---

## 1. Condition Completeness

Every decision point needs a defined outcome for *every* case, not just the ones you thought of first.

- Every `if / else if` chain has a final `else` (or an explicit comment saying why "no other case is possible" — and that assumption is verified, not assumed).
- Every `switch` / `match` has a `default` case that does something deliberate (log + safe fallback), never nothing.
- Every value a field/status/enum can hold has a defined behavior everywhere that field is used — if a new status is added later, grep for every place the old set of statuses was handled and update all of them, not just the one you were working on.
- Boolean logic is checked for the full truth table when more than one flag is involved — don't assume the two flags you tested are the only combination that occurs.
- Loops and recursive logic have a defined behavior for: zero items, one item, many items, and (for recursion) a guaranteed base case.

**Before shipping any conditional logic, ask:** *"If I fed this the one input I haven't thought of yet, what happens?"*

---

## 2. Conflicting / Contradictory Conditions

- Two conditions that are supposed to be mutually exclusive (e.g. a record can't be both `archived` and `active`) are enforced at the data layer (constraint, validation rule), not just assumed true by the UI.
- When two independent rules could both apply to the same case, define which one wins explicitly — don't let it be decided by whichever code path happens to run first.
- Where business rules changed over time, check for now-stale conditions elsewhere in the code that still assume the old rule — a rule change in one place is a search-and-verify task across the codebase, not a one-line edit.
- Time/date-based conditions (deadlines, expiry, "current" period) are checked for boundary conflicts: what happens exactly at the boundary instant, and what happens if the server and client clocks disagree?

---

## 3. All Possibilities of Error

For every action that can fail, walk through this list and decide what happens for each — don't just handle the one failure mode you happened to trigger in testing:

- **Network / connectivity failure** — request never reaches the server, or the response never comes back.
- **Timeout** — the operation is slow, not dead; don't treat it identically to a hard failure.
- **Permission denied** — the user is authenticated but not authorized for this specific action.
- **Not found** — the target record was deleted or never existed.
- **Validation failure** — input didn't meet requirements (see the input validation guide).
- **Conflict / concurrent modification** — someone else changed the record between when it was loaded and when it was saved.
- **Server error** — an unexpected exception on the backend.
- **Rate limit / throttling** — the action was blocked for being too frequent.
- **Partial failure in a batch operation** — 8 of 10 records succeeded, 2 failed; the response must say which is which, not just "some failed."
- **Third-party/external service failure** — the service you depend on is down, slow, or returns something unexpected.

Every one of the above needs: a caught exception (or handled rejection), a log entry with enough context to debug it later, and a response the frontend can act on (see `03-code-review-checklist.md` §3 Error Handling for the try/catch bar).

---

## 4. Missing / Null / Empty Data

"No data" is not one condition — it's several, and they usually need different handling:

| State | Example | Typical handling |
|---|---|---|
| Field is `null` | Optional field never filled in | Treat as "not provided" — don't crash, don't treat as `0`/`false`/empty string |
| Field is empty string `""` | User submitted a form with a blank text field | Decide explicitly if this counts as "missing" or a legitimate empty value — don't let this be accidental |
| Field is `0` / `false` | A real, valid value that is falsy | Never treat falsy-but-present as if it were missing — a common bug source in loose truthiness checks |
| Array/collection is empty `[]` | No related records exist yet | Distinct from `null` (relationship not loaded) — code should never confuse "zero results" with "failed to load results" |
| Related record doesn't exist | Foreign key points to nothing (or is null) | Guard before accessing nested properties; decide what the UI shows instead of the missing data |
| Field is present but the wrong type | API/client sent a string where a number was expected | Reject explicitly with a clear validation error — don't silently coerce and hope |
| Whole request/payload is missing expected keys | Frontend/backend contract drifted | Fail loudly and specifically, not with a generic 500 |

**Rule of thumb:** never assume a value is present, non-null, correctly typed, and non-empty at the same time unless it was just validated. Re-check at every boundary (controller entry, service entry, before rendering) rather than trusting it was already checked upstream.

---

## 5. Pre-Ship Checklist

Before marking any feature with conditional logic as done:

- [ ] Every conditional branch has a defined outcome — no implicit "falls through and does nothing."
- [ ] Any two conditions that shouldn't both be true are enforced, not assumed.
- [ ] Every failure mode in section 3 has been considered for this specific action, even if some are marked "won't happen because X" — that reasoning is written down, not left implicit.
- [ ] Every input this feature reads has been checked against the missing/null/empty table above.
- [ ] The "one input I haven't thought of yet" question has actually been asked and answered.

Cross-reference: `03-code-review-checklist.md` (§1 Correctness & Business Logic, §3 Error Handling) and `14-input-validation-guide.md` for validation-specific detail.