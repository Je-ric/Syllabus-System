@props([
    'href'       => null,
    'type'       => 'button',
    'variant'    => 'primary',
    'loading'    => null,
    'wireTarget' => null,
])

@php
    $iconFix = '[&_i]:leading-none [&_i]:text-[1.1em] [&_i]:translate-y-px ';

    $tableBtn  = 'inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-[10px]
                  transition-all duration-150
                  disabled:opacity-40 disabled:pointer-events-none ' . $iconFix;

    $formBtn   = 'inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-[14px]
                  transition-all duration-150 active:scale-[0.97]
                  focus:ring-2 focus:outline-none
                  disabled:opacity-40 disabled:pointer-events-none ' . $iconFix;

    $wizardBtn = 'inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-[10px]
                  transition-all duration-150
                  disabled:opacity-40 disabled:pointer-events-none ' . $iconFix;

    $styles = [

        // ── Table buttons ──────────────────────────────────────────────────────
        'table-confirm'  => $tableBtn . 'text-white bg-[#16a34a] hover:bg-[#15803d] shadow-sm',
        'table-edit'     => $tableBtn . 'text-white bg-[#2563eb] hover:bg-[#1d4ed8] shadow-sm',
        'table-view'     => $tableBtn . 'text-white bg-[#0891b2] hover:bg-[#0e7490] shadow-sm',
        'table-manage'   => $tableBtn . 'text-white bg-[#52525b] hover:bg-[#3f3f46] shadow-sm',
        'table-danger'   => $tableBtn . 'text-white bg-[#e11d48] hover:bg-[#be123c] shadow-sm',
        'table-disable'  => $tableBtn . 'text-white bg-[#71717a] hover:bg-[#52525b] shadow-sm',
        'table-restore'  => $tableBtn . 'text-white bg-[#7c3aed] hover:bg-[#6d28d9] shadow-sm',
        'table-cancel'   => $tableBtn . 'bg-white text-[#3f3f46] border border-[#d4d4d8]
                                         hover:bg-[#f4f4f5] hover:border-[#a1a1aa]',

        // ── Form / CRUD buttons ────────────────────────────────────────────────
        'primary'    => $formBtn . '
            bg-[#09090b] text-white
            hover:bg-[#18181b]
            focus:ring-[#3f3f46]/40
            shadow-[inset_0_0.5px_0_0_rgba(255,255,255,0.08),0_1px_3px_rgba(0,0,0,0.3)]',

        'secondary'  => $formBtn . '
            bg-[#ffd700] text-[#09090b]
            hover:bg-[#f5ce00]
            focus:ring-[#ffd700]/40
            shadow-sm',

        'outline'    => $formBtn . '
            bg-white text-[#166534]
            hover:bg-[#f0fdf4] hover:border-[#15803d]
            focus:ring-[#16a34a]/25',

        'add-button' => $formBtn . '
            bg-[#16a34a] text-white
            hover:bg-[#15803d]
            focus:ring-[#16a34a]/30
            shadow-sm',

        'save' => $formBtn . '
            bg-[#16a34a] text-white
            hover:bg-[#15803d]
            focus:ring-[#16a34a]/30
            shadow-sm',

        'add-dashed' => $formBtn . '
            justify-center bg-white text-[#16a34a]
            border-2 border-dashed border-[#86efac]
            hover:border-[#16a34a] hover:bg-[#f0fdf4]
            focus:ring-[#16a34a]/20',

        'cancel'     => $formBtn . '
            bg-white text-[#3f3f46]
            border border-[#d4d4d8]
            hover:bg-[#f4f4f5] hover:border-[#a1a1aa]
            focus:ring-[#a1a1aa]/20',

        'back'       => $formBtn . '
            bg-white text-[#3f3f46]
            border border-[#d4d4d8]
            hover:bg-[#f4f4f5] hover:border-[#a1a1aa]
            focus:ring-[#a1a1aa]/20',

        'danger'     => $formBtn . '
            bg-[#e11d48] text-white
            border border-[#be123c]
            hover:bg-[#be123c]
            focus:ring-[#e11d48]/30
            shadow-sm',

        'warning'    => $formBtn . '
            bg-[#d97706] text-white
            hover:bg-[#b45309]
            focus:ring-[#d97706]/30
            shadow-sm',

        // ── Small / wizard buttons ─────────────────────────────────────────────
        'sm-primary' => $wizardBtn . 'bg-[#f0fdf4] text-[#166534] border border-[#86efac]
                                       hover:bg-[#dcfce7] hover:border-[#4ade80]',
        'sm-cancel'  => $wizardBtn . 'bg-white text-[#3f3f46] border border-[#d4d4d8]
                                       hover:bg-[#f4f4f5] hover:border-[#a1a1aa]',
        'sm-danger'  => $wizardBtn . 'bg-[#e11d48] text-white border border-[#be123c]
                                       hover:bg-[#be123c] shadow-sm',
        'sm-warning' => $wizardBtn . 'bg-[#fffbeb] text-[#92400e] border border-[#fcd34d]
                                       hover:bg-[#fef3c7] hover:border-[#f59e0b]',
        'sm-info'    => $wizardBtn . 'bg-[#eff6ff] text-[#1e40af] border border-[#bfdbfe]
                                       hover:bg-[#dbeafe] hover:border-[#93c5fd]',
        'sm-success' => $wizardBtn . 'bg-[#f0fdf4] text-[#166534] border border-[#86efac]
                                       hover:bg-[#dcfce7] hover:border-[#4ade80]',
        'sm-soft'    => $wizardBtn . 'bg-[#f7fee7] text-[#3f6212] border border-[#bef264]
                                       hover:bg-[#ecfccb] hover:border-[#a3e635]',
        'sm-add'     => $wizardBtn . 'bg-[#16a34a] text-white border border-[#15803d]
                                       hover:bg-[#15803d] shadow-sm
                                       focus:ring-2 focus:ring-[#16a34a]/30',
    ];

    $class = $styles[strval($variant)] ?? $styles['primary'];

    $attributeKeys = array_keys($attributes->getAttributes());
    $wireClickKey = null;
    foreach ($attributeKeys as $key) {
        if (str_starts_with($key, 'wire:click')) {
            $wireClickKey = $key;
            break;
        }
    }
    $wireClickValue = $wireClickKey ? $attributes->get($wireClickKey) : null;

    $existingTargetAttr = $attributes->get('wire:target');
    $parsedTarget = null;
    if (is_string($wireClickValue) && preg_match('/^\s*([A-Za-z_][A-Za-z0-9_]*)\s*(?:\(|$)/', $wireClickValue, $m)) {
        $parsedTarget = $m[1];
    }

    $autoTarget   = $wireTarget ?: $parsedTarget;
    $spinnerTarget = $autoTarget ?: $existingTargetAttr;
    $shouldHandleLoading = filled($loading) || filled($spinnerTarget);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge(['class' => $class]) }}
        @if ($shouldHandleLoading && !$attributes->has('wire:loading.attr')) wire:loading.attr="disabled" @endif
        @if ($shouldHandleLoading && $autoTarget && !$attributes->has('wire:target')) wire:target="{{ $autoTarget }}" @endif>

        @if ($shouldHandleLoading)
            @if (filled($loading))
                <span wire:loading.remove @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                      class="inline-flex items-center gap-1.5 leading-none">{{ $slot }}</span>
                <span wire:loading @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                      class="inline-flex items-center gap-1.5 leading-none">
                    <svg class="animate-spin h-3.5 w-3.5 shrink-0 leading-none" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="leading-none">{{ $loading }}</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 leading-none">
                    <span class="inline-flex items-center gap-1.5 leading-none">{{ $slot }}</span>
                    <svg wire:loading @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                         class="animate-spin h-3.5 w-3.5 shrink-0 leading-none" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
            @endif
        @else
            <span class="inline-flex items-center gap-1.5 leading-none">{{ $slot }}</span>
        @endif
    </button>
@endif
