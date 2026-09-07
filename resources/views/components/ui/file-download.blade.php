@props(['href', 'format', 'label'])
@php
    $isHtml = $format === 'html';
    $color = $isHtml ? '#c2410c' : '#b91c1c';
    $background = $isHtml ? '#fff7ed' : '#fef2f2';
    $border = $isHtml ? '#fdba74' : '#fca5a5';
@endphp
<a href="{{ $href }}" title="{{ $label }}" aria-label="{{ $label }}"
    {{ $attributes->merge(['class' => 'syllabus-file-download']) }}
    style="display:inline-flex;align-items:center;justify-content:center;flex:0 0 40px;width:40px;height:40px;box-sizing:border-box;padding:6px;border:1px solid {{ $border }};border-radius:8px;background:{{ $background }};color:{{ $color }};text-decoration:none;">
    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="28" viewBox="0 0 28 30" aria-hidden="true" focusable="false" style="display:block;flex-shrink:0;">
        <path d="M5 2h12l6 6v19H5z" fill="white" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        <path d="M17 2v6h6" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        @if ($isHtml)
            <path d="m11 11-3 3 3 3m6-6 3 3-3 3m-2-7-2 8" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        @else
            <path d="M14 10v8m-3-3 3 3 3-3" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        @endif
        <rect x="1" y="20" width="26" height="9" rx="2" fill="currentColor"/>
        <text x="14" y="26.7" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="7" font-weight="700">{{ $isHtml ? 'HTML' : 'PDF' }}</text>
    </svg>
</a>
