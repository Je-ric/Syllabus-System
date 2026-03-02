@props([
    'align' => 'end',
])

@php
    $alignmentClasses = [
        'start' => 'justify-start',
        'center' => 'justify-center',
        'end' => 'justify-end',
    ];

    $alignmentClass = $alignmentClasses[$align] ?? $alignmentClasses['end'];
@endphp

<footer {{ $attributes->class([
    'border-t border-slate-200 px-6 py-4 flex gap-3 flex-shrink-0',
    $alignmentClass,
]) }}>
    {{ $slot }}
</footer>

{{--
Usage: <x-modal.footer align="end">
            <x-modal.close-button :modalId="'myModal'" text="Cancel" />
            <button type="submit" class="btn btn-primary">Save</button>
        </x-modal.footer>
       <x-modal.footer align="center">
            <button type="button">OK</button>
        </x-modal.footer>

--}}
