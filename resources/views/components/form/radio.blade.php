@props([
    'name',
    'options' => [],
    'value' => null,
    'inline' => true,
    'disabled' => false,
])

<div {{ $attributes->class([$inline ? 'flex flex-wrap gap-4' : 'space-y-2', 'mt-2', $disabled ? 'opacity-50 pointer-events-none select-none' : '']) }}>
    @foreach($options as $optionValue => $optionLabel)
        <label class="inline-flex items-center gap-2 text-sm text-slate-700 {{ $disabled ? 'cursor-not-allowed' : 'cursor-pointer' }}">
            <input
                type="radio"
                name="{{ $name }}"
                value="{{ $optionValue }}"
                @checked((string) $value === (string) $optionValue)
                @disabled($disabled)
                class="h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-500"
            >
            <span>{{ $optionLabel }}</span>
        </label>
    @endforeach

    {{ $slot }}
</div>
