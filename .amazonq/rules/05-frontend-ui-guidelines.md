# Frontend & UI/UX Guidelines

Applies on top of `01-agent-persona.md` and `03-code-review-checklist.md`. Covers what's specific to UI work — general code-quality and safety checks live in the checklist, not repeated here.

## Stack
HTML5, CSS3, Tailwind CSS, JavaScript (ES2023+), Alpine.js, Laravel Blade, Livewire 3, Vite.

## Layout
- Consistent spacing, alignment, margins, and container widths across pages.
- Clear visual hierarchy — the most important element on a screen should look like it.
- Deliberate use of white space, not leftover space.
- Maintain the selected color scheme / palette throughout the app.
- Use simple, low-vocabulary prompts and labels — design for the least technical user.

## Forms
- Easy to scan: logical grouping, clear labels, required-field indicators.
- Validation errors are specific and appear near the field they relate to.
- Full keyboard navigation supported.
- Auto-compute derived fields where possible (e.g. age from birthdate input).
- Cascading address fields (province → city → barangay) driven by JSON data.

## Navigation
- Predictable placement, clear active states, minimal clicks to any destination.
- Breadcrumbs where the hierarchy is deep enough to need them.

## Components
- Reusable and consistently styled across the app.
- Every interactive component handles: hover, focus, active, disabled, loading, error, and empty states — not just the happy path.
- Disable buttons during processing to prevent double submit.
- Show clear status indicators: loading spinner, unsaved changes, pending changes.
- Toast/alert feedback on every action (success, error, warning).
- Confirmation modal before: logout, add, update, delete, archive.

## Responsiveness
- Mobile-first, then tablet, then desktop.
- No horizontal scrolling or overflow at any breakpoint.
- Typography and spacing scale with viewport, not fixed pixel values.

## Accessibility (WCAG 2.2 AA)
- Semantic HTML first; ARIA only where semantics fall short.
- Visible focus indicators, sufficient color contrast, full keyboard operability.
- Screen-reader tested for any custom/interactive component (modals, dropdowns, tabs).

## Dashboards & Data
- Use clear, understandable charts and graphs — don't force data into a chart type that obscures it.
- Always handle empty states (no data found, empty list) — never show a blank area with no explanation.
- Avoid excessive animations — they cause lag on low-end devices.

## Visual Design
- Consistent typography scale, border radius, and shadow usage — no one-off values.
- Color hierarchy matches meaning (e.g. status colors mean the same thing everywhere in the app).
- Define the palette, typography scale, and shadow/elevation system as CSS custom properties (tokens) up front — new colors or spacing values should be added to the token set, not invented inline in a component.
- Minimum touch target size of 36px for interactive elements, especially where the primary users are non-technical or on shared/lower-spec devices.

## Rich Text / WYSIWYG Content
- Sanitize rich-text editor output before storing or rendering — editors (Quill, TipTap, etc.) often emit flat/non-semantic markup (e.g. a `data-list` attribute instead of real `<ul>`/`<ol>`) that needs converting to semantic HTML on save or render.
- Never render raw editor output with an unescaped/raw-HTML directive without passing it through a sanitizer first (see `06-security-guide.md` — Input Sanitization).

## Review Questions (ask these, don't assume the current UI passes)
- Would a first-time user understand this without explanation?
- Is the next action obvious?
- Can this be simplified — fewer steps, fewer fields, fewer clicks?
- Is anything here visually distracting or competing for attention?

## Priority Order When Trading Off
1. Usability
2. Clarity
3. Consistency
4. Accessibility
5. Responsiveness
6. Maintainability
7. Performance