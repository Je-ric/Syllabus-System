{{-- weekly-partials/tab-switcher.blade.php --}}

@if ($hasLEC && $hasLAB)

    <div class="mb-5">

        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-2">
            Component
        </p>

        {{-- Pill switcher --}}
        <div class="inline-flex p-1 rounded-xl border border-slate-200 bg-slate-50 w-full max-w-xs">

            {{-- LEC --}}
            <button
                type="button"
                wire:click="setComponentType('LEC')"
                wire:loading.attr="disabled"
                wire:target="setComponentType"
                @class([
                    'relative flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-semibold transition-all duration-150',
                    'bg-white text-emerald-700 shadow-sm ring-1 ring-inset ring-emerald-100' => $activeComponent === 'LEC',
                    'text-slate-500 hover:text-slate-700'                                    => $activeComponent !== 'LEC',
                ])>

                <span wire:loading wire:target="setComponentType('LEC')" class="absolute">
                    <i class="bx bx-loader-alt bx-spin text-emerald-600 text-[14px]"></i>
                </span>

                <span wire:loading.remove wire:target="setComponentType('LEC')"
                    class="inline-flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full transition-colors
                        {{ $activeComponent === 'LEC' ? 'bg-emerald-500' : 'bg-slate-300' }}">
                    </span>
                    Lecture
                    <span class="font-mono text-[10px] opacity-60">LEC</span>
                </span>

            </button>

            {{-- LAB --}}
            <button
                type="button"
                wire:click="setComponentType('LAB')"
                wire:loading.attr="disabled"
                wire:target="setComponentType"
                @class([
                    'relative flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-semibold transition-all duration-150',
                    'bg-white text-blue-700 shadow-sm ring-1 ring-inset ring-blue-100' => $activeComponent === 'LAB',
                    'text-slate-500 hover:text-slate-700'                              => $activeComponent !== 'LAB',
                ])>

                <span wire:loading wire:target="setComponentType('LAB')" class="absolute">
                    <i class="bx bx-loader-alt bx-spin text-blue-600 text-[14px]"></i>
                </span>

                <span wire:loading.remove wire:target="setComponentType('LAB')"
                    class="inline-flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full transition-colors
                        {{ $activeComponent === 'LAB' ? 'bg-blue-500' : 'bg-slate-300' }}">
                    </span>
                    Laboratory
                    <span class="font-mono text-[10px] opacity-60">LAB</span>
                </span>

            </button>

        </div>

        {{-- Loading state underneath --}}
        <div
            wire:loading
            wire:target="setComponentType"
            class="mt-2 inline-flex items-center gap-1.5 text-[12px] text-slate-400">
            <i class="bx bx-loader-alt bx-spin text-[13px]"></i>
            Switching component…
        </div>

    </div>

@elseif ($hasLEC || $hasLAB)

    <div class="mb-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold
            {{ $hasLEC
                ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200'
                : 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-200' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $hasLEC ? 'bg-emerald-500' : 'bg-blue-500' }}"></span>
            {{ $hasLEC ? 'Lecture (LEC)' : 'Laboratory (LAB)' }}
        </span>
    </div>

@endif