{{--
    user-avatar — Reusable user avatar circle with initials fallback and size variants.
    Used in the sidebar user card, topbar profile pill, and anywhere else a user is shown.

    Props:
        name  (string) — user's display name, used to derive initials
        size  (string) — 'sm' (w-7 h-7) | 'md' (w-8 h-8, default) | 'lg' (w-10 h-10)

    Usage:
        <x-ui.user-avatar :name="$user->name" size="md" />
--}}

@props([
    'name' => '',
    'size' => 'md',
])

@php
    // Derive up to 2 initials from the display name
    $parts    = preg_split('/\s+/', trim($name));
    $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1));

    $sizeClasses = match ($size) {
        'sm'    => 'w-7 h-7 text-[10px]',
        'lg'    => 'w-10 h-10 text-[14px]',
        default => 'w-8 h-8 text-[11px]',   // 'md'
    };
@endphp

<div {{ $attributes->merge([
        'class' => "rounded-full bg-[#EDFFF8] border border-[#AEFFE2]
                    flex items-center justify-center shrink-0 font-semibold
                    text-[#06754E] select-none {$sizeClasses}"
     ]) }}
     aria-hidden="true">
    @if ($initials)
        {{ $initials }}
    @else
        <i class="bx bxs-user text-[#06754E]"></i>
    @endif
</div>
