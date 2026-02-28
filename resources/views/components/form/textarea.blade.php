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
            rounded-md border border-gray-200 bg-white
            px-4 py-2.5 text-sm text-gray-700 shadow-xs
            focus:border-blue-500 focus:ring-1 focus:ring-blue-200
            focus:outline-none
            disabled:bg-gray-50
            disabled:text-gray-400
            disabled:cursor-not-allowed
            transition duration-100 ease-in-out
        '
    ]) }}
></textarea>
