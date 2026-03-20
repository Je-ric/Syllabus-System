@props([
    'title',
    'icon'         => null,
    'open'         => false,
    'color'        => 'slate',   // slate | emerald | blue | amber | rose
    'badge'        => null,      // optional pill text shown in header
    'badgeVariant' => 'slate',   // status-indicator variant for the badge
    'noPadding'    => false,     // skip body padding (for tables, full-bleed content)
])

@php
    $palette = [
        'slate'   => ['icon_bg' => 'bg-slate-100 text-slate-500',   'title' => 'text-slate-800'],
        'emerald' => ['icon_bg' => 'bg-emerald-100 text-emerald-600','title' => 'text-emerald-900'],
        'blue'    => ['icon_bg' => 'bg-blue-100 text-blue-600',      'title' => 'text-blue-900'],
        'amber'   => ['icon_bg' => 'bg-amber-100 text-amber-600',    'title' => 'text-amber-900'],
        'rose'    => ['icon_bg' => 'bg-rose-100 text-rose-600',      'title' => 'text-rose-900'],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

{{--
    x-accordion
    ─────────────────────────────────────────────────────────────────────
    Pure Alpine accordion. Safe to use inside or outside Livewire — no
    wire:loading or $wire calls. Collapses/expands with x-collapse.

    USAGE:
      <x-accordion title="Revision History" icon="history" :open="true">
          <p>Body content here.</p>
      </x-accordion>

      <x-accordion title="Reviewers" icon="user-check" color="blue"
                   badge="3 reviewers" badgeVariant="blue">
          …
      </x-accordion>

      {{-- Full-bleed (e.g. a table inside) --}}
      {{-- <x-accordion title="Evaluation Items" :noPadding="true">
          <table>…</table>
      </x-accordion>

    NAMED SLOT — extra header actions (buttons etc.):
      <x-accordion title="Notes">
          <x-slot name="actions">
              <x-button variant="sm-primary">Edit</x-button>
          </x-slot>
          Body…
      </x-accordion> --}}


<div
    x-data="{ open: {{ $open ? 'true' : 'false' }} }"
    {{ $attributes->class([
        'rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden',
    ]) }}
>
    {{-- Header ──────────────────────────────────────────────────────────── --}}
    <button
        type="button"
        x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left
               hover:bg-slate-50 transition-colors duration-100 focus:outline-none
               focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-inset"
        :aria-expanded="open"
    >
        <div class="flex items-center gap-3 min-w-0">
            @if ($icon)
                <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-lg {{ $p['icon_bg'] }}">
                    <i class="bx bx-{{ $icon }} text-base leading-none"></i>
                </span>
            @endif

            <div class="min-w-0">
                <p class="text-sm font-semibold {{ $p['title'] }} leading-snug">{{ $title }}</p>

                @if ($slot->hasNamedSlot('subtitle') ?? false)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>

            @if ($badge)
                <x-feedback-status.status-indicator :variant="$badgeVariant" class="ml-2 shrink-0">
                    {{ $badge }}
                </x-feedback-status.status-indicator>
            @endif
        </div>

        <div class="flex items-center gap-2 shrink-0 ml-3">
            @if (isset($actions) && $actions->isNotEmpty())
                <div x-on:click.stop class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif

            <i class="bx text-slate-400 text-xl transition-transform duration-200"
               :class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
        </div>
    </button>

    {{-- Body ────────────────────────────────────────────────────────────── --}}
    <div x-show="open" x-collapse x-cloak>
        <div class="border-t border-slate-100 {{ $noPadding ? '' : 'p-5' }}">
            {{ $slot }}
        </div>
    </div>
</div>
