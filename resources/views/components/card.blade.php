@props([
    'title'   => null,
    'icon'    => null,
    'color'   => 'slate',   // slate | emerald | blue | amber | rose | violet
    'padding' => true,      // false = no body padding (for tables, full-bleed)
    'shadow'  => true,
])

@php
    $palette = [
        'slate'   => ['strip' => 'bg-slate-50 border-slate-100',       'icon' => 'bg-slate-100 text-slate-500',       'title' => 'text-slate-700'],
        'emerald' => ['strip' => 'bg-emerald-50 border-emerald-100',   'icon' => 'bg-emerald-100 text-emerald-700',   'title' => 'text-emerald-900'],
        'blue'    => ['strip' => 'bg-blue-50 border-blue-100',         'icon' => 'bg-blue-100 text-blue-700',         'title' => 'text-blue-900'],
        'amber'   => ['strip' => 'bg-amber-50 border-amber-100',       'icon' => 'bg-amber-100 text-amber-700',       'title' => 'text-amber-900'],
        'rose'    => ['strip' => 'bg-rose-50 border-rose-100',         'icon' => 'bg-rose-100 text-rose-700',         'title' => 'text-rose-900'],
        'violet'  => ['strip' => 'bg-violet-50 border-violet-100',     'icon' => 'bg-violet-100 text-violet-700',     'title' => 'text-violet-900'],
        // CLSU brand: dark navy strip — complements the green-grad page/card headers
        'navy'    => ['strip' => 'bg-[#1a2235] border-[#1a2235]',      'icon' => 'bg-white/10 text-[#ffb51b]',        'title' => 'text-white'],
        // Soft gold tint — for secondary info sections beside navy
        'gold'    => ['strip' => 'bg-[#fffbeb] border-[#fde68a]',      'icon' => 'bg-[#fef3c7] text-[#92400e]',      'title' => 'text-[#78350f]'],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

{{--
    x-card
    ─────────────────────────────────────────────────────────────────────
    Generic white card. Use for grouping related content.
    For wizard steps, prefer x-wizard.section (has coloured header strip).

    USAGE:
      <x-card title="Instructor Profile" icon="user" color="emerald">
          …
      </x-card>

      <x-card title="References">
          <x-slot name="action">
              <x-button variant="add-button">Add</x-button>
          </x-slot>
          …
      </x-card>

      <x-card title="Results" :padding="false">
          <table>…</table>
      </x-card>
--}}

<div {{ $attributes->class([
    'rounded-xl border border-slate-200 bg-white overflow-hidden',
    'shadow-sm' => $shadow,
]) }}>

    @if ($title)
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b {{ $p['strip'] }}">
            <div class="flex items-center gap-2.5 min-w-0">
                @if ($icon)
                    <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-lg {{ $p['icon'] }}">
                        <i class="bx bx-{{ $icon }} text-sm leading-none"></i>
                    </span>
                @endif
                <h4 class="text-sm font-semibold {{ $p['title'] }} truncate">{{ $title }}</h4>
            </div>

            @if (isset($action) && $action->isNotEmpty())
                <div class="shrink-0 flex items-center gap-2">{{ $action }}</div>
            @endif
        </div>
    @endif

    <div class="{{ $padding ? 'p-4' : '' }}">
        {{ $slot }}
    </div>

</div>
