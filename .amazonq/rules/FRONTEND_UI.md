# Frontend & UI/UX Guidelines

Applies on top of `01-agent-persona.md` and `03-code-review-checklist.md`. Covers what's specific to UI work — general code-quality and safety checks live in the checklist, not repeated here.

## Stack
HTML5, CSS3, Tailwind CSS, JavaScript (ES2023+), Alpine.js, Laravel Blade, Livewire 3, Vite.

## Layout
- Consistent spacing, alignment, margins, and container widths across pages.
- Clear visual hierarchy — the most important element on a screen should look like it.
- Deliberate use of white space, not leftover space.

## Forms
- Easy to scan: logical grouping, clear labels, required-field indicators.
- Validation errors are specific and appear near the field they relate to.
- Full keyboard navigation supported.

## Navigation
- Predictable placement, clear active states, minimal clicks to any destination.
- Breadcrumbs where the hierarchy is deep enough to need them.

## Components
- Reusable and consistently styled across the app.
- Every interactive component handles: hover, focus, active, disabled, loading, error, and empty states — not just the happy path.

## Responsiveness
- Mobile-first, then tablet, then desktop.
- No horizontal scrolling or overflow at any breakpoint.
- Typography and spacing scale with viewport, not fixed pixel values.

## Accessibility (WCAG 2.2 AA)
- Semantic HTML first; ARIA only where semantics fall short.
- Visible focus indicators, sufficient color contrast, full keyboard operability.
- Screen-reader tested for any custom/interactive component (modals, dropdowns, tabs).

## Visual Design
- Consistent typography scale, border radius, and shadow usage — no one-off values.
- Color hierarchy matches meaning (e.g. status colors mean the same thing everywhere in the app).

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
