# Scripting Security Rules

## Client-Side Injection Prevention

### JavaScript Input Validation
- **Client-side validation is for UX only** — never trust it as the source of truth
- Always implement server-side validation as the authoritative check
- Use regex patterns to detect potentially dangerous content before submission

### Injection Detection Pattern
Use this regex pattern to detect common injection attempts:
```javascript
/<[^>]+>|javascript:|on\w+\s*=|<script|<\/script|--\s|union\s+select|drop\s+table|insert\s+into|delete\s+from|update\s+\w+\s+set/i
```

This pattern detects:
- HTML tags (`<...>`)
- JavaScript URIs (`javascript:`)
- Event handlers (`onclick=`, `onload=`, etc.)
- Script tags (`<script>`, `</script>`)
- SQL comments (`--`)
- SQL injection patterns (`union select`, `drop table`, etc.)

### Implementation Requirements
- **Block and Validate, not Sanitize and Save**: When injection is detected, block the submission and require the user to fix the input — never attempt to "clean" dangerous content
- Show clear, specific error messages: "Injection detected — scripts and HTML tags are not allowed"
- Provide visual feedback (red borders, warning icons) when dangerous content is detected
- Disable save buttons while injection is present in any field

## Alpine.js / Livewire Security

### Alpine.js Data Binding
- Use `x-model` for form inputs — Alpine handles basic escaping
- Never use `x-html` unless content is explicitly sanitized server-side
- Be cautious with `x-text` — it escapes HTML but not all special characters

### Livewire Component Security
- Always validate inputs in Livewire component methods (`rules()` property or manual validation)
- Use `$this->validate()` for server-side validation
- Never trust client-side state — re-validate on the server
- Implement proper authorization checks in component methods

## Rate Limiting

### When to Apply Rate Limiting
Rate limiting must be applied to:
- **Login endpoints** — prevent brute force attacks
- **OTP / password reset endpoints** — prevent abuse
- **Email sending endpoints** — prevent spam
- **Public-facing form submissions** — prevent automated abuse
- **API endpoints** — prevent resource exhaustion

### Laravel Rate Limiting Implementation
```php
// In routes/web.php or routes/api.php
Route::middleware(['throttle:60,1'])->group(function () {
    // 60 requests per minute
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
});

// For stricter limits on sensitive operations
Route::middleware(['throttle:5,1'])->group(function () {
    // 5 requests per minute
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
});
```

### Dynamic Rate Limiting
```php
// Rate limit by user ID
Route::middleware(['throttle:60,1,user'])->group(function () {
    // Different limits per authenticated user
});

// Rate limit by IP + endpoint combination
Route::middleware(['throttle:10,1'])->post('/api/public-form', [FormController::class, 'submit']);
```

## Input Sanitization vs Validation

### Golden Rule
**Block and Validate, not Sanitize and Save**

- **Validation**: Check if input meets requirements (format, type, range). If it fails, reject it.
- **Sanitization**: Remove/transform dangerous content. Only use this for trusted contexts.
- **Blocking**: Prevent submission of dangerous content entirely.

### When to Use Each Approach

| Scenario | Approach | Example |
|---|---|---|
| Plain text fields (names, descriptions) | **Block & Validate** | Reject HTML/script content entirely |
| Rich text editors (WYSIWYG) | **Sanitize** | Use HTML Purifier to allow safe HTML only |
| Search queries | **Validate & Escape** | Validate format, escape for database |
| File uploads | **Validate & Block** | Check type, size, then accept or reject |
| SQL queries | **Parameterized** | Never concatenate user input |

### Server-Side Sanitization (When Required)
```php
// For plain text that should never contain HTML
$cleanText = strip_tags($input);
$cleanText = trim($cleanText);

// For rich text (WYSIWYG) - use HTML Purifier
use HTMLPurifier;
use HTMLPurifier_Config;

$config = HTMLPurifier_Config::createDefault();
$config->set('HTML.Allowed', 'p,b,strong,i,em,u,a[href],ul,ol,li,br');
$purifier = new HTMLPurifier($config);
$cleanHtml = $purifier->purify($input);
```

## JavaScript Security Best Practices

### Content Security Policy (CSP)
- Implement CSP headers to restrict script sources
- Avoid `unsafe-inline` and `unsafe-eval`
- Use nonces or hashes for inline scripts when necessary

### DOM XSS Prevention
- Never use `innerHTML` with untrusted data
- Use `textContent` instead of `innerHTML` for text content
- When using `innerHTML`, sanitize content first
- Be cautious with `document.write()` and similar methods

### Event Handler Security
- Avoid inline event handlers (`onclick="..."`)
- Use `addEventListener()` instead
- Validate data before using it in dynamic code execution

## Livewire-Specific Rules

### Wire:click Security
- Always validate in the component method, never rely only on client-side checks
- Use `->authorize()` for authorization checks
- Implement rate limiting on frequently-clicked actions

### Wire:model Security
- Remember that `wire:model` updates can be triggered by users
- Always validate model properties in the component
- Use `->rules()` or custom validation logic

### Wire:loading Security
- Don't hide security indicators during loading states
- Keep error states distinct from loading states
- Prevent duplicate submissions during loading

## Form Submission Security

### CSRF Protection
- Always include `@csrf` in every Laravel form
- For AJAX requests, include the CSRF token in headers
- Verify CSRF tokens on all state-changing operations

### Double-Submit Prevention
- Use confirmation modals for destructive actions
- Disable submit buttons during form submission
- Implement "confirmed_submission" flags for critical operations

### File Upload Security
- Validate MIME type server-side (not just extension)
- Set maximum file size limits
- Rename files on upload (never use original filenames)
- Store files outside the public directory when possible
- Scan uploaded files for malicious content if feasible

## Code Review Checklist

For every form and interactive component:

- [ ] Client-side injection detection implemented where appropriate
- [ ] Server-side validation is authoritative
- [ ] Rate limiting applied to sensitive endpoints
- [ ] Input is blocked (not sanitized) when dangerous content is detected
- [ ] CSRF protection present on all forms
- [ ] User feedback is clear and specific for security violations
- [ ] Loading states don't hide security warnings
- [ ] File uploads have proper validation and storage
- [ ] Alpine.js/Livewire data binding is secure
- [ ] No use of `innerHTML` with untrusted data
- [ ] Event handlers are properly attached (not inline)
- [ ] Rate limits are appropriate for the endpoint's sensitivity

## Project-Specific Implementation Patterns

### Current Security Implementation in CSMS

#### Server-Side Validation (Excellent)
- **SecurityValidator Helper**: Located at `app/Helpers/SecurityValidator.php`
  - Comprehensive pattern detection for script, SQL, and code injection
  - Methods: `containsAnyInjection()`, `containsScriptInjection()`, `containsSqlInjection()`, `containsCodeInjection()`
  - Specific injection type detection for error messages

- **NoInjectionRule**: Custom Laravel validation rule at `app/Rules/NoInjectionRule.php`
  - Used in CourseController and other form validations
  - Blocks submission when injection patterns are detected
  - Provides specific error messages indicating injection type

#### Client-Side Detection (Excellent)
- **JavaScript Injection Detection**: Implemented in Livewire program components
  - Pattern: `/<[^>]+>|javascript:|on\w+\s*=|<script|<\/script|--\s|union\s+select|drop\s+table|insert\s+into|delete\s+from|update\s+\w+\s+set/i`
  - Real-time visual feedback (red borders, warning icons)
  - Blocks save operations when injection detected
  - Examples: `manage-peos.blade.php`, `manage-pos.blade.php`

#### Authorization & Access Control (Good)
- **Program Authorization**: `authorizeProgram()` method in CourseController
  - Department-based access control for chairs
  - Admin bypass for full access
  - Proper redirect with warning messages

- **Livewire Mount Authorization**: Check in `ManagePeos` and `ManagePos` components
  - Role-based access control (admin vs chair)
  - Department assignment validation
  - Session flash messages for unauthorized access

#### Areas for Improvement

**Rate Limiting**: Not currently implemented on form endpoints
- Add rate limiting to CourseController routes (store, update, destroy)
- Add rate limiting to Livewire component methods (savePeos, savePos)
- Implement stricter limits on destructive operations (delete, archive)

**Recommended Rate Limiting Implementation**:
```php
// In routes/web.php
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{course}', [CourseController::class, 'update']);
});

Route::middleware(['throttle:10,1'])->group(function () {
    Route::delete('/courses/{course}', [CourseController::class, 'destroy']);
    Route::post('/courses/{course}/archive', [CourseController::class, 'archive']);
});
```

**Livewire Rate Limiting**: Add to component methods
```php
// In Livewire components, use rate limiting middleware
protected $listeners = ['savePeos' => 'savePeos'];

public function savePeos(array $peosData): void
{
    // Add rate limiting check here or use middleware
    if (RateLimiter::tooManyAttempts('save-peos:'.auth()->id(), 10)) {
        throw new \Exception('Too many save attempts. Please wait.');
    }
    
    // existing validation logic...
    RateLimiter::hit('save-peos:'.auth()->id(), 60); // 10 attempts per minute
}
```

### Security Patterns to Follow

#### 1. Course Form Security Pattern
```php
// Controller validation (CourseController.php)
'code' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9\-\.\s]+$/', $courseCodeRule],
'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s\-\.\,0-9]+$/u', new NoInjectionRule()],
'description' => ['nullable', 'string', 'max:5000', new NoInjectionRule()],
```

#### 2. Livewire Component Security Pattern
```php
// Client-side detection (Blade files)
hasInjection(text) {
    if (!text) return false;
    return /<[^>]+>|javascript:|on\w+\s*=|<script|<\/script|--\s|union\s+select|drop\s+table|insert\s+into|delete\s+from|update\s+\w+\s+set/i.test(text);
}

// Server-side validation (Livewire components)
if (SecurityValidator::containsAnyInjection($text)) {
    $type = SecurityValidator::getInjectionType($text);
    throw new \RuntimeException("Field contains {$type} injection and is not allowed.");
}
```

#### 3. Authorization Pattern
```php
// In controllers
if ($redirect = $this->authorizeProgram($program)) return $redirect;

// In Livewire mount methods
if (!$user->hasRole('admin')) {
    $assignment = $user->getPrimaryDepartmentAssignment();
    $allowed = $assignment && Program::whereHas('departments', fn($q) =>
        $q->where('department_id', $assignment->department_id)
    )->where('id', $program->id)->exists();
    if (!$allowed) {
        session()->flash('toast', ['message' => 'Unauthorized access', 'type' => 'warning']);
        $this->redirect(route('programs.index'));
        return;
    }
}
```

## Cross-Reference

- `06-security-guide.md` — General security practices
- `14-Input validation.md` — Validation framework and standards
- `07-api-standards.md` — API-specific security requirements
- `app/Helpers/SecurityValidator.php` — Implementation of injection detection patterns
- `app/Rules/NoInjectionRule.php` — Laravel validation rule for injection prevention
