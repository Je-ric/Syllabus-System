@props([
    'href'        => null,
    'type'        => 'button',
    'variant'     => 'primary',
    'loading'     => null,
    'wireTarget'  => null,
    'submitting'  => null,   {{-- Alpine expression, e.g. "submitting" or "isLoading". When set, shows spinner when truthy. --}}
    'loadingText' => null,   {{-- Label shown while submitting, e.g. "Saving…". Defaults to slot content. --}}
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
        // Default = Emerald 700, Hover = 800 — solid, readable on white bg
        // 'table-confirm'  => $tableBtn . 'text-white bg-[#00965F] hover:bg-[#06754E] active:bg-[#076042] focus:ring-[#00965F]/30 shadow-[0_1px_2px_rgba(16,24,40,0.08)]',
        // 'table-edit'     => $tableBtn . 'text-white bg-[#194C6E] hover:bg-[#143D57] active:bg-[#0e2f43] focus:ring-[#194C6E]/30 shadow-[0_1px_2px_rgba(16,24,40,0.08)]',
        // 'table-view'     => $tableBtn . 'text-white bg-[#2A3B47] hover:bg-[#1D2836] active:bg-[#141e28] focus:ring-[#2A3B47]/30 shadow-[0_1px_2px_rgba(16,24,40,0.08)]',
        // 'table-manage'   => $tableBtn . 'text-white bg-[#394056] hover:bg-[#2A3B47] active:bg-[#1D2836] focus:ring-[#394056]/30 shadow-[0_1px_2px_rgba(16,24,40,0.08)]',
        // 'table-danger'   => $tableBtn . 'text-white bg-[#D21B14] hover:bg-[#BA1F19] active:bg-[#9D1F1A] focus:ring-[#D21B14]/30 shadow-[0_1px_2px_rgba(16,24,40,0.08)]',
        // 'table-disable'  => $tableBtn . 'text-white bg-[#4F5D6B] hover:bg-[#394056] active:bg-[#2A3B47] focus:ring-[#4F5D6B]/30 shadow-[0_1px_2px_rgba(16,24,40,0.08)]',
        // 'table-restore'  => $tableBtn . 'text-white bg-[#06754E] hover:bg-[#076042] active:bg-[#003724] focus:ring-[#06754E]/30 shadow-[0_1px_2px_rgba(16,24,40,0.08)]',
        // 'table-cancel'   => $tableBtn . 'bg-white text-[#394056] border border-[#D6DDE3]
        //                                  hover:bg-[#F1F3F5] hover:border-[#C1C8D4] focus:ring-[#D6DDE3]/30',
        'table-confirm'  => $tableBtn . 'text-white bg-[linear-gradient(180deg,#00C075_0%,#00965F_100%)] hover:bg-[linear-gradient(180deg,#00965F_0%,#06754E_100%)] active:bg-[#06754E] focus:ring-[#00C075]/30 shadow-[0_1px_2px_rgba(0,150,95,0.3)]',
        'table-edit'     => $tableBtn . 'text-white bg-[linear-gradient(180deg,#1F5E89_0%,#194C6E_100%)] hover:bg-[linear-gradient(180deg,#194C6E_0%,#143D57_100%)] active:bg-[#143D57] focus:ring-[#1F5E89]/30 shadow-[0_1px_2px_rgba(25,76,110,0.3)]',
        'table-view'     => $tableBtn . 'text-white bg-[linear-gradient(180deg,#253540_0%,#1D2836_100%)] hover:bg-[linear-gradient(180deg,#1D2836_0%,#141e28_100%)] active:bg-[#141e28] focus:ring-[#253540]/30 shadow-[0_1px_2px_rgba(29,40,54,0.3)]',
        'table-manage'   => $tableBtn . 'text-white bg-[linear-gradient(180deg,#394056_0%,#2A3B47_100%)] hover:bg-[linear-gradient(180deg,#2A3B47_0%,#1D2836_100%)] active:bg-[#1D2836] focus:ring-[#394056]/30 shadow-[0_1px_2px_rgba(42,59,71,0.3)]',
        'table-danger'   => $tableBtn . 'text-white bg-[linear-gradient(180deg,#E52F28_0%,#BA1F19_100%)] hover:bg-[linear-gradient(180deg,#D21B14_0%,#9D1F1A_100%)] active:bg-[#9D1F1A] focus:ring-[#E52F28]/30 shadow-[0_1px_2px_rgba(186,31,25,0.3)]',
        'table-disable'  => $tableBtn . 'text-white bg-[linear-gradient(180deg,#4F5D6B_0%,#394056_100%)] hover:bg-[linear-gradient(180deg,#394056_0%,#2A3B47_100%)] active:bg-[#2A3B47] focus:ring-[#4F5D6B]/30 shadow-[0_1px_2px_rgba(57,64,86,0.25)]',
        'table-restore'  => $tableBtn . 'text-white bg-[linear-gradient(180deg,#06754E_0%,#076042_100%)] hover:bg-[linear-gradient(180deg,#076042_0%,#003724_100%)] active:bg-[#003724] focus:ring-[#06754E]/30 shadow-[0_1px_2px_rgba(6,117,78,0.3)]',
        'table-cancel'   => $tableBtn . 'bg-white text-[#394056] border border-[#D6DDE3] hover:bg-[#F1F3F5] hover:border-[#C1C8D4] focus:ring-[#D6DDE3]/30',

        'table-light-edit' => $tableBtn . 'p-2 rounded-lg text-[#A5B2BD] hover:text-[#194C6E] hover:bg-[#DAF1FF] focus:ring-[#3197D6]/25 transition-colors',
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
        'sm-primary' => $wizardBtn . 'bg-[#D5FFF0] text-[#076042] border border-[#00965F] hover:bg-[#AEFFE2] hover:border-[#06754E] focus:ring-[#00965F]/20',
        'sm-cancel'  => $wizardBtn . 'bg-white text-[#394056] border border-[#D6DDE3] hover:bg-[#F1F3F5] hover:border-[#C1C8D4] focus:ring-[#D6DDE3]/20',
        'sm-danger'  => $wizardBtn . 'bg-[#E52F28] text-white border border-[#D21B14]
                                       hover:bg-[#D21B14] active:bg-[#BA1F19] focus:ring-[#E52F28]/30 shadow-[0_1px_2px_rgba(16,24,40,0.08)]',
        'sm-warning' => $wizardBtn . 'bg-[#FFF6E2] text-[#875200] border border-[#F5B126]
                                       hover:bg-[#FFE9B5] hover:border-[#D79400] focus:ring-[#FFC646]/20',
        'sm-info'    => $wizardBtn . 'bg-[#DAF1FF] text-[#143D57] border border-[#3197D6]
                                       hover:bg-[#AEDFFF] hover:border-[#194C6E] focus:ring-[#3197D6]/20',
        'sm-success' => $wizardBtn . 'bg-[#D5FFF0] text-[#076042] border border-[#00965F]
                                       hover:bg-[#AEFFE2] hover:border-[#06754E] focus:ring-[#00965F]/20',
        'sm-soft'    => $wizardBtn . 'bg-[#AEFFE2] text-[#06754E] border border-[#00C075]
                                       hover:bg-[#70FFCC] hover:border-[#00965F] focus:ring-[#00C075]/20',
        'sm-add'     => $wizardBtn . 'bg-[#00965F] text-white border border-[#06754E]
                                       hover:bg-[#06754E] active:bg-[#076042] focus:ring-[#00965F]/30 shadow-[0_1px_2px_rgba(16,24,40,0.08)]',

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
