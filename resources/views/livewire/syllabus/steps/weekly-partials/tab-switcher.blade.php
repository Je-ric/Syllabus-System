{{-- weekly-partials/tab-switcher.blade.php --}}

@if ($hasLEC && $hasLAB)

    <div class="mb-5">
        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-2">Component</p>
        <div class="inline-flex items-center gap-1 p-1 rounded-xl border border-[#e2e8f0] bg-[#f8fafc]">

            {{-- LEC --}}
            <button type="button"
                wire:click="setComponentType('LEC')"
                wire:loading.attr="disabled"
                wire:target="setComponentType"
                @class([
                    'relative flex items-center gap-2 px-4 py-2 text-[13px] font-semibold rounded-lg transition-all duration-150 min-w-[140px] justify-center',
                    'bg-white text-[#166534] ring-1 ring-[#bbf7d0]' => $activeComponent === 'LEC',
                    'text-[#475569] hover:text-[#0f172a] hover:bg-white/70' => $activeComponent !== 'LEC',
                ])
                style="{{ $activeComponent === 'LEC' ? 'box-shadow: 0 2px 16px rgba(0,0,0,.07);' : '' }}">
                <span wire:loading wire:target="setComponentType('LEC')" class="flex items-center gap-1.5">
                    <i class="bx bx-loader-alt bx-spin text-[#16a34a]"></i> Switching…
                </span>
                <span wire:loading.remove wire:target="setComponentType('LEC')" class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ $activeComponent === 'LEC' ? 'bg-[#16a34a]' : 'bg-[#e2e8f0]' }}"></span>
                    Lecture (LEC)
                </span>
            </button>

            {{-- LAB --}}
            <button type="button"
                wire:click="setComponentType('LAB')"
                wire:loading.attr="disabled"
                wire:target="setComponentType"
                @class([
                    'relative flex items-center gap-2 px-4 py-2 text-[13px] font-semibold rounded-lg transition-all duration-150 min-w-[140px] justify-center',
                    'bg-white text-[#1e40af] ring-1 ring-[#bfdbfe]' => $activeComponent === 'LAB',
                    'text-[#475569] hover:text-[#0f172a] hover:bg-white/70' => $activeComponent !== 'LAB',
                ])
                style="{{ $activeComponent === 'LAB' ? 'box-shadow: 0 2px 16px rgba(0,0,0,.07);' : '' }}">
                <span wire:loading wire:target="setComponentType('LAB')" class="flex items-center gap-1.5">
                    <i class="bx bx-loader-alt bx-spin text-[#3b82f6]"></i> Switching…
                </span>
                <span wire:loading.remove wire:target="setComponentType('LAB')" class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ $activeComponent === 'LAB' ? 'bg-[#3b82f6]' : 'bg-[#e2e8f0]' }}"></span>
                    Laboratory (LAB)
                </span>
            </button>

        </div>

        <div wire:loading wire:target="setComponentType"
            class="mt-2 inline-flex items-center gap-1.5 text-[13px] text-[#475569]">
            <i class="bx bx-loader-alt bx-spin text-[#94a3b8]"></i>
            Saving and loading {{ $activeComponent === 'LEC' ? 'Laboratory' : 'Lecture' }} content…
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
