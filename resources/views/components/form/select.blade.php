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
            w-full appearance-none rounded-lg border border-slate-300 bg-white
            px-4 py-2.5 pr-10 text-sm text-slate-700 shadow-sm
            hover:border-slate-400
            focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none
            disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed disabled:border-slate-200
            transition-colors duration-150
            bg-[url(\'data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2716%27 height=%2716%27 viewBox=%270 0 24 24%27%3E%3Cpath fill=%27%23475569%27 d=%27M7 10l5 5 5-5z%27/%3E%3C/svg%3E\')]
            bg-no-repeat
            bg-[right_0.75rem_center]
            bg-[length:1rem_1rem]
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
