@props([
    'type' => 'text',
])

<input
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => '
            w-full
            rounded-lg border border-blue-300 bg-white/90
            px-4 py-2.5 text-sm text-slate-700 shadow-sm
            focus:border-blue-500 focus:ring-2 focus:ring-blue-200
            transform focus:-translate-y-1
            focus:outline-green-300 hover:shadow-lg hover:border-green-300

            disabled:bg-slate-100
            disabled:text-slate-500
            disabled:cursor-not-allowed

            transition duration-150 ease-in-out
        '
    ]) }}
/>

{{-- Usage:
handles: text, numbers, date

<x-form.input type="text" name="name" class="mt-2" value="{{ old('name', $course->course_title ?? '') }}" required>
<x-form.input type="date" name="start_date" class="mt-2" />


--}}
