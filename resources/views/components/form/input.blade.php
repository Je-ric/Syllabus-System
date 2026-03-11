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
            w-full rounded-lg border border-slate-300 bg-white
            px-2.5 py-1.5 text-sm text-slate-700 shadow-sm
            placeholder:text-slate-400
            hover:border-slate-400
            focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none
            disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed disabled:border-slate-200
            transition-colors duration-150
        '
    ]) }}
/>
