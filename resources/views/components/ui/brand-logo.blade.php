{{--
    brand-logo — Reusable CLSU logo + wordmark lockup.
    Used in the sidebar, auth pages, email templates, and any other branding context.

    Props:
        href      (string)  — wrapping link target; defaults to dashboard route
        showText  (bool)    — show the "C.S.M.S." wordmark next to the logo (default: true)
        imgSize   (string)  — Tailwind size class for the image (default: 'w-9 h-9')

    Usage:
        <x-ui.brand-logo />

        <x-ui.brand-logo href="#" :show-text="false" img-size="w-12 h-12" />
--}}

@props([
    'href'     => null,
    'showText' => true,
    'imgSize'  => 'w-9 h-9',
])

@php $resolvedHref = $href ?? (Route::has('dashboard') ? route('dashboard') : '#'); @endphp

<a href="{{ $resolvedHref }}" class="flex items-center gap-2.5" {{ $attributes }}>
    <div class="{{ $imgSize }} flex items-center justify-center shrink-0">
        <img src="{{ asset('assets/CLSU-LOGO-removebg.png') }}"
             alt="CLSU Logo"
             class="{{ $imgSize }} object-contain">
    </div>
    @if ($showText)
        <h1 class="brand-title text-2xl leading-tight text-[#09090b] font-bold">C.S.M.S.</h1>
    @endif
</a>
