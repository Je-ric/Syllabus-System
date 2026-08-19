{{--
    brand-logo — CLSU logo + CSMS wordmark, stacked vertical lockup.

    Props:
        href      (string)  — wrapping link target; defaults to dashboard route
        showText  (bool)    — show the "CSMS" wordmark below the logo (default: true)
        imgSize   (string)  — Tailwind size class for the image (default: 'w-14 h-14')

    Usage:
        <x-ui.brand-logo />
        <x-ui.brand-logo href="#" :show-text="false" img-size="w-16 h-16" />
--}}

@props([
    'href'     => null,
    'showText' => true,
    'imgSize'  => 'w-14 h-14',
])

@php $resolvedHref = $href ?? (Route::has('dashboard') ? route('dashboard') : '#'); @endphp

<a href="{{ $resolvedHref }}" class="brand-logo-link" {{ $attributes }}>

    {{-- Logo mark with glow --}}
    <div class="brand-logo-glow-wrap">
        <img src="{{ asset('assets/CLSU-LOGO-removebg.png') }}"
             alt="CLSU Logo"
             class="{{ $imgSize }} object-contain brand-logo-img">
    </div>

    @if ($showText)
        <div class="brand-text-block">
            <span class="brand-acronym">CSMS</span>
            <span class="brand-tagline">Syllabus Management System</span>
        </div>
    @endif

</a>
