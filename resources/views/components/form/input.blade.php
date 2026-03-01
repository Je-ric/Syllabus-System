@props([
    'type' => 'text',
])

{{--
    x-form.input — unified with textarea/select
    Theme: slate border, emerald focus, no quirky hover transforms
    Works for: text, number, email, password, date, url, tel
--}}
<input
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => '
            w-full rounded-xl border border-slate-300 bg-gray-100
            px-4 py-2.5 text-sm text-slate-700 shadow-sm
            placeholder:text-slate-400
            hover:border-slate-400
            focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 focus:outline-none
            disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed disabled:border-slate-200
            transition-colors duration-150
        '
    ]) }}
/>
