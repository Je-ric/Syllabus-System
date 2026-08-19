@props([
    'href'        => null,
    'type'        => 'button',
    'variant'     => 'primary',
    'loading'     => null,
    'wireTarget'  => null,
    'submitting'  => null,
    'loadingText' => null,
])

@php
    $iconFix = '[&_i]:leading-none [&_i]:text-[1.1em] [&_i]:translate-y-px ';

    // Table buttons: min ~36px height
    $tableBtn  = 'inline-flex items-center gap-1.5 px-3 py-1.5 text-[12.5px] font-semibold rounded-[6px]
                  transition-all duration-200
                  focus:outline-none focus:ring-2
                  disabled:opacity-40 disabled:pointer-events-none ' . $iconFix;

    // Form / CRUD buttons: min ~42px height
    $formBtn   = 'inline-flex items-center gap-2 px-4 py-2 text-[13.5px] font-semibold rounded-[7px]
                  transition-all duration-200 active:scale-[0.97]
                  focus:ring-2 focus:outline-none
                  disabled:opacity-40 disabled:pointer-events-none ' . $iconFix;

    // Small / wizard buttons: compact
    $wizardBtn = 'inline-flex items-center gap-1.5 px-3 py-1.5 text-[12.5px] font-semibold rounded-[6px]
                  transition-all duration-200
                  focus:outline-none focus:ring-2
                  disabled:opacity-40 disabled:pointer-events-none ' . $iconFix;

    $styles = [

        // ── Table buttons ──────────────────────────────────────────────────────
        'table-confirm'  => $tableBtn . 'text-white bg-[linear-gradient(180deg,#00C075_0%,#00965F_100%)] hover:bg-[linear-gradient(180deg,#00965F_0%,#06754E_100%)] active:bg-[#06754E] focus:ring-[#00C075]/30 shadow-[0_1px_2px_rgba(0,150,95,0.3)]',
        'table-edit'     => $tableBtn . 'text-[#5D3A00] bg-[linear-gradient(180deg,#FFC646_0%,#F5B126_100%)] hover:bg-[linear-gradient(180deg,#F5B126_0%,#D79400_100%)] active:bg-[#D79400] focus:ring-[#FFC646]/30 shadow-[0_1px_2px_rgba(215,148,0,0.3)]',
        'table-view'     => $tableBtn . 'text-white bg-[linear-gradient(180deg,#3197D6_0%,#1F7AB8_100%)] hover:bg-[linear-gradient(180deg,#1F7AB8_0%,#194C6E_100%)] active:bg-[#194C6E] focus:ring-[#3197D6]/30 shadow-[0_1px_2px_rgba(49,151,214,0.3)]',
        'table-manage'   => $tableBtn . 'text-white bg-[linear-gradient(180deg,#5C6BC0_0%,#3F51B5_100%)] hover:bg-[linear-gradient(180deg,#3F51B5_0%,#303F9F_100%)] active:bg-[#303F9F] focus:ring-[#5C6BC0]/30 shadow-[0_1px_2px_rgba(63,81,181,0.3)]',
        'table-danger'   => $tableBtn . 'text-white bg-[linear-gradient(180deg,#E52F28_0%,#BA1F19_100%)] hover:bg-[linear-gradient(180deg,#D21B14_0%,#9D1F1A_100%)] active:bg-[#9D1F1A] focus:ring-[#E52F28]/30 shadow-[0_1px_2px_rgba(186,31,25,0.3)]',
        'table-disable'  => $tableBtn . 'text-white bg-[linear-gradient(180deg,#F97316_0%,#EA6C0A_100%)] hover:bg-[linear-gradient(180deg,#EA6C0A_0%,#C2570A_100%)] active:bg-[#C2570A] focus:ring-[#F97316]/30 shadow-[0_1px_2px_rgba(249,115,22,0.3)]',
        'table-restore'  => $tableBtn . 'text-[#5D3A00] bg-[linear-gradient(180deg,#FFD966_0%,#FFC646_100%)] hover:bg-[linear-gradient(180deg,#FFC646_0%,#F5B126_100%)] active:bg-[#F5B126] focus:ring-[#FFC646]/30 shadow-[0_1px_2px_rgba(215,148,0,0.3)]',
        'table-cancel'   => $tableBtn . 'bg-white text-[#394056] border border-[#D6DDE3] hover:bg-[#F1F3F5] hover:border-[#C1C8D4] focus:ring-[#D6DDE3]/30',

        'table-light-edit' => $tableBtn . 'p-2 rounded-lg text-[#A5B2BD] hover:text-[#1F7AB8] hover:bg-[#DAF1FF] focus:ring-[#3197D6]/25 transition-colors',
        'table-light-delete' => $tableBtn . 'p-2 rounded-lg text-[#A5B2BD] hover:text-[#9D1F1A] hover:bg-[#FFE3E2] focus:ring-[#E52F28]/25 transition-colors',

        // ── Form / CRUD buttons — gradient-based for visibility on white ───────
        'primary'    => $formBtn . 'text-white bg-[linear-gradient(180deg,#00C075_0%,#00965F_100%)] hover:bg-[linear-gradient(180deg,#00965F_0%,#06754E_100%)] active:bg-[#06754E] focus:ring-[#00C075]/40 shadow-[0_1px_3px_rgba(0,150,95,0.35)]',
        'secondary'  => $formBtn . 'text-white bg-[linear-gradient(180deg,#394056_0%,#253540_100%)] hover:bg-[linear-gradient(180deg,#2A3B47_0%,#1D2836_100%)] active:bg-[#1D2836] focus:ring-[#394056]/40 shadow-[0_1px_3px_rgba(37,53,64,0.35)]',
        'outline'    => $formBtn . 'bg-transparent text-[#00965F] border-[1.5px] border-[#00C075] hover:bg-[#D5FFF0] hover:border-[#00965F] hover:text-[#06754E] active:bg-[#AEFFE2] active:border-[#06754E] active:text-[#076042] focus:ring-[#00C075]/25',
        'add-button' => $formBtn . 'text-white bg-[linear-gradient(180deg,#00C075_0%,#00965F_100%)] hover:bg-[linear-gradient(180deg,#00965F_0%,#06754E_100%)] active:bg-[#06754E] focus:ring-[#00C075]/40 shadow-[0_1px_3px_rgba(0,150,95,0.35)]',
        'save'       => $formBtn . 'text-white bg-[linear-gradient(180deg,#00C075_0%,#00965F_100%)] hover:bg-[linear-gradient(180deg,#00965F_0%,#06754E_100%)] active:bg-[#06754E] focus:ring-[#00C075]/40 shadow-[0_1px_3px_rgba(0,150,95,0.35)]',
        'add-dashed' => $formBtn . 'justify-center bg-transparent text-[#00965F] border-2 border-dashed border-[#00C075] hover:border-[#00965F] hover:bg-[#D5FFF0] hover:text-[#06754E] active:bg-[#AEFFE2] focus:ring-[#00C075]/20',
        'cancel'     => $formBtn . 'bg-white text-[#394056] border border-[#D6DDE3] hover:bg-[#F1F3F5] hover:border-[#C1C8D4] focus:ring-[#D6DDE3]/20',
        'back'       => $formBtn . 'bg-white text-[#394056] border border-[#D6DDE3] hover:bg-[#F1F3F5] hover:border-[#C1C8D4] focus:ring-[#D6DDE3]/20',
        'danger'     => $formBtn . 'text-white bg-[linear-gradient(180deg,#E52F28_0%,#BA1F19_100%)] hover:bg-[linear-gradient(180deg,#D21B14_0%,#9D1F1A_100%)] active:bg-[#9D1F1A] focus:ring-[#E52F28]/35 shadow-[0_1px_3px_rgba(186,31,25,0.35)]',
        'warning'    => $formBtn . ' text-[#394056] bg-[linear-gradient(180deg,#FFC646_0%,#F5B126_100%)] hover:bg-[linear-gradient(180deg,#F5B126_0%,#D79400_100%)] active:bg-[#D79400] focus:ring-[#FFC646]/35 shadow-[0_1px_3px_rgba(215,148,0,0.30)]',
        'gold'       => $formBtn . ' text-[#394056] bg-[linear-gradient(180deg,#FFC646_0%,#F5B126_100%)] hover:bg-[linear-gradient(180deg,#F5B126_0%,#D79400_100%)] active:bg-[#D79400] focus:ring-[#FFC646]/35 shadow-[0_1px_3px_rgba(215,148,0,0.30)]',

        'sidebar-tool' => $wizardBtn . ' text-[#344054] bg-white border border-transparent hover:bg-[#F8FAFC] hover:border-[#E4E7EC] active:bg-[#EEF2F6] active:border-[#D0D5DD] focus:ring-[#D0D5DD]/40 shadow-none',

        // ── Small / wizard buttons ─────────────────────────────────────────────
        'sm-primary' => $wizardBtn . 'text-white bg-[linear-gradient(180deg,#00C075_0%,#00965F_100%)] hover:bg-[linear-gradient(180deg,#00965F_0%,#06754E_100%)] active:bg-[#06754E] focus:ring-[#00C075]/30 shadow-[0_1px_2px_rgba(0,150,95,0.3)]',
        'sm-cancel'  => $wizardBtn . 'bg-white text-[#394056] border border-[#D6DDE3] hover:bg-[#F1F3F5] hover:border-[#C1C8D4] focus:ring-[#D6DDE3]/20',
        'sm-danger'  => $wizardBtn . 'text-white bg-[linear-gradient(180deg,#E52F28_0%,#BA1F19_100%)] hover:bg-[linear-gradient(180deg,#D21B14_0%,#9D1F1A_100%)] active:bg-[#9D1F1A] focus:ring-[#E52F28]/30 shadow-[0_1px_2px_rgba(186,31,25,0.3)]',
        'sm-warning' => $wizardBtn . 'text-[#5D3A00] bg-[linear-gradient(180deg,#FFC646_0%,#F5B126_100%)] hover:bg-[linear-gradient(180deg,#F5B126_0%,#D79400_100%)] active:bg-[#D79400] focus:ring-[#FFC646]/30 shadow-[0_1px_2px_rgba(215,148,0,0.3)]',
        'sm-info'    => $wizardBtn . 'text-white bg-[linear-gradient(180deg,#3197D6_0%,#1F7AB8_100%)] hover:bg-[linear-gradient(180deg,#1F7AB8_0%,#194C6E_100%)] active:bg-[#194C6E] focus:ring-[#3197D6]/30 shadow-[0_1px_2px_rgba(49,151,214,0.3)]',
        'sm-success' => $wizardBtn . 'text-white bg-[linear-gradient(180deg,#00C075_0%,#00965F_100%)] hover:bg-[linear-gradient(180deg,#00965F_0%,#06754E_100%)] active:bg-[#06754E] focus:ring-[#00C075]/30 shadow-[0_1px_2px_rgba(0,150,95,0.3)]',
        'sm-add'     => $wizardBtn . 'text-white bg-[linear-gradient(180deg,#00C075_0%,#00965F_100%)] hover:bg-[linear-gradient(180deg,#00965F_0%,#06754E_100%)] active:bg-[#06754E] focus:ring-[#00C075]/30 shadow-[0_1px_2px_rgba(0,150,95,0.3)]',

        // ── Preview / open-in-new-tab buttons ─────────────────────────────────
        // Three distinct flavors — emerald, blue, slate — horizontal pill with left icon accent
        'preview-complete'    => $formBtn . 'bg-white text-[#06754E] border border-[#86efac]
                                             hover:bg-[#f0fdf4] hover:border-[#4ade80] hover:text-[#15803d]
                                             active:bg-[#dcfce7] focus:ring-[#16a34a]/20
                                             shadow-[0_1px_3px_rgba(0,150,95,0.12)]',
        'preview-abridged'    => $formBtn . 'bg-white text-[#1d4ed8] border border-[#bfdbfe]
                                             hover:bg-[#eff6ff] hover:border-[#93c5fd] hover:text-[#1e40af]
                                             active:bg-[#dbeafe] focus:ring-[#3b82f6]/20
                                             shadow-[0_1px_3px_rgba(59,130,246,0.12)]',
        'preview-assessment'  => $formBtn . 'bg-white text-[#334155] border border-[#cbd5e1]
                                             hover:bg-[#f8fafc] hover:border-[#94a3b8] hover:text-[#1e293b]
                                             active:bg-[#f1f5f9] focus:ring-[#64748b]/20
                                             shadow-[0_1px_3px_rgba(100,116,139,0.12)]',
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

    $autoTarget    = $wireTarget ?: $parsedTarget;
    $spinnerTarget = $autoTarget ?: $existingTargetAttr;
    $shouldHandleLoading = filled($loading) || filled($spinnerTarget);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@elseif ($submitting)
    <button type="{{ $type }}"
        {{ $attributes->merge(['class' => $class]) }}
        @if ($shouldHandleLoading && !$attributes->has('wire:loading.attr')) wire:loading.attr="disabled" @endif
        @if ($shouldHandleLoading && $autoTarget && !$attributes->has('wire:target')) wire:target="{{ $autoTarget }}" @endif>

        {{-- Alpine submitting state --}}
        <template x-if="!({{ $submitting }})">
            <span class="inline-flex items-center gap-1.5 leading-none">{{ $slot }}</span>
        </template>
        <template x-if="{{ $submitting }}">
            <span class="inline-flex items-center gap-1.5 leading-none">
                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ $loadingText ?? $slot }}
            </span>
        </template>

        @if ($shouldHandleLoading)
            @if (filled($loading))
                <span class="inline-flex items-center justify-center gap-1.5 leading-none">
                    <span wire:loading.remove @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                          class="inline-flex items-center gap-1.5 leading-none sr-only">{{ $slot }}</span>
                    <span wire:loading @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                          class="inline-flex items-center gap-1.5 leading-none sr-only">
                        <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span class="leading-none">{{ $loading }}</span>
                    </span>
                </span>
            @endif
        @endif
    </button>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge(['class' => $class]) }}
        @if ($shouldHandleLoading && !$attributes->has('wire:loading.attr')) wire:loading.attr="disabled" @endif
        @if ($shouldHandleLoading && $autoTarget && !$attributes->has('wire:target')) wire:target="{{ $autoTarget }}" @endif>

        @if ($shouldHandleLoading)
            @if (filled($loading))
                {{-- Outer container holds both states at fixed height to prevent layout shift --}}
                <span class="inline-flex items-center justify-center gap-1.5 leading-none">
                    <span wire:loading.remove @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                          class="inline-flex items-center gap-1.5 leading-none">{{ $slot }}</span>
                    <span wire:loading @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                          class="inline-flex items-center gap-1.5 leading-none">
                        <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span class="leading-none">{{ $loading }}</span>
                    </span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 leading-none">
                    {{ $slot }}
                    <svg wire:loading @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                         class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
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


<style>
    /* ── Button motion polish ─────────────────────────────────────────── */
    /* Fixes: gradient backgrounds don't crossfade with `transition-all`,
    and table-* buttons have no press/active feedback at all. */

    [class*="bg-[linear-gradient"] {
        position: relative;
        isolation: isolate;
        transition: transform 150ms cubic-bezier(0.34, 1.56, 0.64, 1),
                    box-shadow 200ms ease,
                    filter 200ms ease;
    }

    /* Crossfade trick: stack the hover gradient as a pseudo-element and
    fade its opacity in, instead of swapping background-image directly. */
    [class*="bg-[linear-gradient"]::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        border-radius: inherit;
        opacity: 0;
        transition: opacity 220ms ease;
        background: inherit;
        filter: brightness(0.93);
    }
    [class*="bg-[linear-gradient"]:hover::before {
        opacity: 1;
    }

    /* Lift on hover, press on click — applies to every button variant */
    button, a.inline-flex {
        transition: transform 150ms cubic-bezier(0.34, 1.56, 0.64, 1),
                    box-shadow 200ms ease,
                    background-color 200ms ease,
                    border-color 200ms ease,
                    color 200ms ease;
    }
    button:not(:disabled):hover, a.inline-flex:hover {
        transform: translateY(-1px);
    }
    button:not(:disabled):active, a.inline-flex:active {
        transform: translateY(0) scale(0.96);
        transition-duration: 90ms;
    }

    /* Solid (non-gradient) colored buttons get a subtle glow on hover */
    [class*="shadow-[0_1px"]:hover {
        filter: brightness(1.04);
    }

    @media (prefers-reduced-motion: reduce) {
        * { transition-duration: 0.01ms !important; }
    }
</style>
