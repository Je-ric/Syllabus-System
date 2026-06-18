{{-- weekly-partials/tab-switcher.blade.php --}}

@if ($hasLEC && $hasLAB)

<div class="mb-5">

    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500 mb-2">
        Component
    </p>

    <div class="grid grid-cols-2 w-full p-1 rounded-xl border border-slate-200 bg-slate-50">

        {{-- LEC --}}
        <button
            type="button"
            wire:click="setComponentType('LEC')"
            wire:loading.attr="disabled"
            wire:target="setComponentType"
            @class([
                'flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-[13px] font-semibold transition-all',
                'bg-white text-emerald-700 shadow-sm border border-emerald-100' => $activeComponent === 'LEC',
                'text-slate-600 hover:bg-white/70' => $activeComponent !== 'LEC',
            ])>

            <span wire:loading wire:target="setComponentType('LEC')">
                <i class="bx bx-loader-alt bx-spin"></i>
            </span>

            <span wire:loading.remove wire:target="setComponentType('LEC')"
                class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full {{ $activeComponent === 'LEC' ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                Lecture (LEC)
            </span>

        </button>

        {{-- LAB --}}
        <button
            type="button"
            wire:click="setComponentType('LAB')"
            wire:loading.attr="disabled"
            wire:target="setComponentType"
            @class([
                'flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-[13px] font-semibold transition-all',
                'bg-white text-blue-700 shadow-sm border border-blue-100' => $activeComponent === 'LAB',
                'text-slate-600 hover:bg-white/70' => $activeComponent !== 'LAB',
            ])>

            <span wire:loading wire:target="setComponentType('LAB')">
                <i class="bx bx-loader-alt bx-spin"></i>
            </span>

            <span wire:loading.remove wire:target="setComponentType('LAB')"
                class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full {{ $activeComponent === 'LAB' ? 'bg-blue-500' : 'bg-slate-300' }}"></span>
                Laboratory (LAB)
            </span>

        </button>

    </div>

    <div
        wire:loading
        wire:target="setComponentType"
        class="mt-2 text-[13px] text-slate-500 flex items-center gap-2">

        <i class="bx bx-loader-alt bx-spin"></i>

        Loading component...

    </div>

</div>

@elseif ($hasLEC || $hasLAB)

    <div class="mb-4 flex items-center gap-2">
        <x-feedback-status.status-indicator
            :variant="$hasLEC ? 'brand' : 'lab'"
            :dot="true">
            {{ $hasLEC ? 'Lecture (LEC)' : 'Laboratory (LAB)' }}
        </x-feedback-status.status-indicator>
    </div>

@endif
