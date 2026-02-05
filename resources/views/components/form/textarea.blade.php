@props([
    'rows' => 3,
    'placeholder' => '',
])

<textarea
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => '
            w-full
            border border-green-300
            rounded-md
            px-4 py-2
            text-sm text-gray-800
            bg-green-50/40
            focus:outline-none
            focus:ring-1 focus:ring-green-600
            focus:border-green-600
            transition
        '
    ]) }}
></textarea>
