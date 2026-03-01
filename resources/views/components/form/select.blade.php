@props([
    'name' => null,
])

{{--
    x-form.select — unified with input/textarea
    Custom chevron via bg-[url()] SVG so appearance-none works cross-browser
--}}
<select
    name="{{ $name }}"
    {{ $attributes->merge([
        'class' => '
            w-full appearance-none rounded-xl border border-slate-300 bg-gray-100
            px-4 py-2.5 pr-9 text-sm text-slate-700 shadow-sm
            hover:border-slate-400
            focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 focus:outline-none
            disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed disabled:border-slate-200
            transition-colors duration-150
            bg-[url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 24 24\'%3E%3Cpath fill=\'%2394a3b8\' d=\'M7 10l5 5 5-5z\'/%3E%3C/svg%3E")]
            bg-no-repeat bg-[right_0.75rem_center]
        '
    ]) }}
>
    {{ $slot }}
</select>

{{--
Usage:
<x-form.select name="type" class="mt-2">
    <option value="">Select…</option>
    <option value="exam">Exam</option>
</x-form.select>
--}}
