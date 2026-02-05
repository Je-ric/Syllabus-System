@props([
    'type' => 'text',
])

<input
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => '
            w-full
            border border-green-300
            rounded-md
            px-4 py-2
            text-sm text-gray-800
            bg-white

            placeholder-gray-400

            focus:outline-none
            focus:border-green-600
            focus:ring-1
            focus:ring-green-600

            disabled:bg-gray-100
            disabled:text-gray-500
            disabled:cursor-not-allowed

            transition duration-150 ease-in-out
        '
    ]) }}
/>
