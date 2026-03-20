@props([
    'label'   => null,
    'checked' => false,
])

{{--
    x-form.checkbox
    ─────────────────────────────────────────────────────────────────────
    USAGE:
      <x-form.checkbox wire:model="active" label="Active" />
      <x-form.checkbox name="agree" :checked="true">I agree to the terms</x-form.checkbox>
--}}
<label {{ $attributes->only('class')->class([
    'inline-flex items-center gap-2 cursor-pointer select-none',
    'text-sm text-slate-700',
]) }}>
    <input
        type="checkbox"
        @checked($checked)
        {{ $attributes->except('class') }}
        class="h-4 w-4 rounded border-slate-300 text-emerald-600
               focus:ring-2 focus:ring-emerald-300 focus:ring-offset-0
               transition-colors"
    >

    @if($label || $slot->isNotEmpty())
        <span class="leading-snug">{{ $label ?: $slot }}</span>
    @endif
</label>
