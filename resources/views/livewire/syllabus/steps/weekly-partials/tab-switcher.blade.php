{{-- weekly-partials/tab-switcher.blade.php --}}

@if ($hasLEC && $hasLAB)

    <div class="mb-5">

        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Component</p>

        {{-- Pill switcher --}}
        <div class="inline-flex p-1 rounded-xl border border-slate-200 bg-[#f8fafc]" style="box-shadow: inset 0 1px 3px rgba(0,0,0,.05);">

            {{-- LEC --}}
            <button
                type="button"
                wire:click="setComponentType('LEC')"
                wire:loading.attr="disabled"
                wire:target="setComponentType"
                @class([
                    'relative flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold transition-all duration-150',
                    'bg-white text-emerald-700 shadow-sm ring-1 ring-inset ring-emerald-100' => $activeComponent === 'LEC',
                    'text-slate-500 hover:text-slate-700 hover:bg-white/50'                  => $activeComponent !== 'LEC',
                ])>

                <span wire:loading wire:target="setComponentType('LEC')" class="absolute inset-0 flex items-center justify-center">
                    <i class="bx bx-loader-alt bx-spin text-emerald-600 text-sm"></i>
                </span>

                <span wire:loading.remove wire:target="setComponentType('LEC')" class="inline-flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full transition-colors
                        {{ $activeComponent === 'LEC' ? 'bg-emerald-500' : 'bg-slate-300' }}">
                    </span>
                    Lecture
                    {{-- <span class="text-xs font-mono opacity-50">LEC</span> --}}
                </span>

            </button>

            {{-- LAB --}}
            <button
                type="button"
                wire:click="setComponentType('LAB')"
                wire:loading.attr="disabled"
                wire:target="setComponentType"
                @class([
                    'relative flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold transition-all duration-150',
                    'bg-white text-blue-700 shadow-sm ring-1 ring-inset ring-blue-100' => $activeComponent === 'LAB',
                    'text-slate-500 hover:text-slate-700 hover:bg-white/50'            => $activeComponent !== 'LAB',
                ])>

                <span wire:loading wire:target="setComponentType('LAB')" class="absolute inset-0 flex items-center justify-center">
                    <i class="bx bx-loader-alt bx-spin text-blue-600 text-sm"></i>
                </span>

                <span wire:loading.remove wire:target="setComponentType('LAB')" class="inline-flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full transition-colors
                        {{ $activeComponent === 'LAB' ? 'bg-blue-500' : 'bg-slate-300' }}">
                    </span>
                    Laboratory
                    {{-- <span class="text-xs font-mono opacity-50">LAB</span> --}}
                </span>

            </button>

        </div>

        <div wire:loading wire:target="setComponentType"
            class="mt-2 inline-flex items-center gap-1.5 text-xs text-slate-400">
            <i class="bx bx-loader-alt bx-spin text-sm"></i> Switching component…
        </div>

    </div>

@elseif ($hasLEC || $hasLAB)

    <div class="mb-4">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
            {{ $hasLEC
                ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200'
                : 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-200' }}">
            <span class="w-2 h-2 rounded-full {{ $hasLEC ? 'bg-emerald-500' : 'bg-blue-500' }}"></span>
            {{ $hasLEC ? 'Lecture' : 'Laboratory' }}
        </span>
    </div>

@endif
