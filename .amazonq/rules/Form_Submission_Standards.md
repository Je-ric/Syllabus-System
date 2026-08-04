# Form Submission Improvements

## What's Already Done

* Submitting state on all forms

  * Disables buttons and inputs while submitting.
* `::readonly` on text inputs and textareas

  * Values still submit correctly.
* `::disabled` on selects with hidden input mirrors

  * Preserves submitted values while preventing interaction.
* Spinner and loading label swap

  * Implemented through the `submitting` and `loadingText` button component props.
* `cursor-not-allowed` on locked fields

  * Provides a visual indication that the field cannot be edited.
* Submit button disabled when:

  * No input is provided (Add modals).
  * No changes are detected (Update modals).
* Submit button disabled until a selection is made

  * Applied to Assign modals.

---

# What Still Needs Doing

## 1. Double-submit prevention on the server side

The client-side `submitting = true` prevents users from repeatedly clicking the submit button, but duplicate POST requests can still reach the server if JavaScript is delayed or the form is submitted before Alpine initializes.

Need idempotency tokens or a simple request throttle per route using Laravel's built-in Rate Limiter.

```php
// RateLimiter in RouteServiceProvider or a middleware
RateLimiter::for('form-submit', fn(Request $r) =>
    Limit::perMinute(10)->by($r->user()?->id)
);
```

---

## 2. Modal close prevention while submitting

Currently, users can still close the modal by clicking outside it or pressing **Escape** while a submission is in progress.

The dialog should remain locked while `submitting = true`.

```blade
{{-- on the <dialog> element --}}
@keydown.escape.prevent="if (submitting) $event.preventDefault()"
x-on:click.self="if (!submitting) $el.close()"
```

This should ideally be implemented inside the `x-modal.dialog` component or exposed as a configurable prop.

---

## 3. Cancel/Close button locked while submitting

The `x-modal.close-button` already receives:

```blade
::disabled="submitting"
```

However, the `<dialog>` element can still be dismissed using the Escape key or by clicking the backdrop (see Item #2).

Disabling the close button alone is not sufficient.

---

## 4. Back-navigation / page unload warning

If a user has entered data into an Add or Update form and accidentally refreshes, closes the browser, or navigates away, there is currently no warning about unsaved changes.

A `beforeunload` listener tied to form dirtiness would help prevent accidental data loss.

```blade
x-on:submit="submitting = true"
x-init="
    window.addEventListener('beforeunload', e => {
        if (goalText.trim() && goalText !== original) {
            e.preventDefault(); e.returnValue = '';
        }
    })
"
```

---

## 5. Input length limits / validation hints

The Goal and Objective textareas currently have:

* No `maxlength`
* No character counter

Users can enter arbitrarily long text and only receive validation feedback after submitting.

---

## 6. Re-enable submitting on server-side validation failure

With the current full-page form submissions, Laravel redirects back after validation errors, causing Alpine to reload and automatically reset `submitting` to `false`.

This behavior is already acceptable.

However, if these forms are converted to AJAX in the future, `submitting` should be reset inside the request's error (`catch`) handler.

---

## 7. Confirm modals for destructive actions — close button behavior

Delete and Remove confirmation dialogs already include a **Cancel** button.

However, they still allow closing via the Escape key or backdrop click.

This is a low-risk issue but should be made consistent with Item #2.

---

## 8. Toast / feedback after success

Success feedback is already partially implemented using Laravel's:

```php
session('toast')
```

Verify that:

* All successful routes consistently return a toast message.
* The frontend reliably displays the toast after redirects.

---

# Priority Order

| Priority | Item                                    | Risk                         | Effort |
| -------- | --------------------------------------- | ---------------------------- | ------ |
| 1        | Modal close lock while submitting       | Medium — orphaned request    | Low    |
| 2        | Server-side rate limiting               | High — spam / duplicate data | Low    |
| 3        | Cancel locked via Escape/backdrop       | Low — UX consistency         | Low    |
| 4        | Input length limits + character counter | Low — data integrity         | Medium |
| 5        | Beforeunload warning                    | Low — convenience            | Medium |

---

**Next Step**

Implement the remaining improvements in the order shown above, prioritizing reliability and consistency before introducing additional UX enhancements.


## Additional Improvements Completed

### Form & Modal Standards

* Standardized `submitting` state across all forms.
* Standardized loading button behavior using the shared button component.
* Standardized Add, Update, Assign, and Delete modal behaviors.
* Standardized Alpine state initialization using `@js()` for all PHP values.

### Alpine State Initialization

* All Alpine variables are initialized using `@js()` instead of Blade string interpolation.
* Supports quotes, newlines, and special characters safely.
* Supports `old()` values for validation redirects.

### Update Modal Change Detection

* Update forms track original values using `orig*` properties.
* Submit buttons remain disabled until a meaningful change is detected.
* String comparisons trim whitespace before comparing.
* Array comparisons normalize values before comparison.

### Select & Checkbox Handling

* Disabled `<select>` elements preserve submitted values using hidden input mirrors.
* Alpine `x-model` is used instead of `x-ref` to avoid submit race conditions.
* Checkbox groups are visually locked instead of individually disabled.

### Alpine Type Consistency

* Select values are consistently treated as strings.
* PHP IDs used in Alpine expressions are quoted to avoid strict comparison issues.

### Route & Form Standards

* Modal forms use verified route names from `web.php`.
* HTTP method spoofing (`@method`) is always placed inside the form after `@csrf`.

### Modal ID Consistency

* Modal IDs are unique per record.
* The same generated ID is reused consistently across:

  * `<dialog>`
  * `x-modal.header`
  * `x-modal.close-button`
  * JavaScript `showModal()` calls

### Hidden Input Standards

* Hidden inputs always contain a valid value before submission.
* Hidden values are synchronized through Alpine or JavaScript when necessary.

### Loading Label Standardization

* Loading labels use predefined mappings instead of generated strings.
* Supports irregular verbs consistently across the application.

$loadingLabels = [
    'approve' => 'Approving…',
    'reject'  => 'Rejecting…',
    'restore' => 'Restoring…',
    'disable' => 'Disabling…',
    'delete'  => 'Deleting…',
    'archive' => 'Archiving…',
    'assign'  => 'Assigning…',
    'remove'  => 'Removing…',
    'save'    => 'Saving…',
    'create'  => 'Creating…',
    'update'  => 'Updating…',
];


### Form Submission Guard

* Alpine submit handlers use `$event.preventDefault()` when submission must be blocked.
* Submit logic no longer relies on `return false`, which Alpine ignores.
