{{-- weekly-partials/tab-switcher.blade.php --}}

@if ($hasLEC && $hasLAB)

    <div class="mb-5">
        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#71717a] mb-2">Component</p>

        <div class="inline-flex p-1 rounded-[14px] border border-[#e4e4e7] bg-[#f4f4f5]"
             style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.04);">

            {{-- LEC --}}
            <button
                type="button"
                wire:click="setComponentType('LEC')"
                wire:loading.attr="disabled"
                wire:target="setComponentType"
                @class([
                    'relative flex items-center gap-2 px-5 py-2 rounded-[10px] text-[13px] font-semibold transition-all duration-150',
                    'bg-white text-[#166534] border border-[#d1fae5] shadow-sm' => $activeComponent === 'LEC',
                    'text-[#71717a] hover:text-[#18181b] hover:bg-white/60'     => $activeComponent !== 'LEC',
                ])>
                <span wire:loading wire:target="setComponentType('LEC')" class="absolute inset-0 flex items-center justify-center">
                    <i class="bx bx-loader-alt bx-spin text-[#16a34a] text-sm"></i>
                </span>
                <span wire:loading.remove wire:target="setComponentType('LEC')" class="inline-flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full transition-colors {{ $activeComponent === 'LEC' ? 'bg-[#16a34a]' : 'bg-[#d4d4d8]' }}"></span>
                    Lecture
                </span>
            </button>

            {{-- LAB --}}
            <button
                type="button"
                wire:click="setComponentType('LAB')"
                wire:loading.attr="disabled"
                wire:target="setComponentType"
                @class([
                    'relative flex items-center gap-2 px-5 py-2 rounded-[10px] text-[13px] font-semibold transition-all duration-150',
                    'bg-white text-[#1e40af] border border-[#bfdbfe] shadow-sm' => $activeComponent === 'LAB',
                    'text-[#71717a] hover:text-[#18181b] hover:bg-white/60'     => $activeComponent !== 'LAB',
                ])>
                <span wire:loading wire:target="setComponentType('LAB')" class="absolute inset-0 flex items-center justify-center">
                    <i class="bx bx-loader-alt bx-spin text-[#2563eb] text-sm"></i>
                </span>
                <span wire:loading.remove wire:target="setComponentType('LAB')" class="inline-flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full transition-colors {{ $activeComponent === 'LAB' ? 'bg-[#2563eb]' : 'bg-[#d4d4d8]' }}"></span>
                    Laboratory
                </span>
            </button>

        </div>

        <div wire:loading wire:target="setComponentType"
            class="mt-2 inline-flex items-center gap-1.5 text-[12px] text-[#71717a]">
            <i class="bx bx-loader-alt bx-spin text-sm"></i> Switching component…
        </div>
    </div>

@elseif ($hasLEC || $hasLAB)

    <div class="mb-4">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[12px] font-semibold border
            {{ $hasLEC
                ? 'bg-[#f0fdf4] text-[#166634] border-[#d1fae5]'
                : 'bg-[#eff6ff] text-[#1e40af] border-[#bfdbfe]' }}">
            <span class="w-2 h-2 rounded-full {{ $hasLEC ? 'bg-[#16a34a]' : 'bg-[#2563eb]' }}"></span>
            {{ $hasLEC ? 'Lecture' : 'Laboratory' }}
        </span>
    </div>

@endif
