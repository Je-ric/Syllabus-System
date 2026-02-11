<style>
select option {
    background: white;
    color: #334155; /* slate-700 */
}</style>

@props([
    'name',
])

<select
    name="{{ $name }}"
    {{ $attributes->merge([
        'class' => '
            w-full
            rounded-xl border border-slate-300 bg-white/90
            px-4 py-2.5 text-sm text-slate-700
            shadow-sm focus:border-emerald-500
            focus:ring-2 focus:ring-emerald-200
            transition duration-150 ease-in-out

            disabled:bg-gray-100
            disabled:text-gray-500
            disabled:cursor-not-allowed
        '
    ]) }}
>
    {{ $slot }}
</select>

{{--

<x-form.select name="year_level" class="mt-2">
    <option value="">Select Year</option>
    <option value="1">Year 1</option>
    <option value="2">Year 2</option>
</x-form.select>

--}}
