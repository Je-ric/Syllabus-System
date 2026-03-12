{{--
    Partial: weekly-partials/tab-switcher.blade.php
    ────────────────────────────────────────────────
    • Both LEC + LAB → pill switcher with auto-save-on-switch.
    • Only one component → static badge.
    • Neither → nothing rendered.

    Inherits from parent component view:
        $hasLEC          bool
        $hasLAB          bool
        $activeComponent string  'LEC' | 'LAB'
--}}

@if ($hasLEC && $hasLAB)

    <div class="mb-5">
        <div class="inline-flex items-center gap-1 p-1 bg-slate-100 rounded-xl">

            {{-- LEC tab --}}
            <button type="button"
                wire:click="setComponentType('LEC')"
                wire:loading.attr="disabled"
                wire:target="setComponentType"
                @class([
                    'relative flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150',
                    'bg-white text-emerald-700 shadow-sm ring-1 ring-slate-200' => $activeComponent === 'LEC',
                    'text-slate-500 hover:text-slate-700 hover:bg-white/60'     => $activeComponent !== 'LEC',
                ])>
                <span wire:loading wire:target="setComponentType('LEC')" class="flex items-center gap-1.5">
                    <i class="bx bx-loader-alt bx-spin text-emerald-600"></i> Switching…
                </span>
                <span wire:loading.remove wire:target="setComponentType('LEC')" class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ $activeComponent === 'LEC' ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                    Lecture (LEC)
                </span>
                {{-- "saving" mini-badge floats on the active tab while the other is loading --}}
                @if ($activeComponent === 'LEC')
                    <span wire:loading wire:target="setComponentType('LAB')"
                        class="absolute -top-1.5 -right-1.5 flex items-center gap-0.5
                               bg-amber-100 text-amber-700 text-[10px] font-semibold
                               px-1.5 py-0.5 rounded-full border border-amber-200">
                        <i class="bx bx-loader-alt bx-spin text-[10px]"></i> saving
                    </span>
                @endif
            </button>

            {{-- LAB tab --}}
            <button type="button"
                wire:click="setComponentType('LAB')"
                wire:loading.attr="disabled"
                wire:target="setComponentType"
                @class([
                    'relative flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150',
                    'bg-white text-blue-700 shadow-sm ring-1 ring-slate-200' => $activeComponent === 'LAB',
                    'text-slate-500 hover:text-slate-700 hover:bg-white/60'  => $activeComponent !== 'LAB',
                ])>
                <span wire:loading wire:target="setComponentType('LAB')" class="flex items-center gap-1.5">
                    <i class="bx bx-loader-alt bx-spin text-blue-600"></i> Switching…
                </span>
                <span wire:loading.remove wire:target="setComponentType('LAB')" class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ $activeComponent === 'LAB' ? 'bg-blue-500' : 'bg-slate-300' }}"></span>
                    Laboratory (LAB)
                </span>
                @if ($activeComponent === 'LAB')
                    <span wire:loading wire:target="setComponentType('LEC')"
                        class="absolute -top-1.5 -right-1.5 flex items-center gap-0.5
                               bg-amber-100 text-amber-700 text-[10px] font-semibold
                               px-1.5 py-0.5 rounded-full border border-amber-200">
                        <i class="bx bx-loader-alt bx-spin text-[10px]"></i> saving
                    </span>
                @endif
            </button>

        </div>

        {{-- Inline status shown while the request is in-flight --}}
        <div wire:loading wire:target="setComponentType"
            class="mt-2 flex items-center gap-1.5 text-xs text-slate-400">
            <i class="bx bx-loader-alt bx-spin"></i>
            Saving current data and loading
            {{ $activeComponent === 'LEC' ? 'Laboratory' : 'Lecture' }} content…
        </div>
    </div>

@elseif ($hasLEC || $hasLAB)

    {{-- Single-component static badge --}}
    <div class="mb-4">
        <x-feedback-status.status-indicator
            :variant="$hasLEC ? 'emerald' : 'blue'"
            :dot="true"
            size="sm">
            {{ $hasLEC ? 'Lecture (LEC)' : 'Laboratory (LAB)' }}
        </x-feedback-status.status-indicator>
    </div>

@endif