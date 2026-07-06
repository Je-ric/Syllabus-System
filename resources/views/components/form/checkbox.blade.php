@props([
    'label'   => null,
    'checked' => false,
])

<label {{ $attributes->only('class')->class([
    'inline-flex items-center gap-2 cursor-pointer select-none',
    'text-sm text-[#18181b]',
]) }}>
    <input
        type="checkbox"
        @checked($checked)
        {{ $attributes->except('class') }}
        class="h-4 w-4 rounded-[4px] border-[#d4d4d8] text-[#16a34a]
               focus:ring-2 focus:ring-[#16a34a]/20 focus:ring-offset-0
               transition-colors"
    >

    @if($label || $slot->isNotEmpty())
        <span class="leading-snug">{{ $label ?: $slot }}</span>
    @endif
</label>
