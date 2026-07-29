{{--
    nav-link — Reusable navigation link component with icon, label, and active state.

    Props:
        href (required)  — route URL
        icon (required)  — Boxicon class (e.g. 'bx-grid-alt')
        active (bool)    — whether this link is the current page
        label (slot)     — link text

    Usage:
        <x-ui.nav-link href="{{ route('dashboard') }}" icon="bx-grid-alt" :active="request()->routeIs('dashboard')">
            Dashboard
        </x-ui.nav-link>
--}}

@props([
    'href' => '#',
    'icon' => 'bx-circle',
    'active' => false,
])

<a href="{{ $href }}"
   class="nav-link {{ $active ? 'active' : '' }}"
   {{ $attributes }}>
    <i class="bx {{ $icon }} nav-icon" aria-hidden="true"></i>
    {{ $slot }}
</a>
