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
            rounded-xl border border-slate-300 bg-white/90
            px-4 py-2.5 text-sm text-slate-700 shadow-sm
            focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition
        '
    ]) }}
></textarea>
