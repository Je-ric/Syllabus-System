@props([
    'type' => 'text',
])

{{--
    x-form.input
    ─────────────────────────────────────────────────────────────────────
    Unified with x-form.select / x-form.textarea.
    Padding: px-3 py-2 — all three form controls share the same height.

    USAGE:
      <x-form.input wire:model="name" placeholder="Enter name…" />
      <x-form.input type="email" wire:model="email" />
      <x-form.input type="number" min="0" max="100" />
--}}
<input
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => '
            w-full rounded-lg border border-slate-300 bg-white
            px-3 py-2 text-sm text-slate-700 shadow-sm
            placeholder:text-slate-400
            hover:border-slate-400
            focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none
            disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed disabled:border-slate-200
            transition-colors duration-150
        '
    ]) }}
/>
