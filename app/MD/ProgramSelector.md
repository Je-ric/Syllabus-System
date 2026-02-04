# ProgramSelector Component Usage Guide

The `ProgramSelector` Livewire component is a reusable, flexible component for selecting College → Department → Program hierarchies across your application.

## Component Location
`app/Livewire/Programs/ProgramSelector.php`

## Available Parameters

### 1. `programId` (optional)
- **Type:** Integer
- **Default:** null
- **Purpose:** Pre-select a program on mount
- **Usage:** `:program-id="$program->id"`

### 2. `redirectRoute` (optional)
- **Type:** String (route name)
- **Default:** null
- **Purpose:** Route to redirect to after program selection
- **Usage:** `redirect-route="courses.index"`
- **Special Routes:**
  - `courses.index` - Redirects with `?program_id={id}`
  - Any other route - Redirects to `route($name, $programId)`

### 3. `autoRedirect` (optional)
- **Type:** Boolean
- **Default:** true
- **Purpose:** Control whether redirect happens after selection
- **Usage:** `:autoRedirect="false"`
- **When false:** Component emits `programSelected` event instead of redirecting

## Events Dispatched

### `programSelected`
- **Fired:** When a program is selected
- **Payload:** `programId` (integer)
- **Usage:** Listen in parent component or JavaScript

## Usage Examples

### Example 1: Courses Management (With Redirect)
```blade
<livewire:programs.program-selector
    :program-id="optional($program)?->id"
    redirect-route="courses.index"
    :autoRedirect="true"
/>
```
**Behavior:** When user selects a program, page redirects to `/courses?program_id={id}`

---

### Example 2: Syllabus Selection (Without Redirect, With AJAX)
```blade
<livewire:programs.program-selector :autoRedirect="false" />
```
**Behavior:**
- Component renders but doesn't redirect
- JavaScript can listen to program selection changes via the rendered select elements
- Or listen to Livewire events: `@this.on('programSelected', (programId) => { ... })`

**JavaScript example in same view:**
```javascript
document.addEventListener('DOMContentLoaded', function() {
    const programSelect = document.querySelector('select[wire\\:model\\.live="programId"]');

    programSelect.addEventListener('change', function() {
        const programId = this.value;
        // Fetch courses or other data
        fetchCourses(programId);
    });
});
```

---

### Example 3: Program Management (With Redirect)
```blade
<livewire:programs.program-selector
    :program-id="optional($program)->id"
    redirect-route="programs.show"
    :autoRedirect="true"
/>
```
**Behavior:** When user selects a program, page redirects to `/programs/{id}`

---

### Example 4: Listen to Livewire Events
```blade
<livewire:programs.program-selector :autoRedirect="false" />

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('programSelected', (data) => {
            console.log('Program selected:', data.programId);
            // Handle program selection
        });
    });
</script>
```

---

## Component Internal Flow

1. **Mount:**
   - Load all colleges
   - Pre-select program if `programId` provided
   - Set `redirectRoute` and `autoRedirect` properties

2. **College Selection Changed:**
   - Fetch departments for selected college
   - Reset department, program, and sub-programs

3. **Department Selection Changed:**
   - Fetch programs for selected department
   - Reset program selection

4. **Program Selection Changed:**
   - Dispatch `programSelected` event
   - If `autoRedirect` is true AND `redirectRoute` is set:
     - Redirect to the specified route

---

## Database Relationships

The component relies on these relationships:
- **College** → has many **Department**
- **Department** → belongs to many **Program** (via `program_departments` pivot)
- **Program** → belongs to many **Department**

---

## Styling

The component uses Tailwind CSS with a grid layout:
- Default: 1 column on mobile, 3 columns on medium+ screens
- Each dropdown includes loading indicator
- Disabled state when parent not selected

---

## Best Practices

✅ **DO:**
- Set `autoRedirect="false"` when you need custom logic after selection
- Use both `redirectRoute` and `autoRedirect` parameters explicitly for clarity
- Pre-select a program when editing to show current state
- Listen to select element changes for immediate feedback

❌ **DON'T:**
- Rely on default redirect without specifying `redirectRoute`
- Use the same selector for multiple ProgramSelectors on one page without unique identifiers
- Forget to handle the no-redirect case in JavaScript

---

## Troubleshooting

### "Route [syllabus.select-course] not defined" error
**Cause:** Passing invalid `redirect-route` name
**Solution:** Either remove `redirectRoute` parameter or use a valid route name

### Component not triggering actions on selection
**Cause:** `autoRedirect` is true but `redirectRoute` is not set
**Solution:** Set both parameters: `redirect-route="route.name" :autoRedirect="true"`

### AJAX not working
**Cause:** JavaScript selector not matching the rendered element
**Solution:** Check browser console for actual selector, might need to adjust selector if Livewire changes structure

---

## Supported Routes Currently Configured

- `courses.index` - Manage courses (with program_id query param)
- `programs.show` - View program (with program id param)
- `syllabus.create` - Create syllabus (no redirect, uses AJAX)

Add more routes as needed following the same pattern!
