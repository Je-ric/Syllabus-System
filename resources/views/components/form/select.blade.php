@props([
    'name' => null,
])

{{--
    x-form.select
    ─────────────────────────────────────────────────────────────────────
    Unified with x-form.input / x-form.textarea.
    Padding: px-3 py-2 — matches input/textarea exactly so heights align
    when placed side-by-side.

    The chevron arrow is a wrapper div approach (more reliable than
    bg-[url()] SVG data URIs which break in some Tailwind JIT builds).

    USAGE:
      <x-form.select wire:model="type">
          <option value="">Select…</option>
          <option value="exam">Exam</option>
      </x-form.select>
--}}

<div class="relative">
    <select
        name="{{ $name }}"
        {{ $attributes->merge([
            'class' => '
                w-full appearance-none rounded-lg border border-slate-300 bg-white
                px-3 py-2 pr-9 text-sm text-slate-700 shadow-sm
                hover:border-slate-400
                focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none
                disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed disabled:border-slate-200
                transition-colors duration-150
            '
        ]) }}
    >
        {{ $slot }}
    </select>

    {{-- Chevron icon — pointer-events-none so clicks pass through to select --}}
    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
        <i class="bx bx-chevron-down text-base leading-none"></i>
    </span>
</div>
