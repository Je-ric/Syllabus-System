@props([
    'label' => null,
    'checked' => false,
])

<label {{ $attributes->only('class')->class('inline-flex items-center gap-2 cursor-pointer text-sm text-slate-700') }}>
    <input
        type="checkbox"
        @checked($checked)
        {{ $attributes->except('class') }}
        class="h-4 w-4 rounded border-slate-300 text-green-600 focus:ring-green-500"
    >

    @if($label || trim($slot))
        <span>{{ $label ?: $slot }}</span>
    @endif
</label>
