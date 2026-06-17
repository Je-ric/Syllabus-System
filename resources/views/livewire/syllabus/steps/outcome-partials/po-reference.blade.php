{{-- PO Reference — Offcanvas trigger button + panel --}}

<div x-data="{ poOpen: false }">

    {{-- Trigger --}}
    <button type="button"
        x-on:click="poOpen = true"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
               border border-slate-200 bg-white text-[13px] font-semibold text-slate-700
               hover:bg-slate-50 hover:border-slate-300 transition-colors w-full justify-between"
        style="box-shadow: 0 2px 8px rgba(0,0,0,.05);">
        <span class="flex items-center gap-2">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 text-slate-500">
                <i class="bx bx-list-check text-base leading-none"></i>
            </span>
            Program Outcomes Reference
        </span>
        <span class="flex items-center gap-1 text-[12px] text-slate-400 font-normal">
            {{ count($programOutcomes) }} PO{{ count($programOutcomes) !== 1 ? 's' : '' }}
            <i class="bx bx-chevron-right text-base"></i>
        </span>
    </button>

    {{-- Backdrop --}}
    <div x-show="poOpen" x-cloak x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         x-on:click="poOpen = false"
         class="fixed inset-0 bg-black/30 z-40"></div>

    {{-- Offcanvas panel --}}
    <div x-show="poOpen" x-cloak
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 z-50 w-full max-w-lg bg-white shadow-2xl flex flex-col">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-500">
                    <i class="bx bx-list-check text-base leading-none"></i>
                </span>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Program Outcomes</p>
                    <p class="text-xs text-slate-400 mt-0.5">Reference — align your COs to these POs</p>
                </div>
            </div>
            <button type="button" x-on:click="poOpen = false"
                class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto">
            @if (count($programOutcomes) > 0)
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 sticky top-0 z-10">
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-widest
                                       text-slate-400 border-b border-slate-100 w-14">PO</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-widest
                                       text-slate-400 border-b border-slate-100">Description</th>
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-widest
                                       text-slate-400 border-b border-slate-100 w-24">IED</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($programOutcomes as $po)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-3 align-top">
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                                                 bg-slate-100 text-slate-600 text-xs font-bold ring-1 ring-slate-200">
                                        {{ $po['po_code'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ $po['po_text'] }}</p>
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    @if (!empty($po['ied']))
                                        <x-feedback-status.ied-badge :level="$po['ied']" />
                                    @else
                                        <span class="text-slate-300 text-sm">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-6">
                    <x-empty-state icon="bx bx-list-check" title="No Program Outcomes"
                        description="POs are defined at the program level by your Department Chair." />
                </div>
            @endif
        </div>

        {{-- IED legend footer --}}
        @if (count($programOutcomes) > 0)
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 shrink-0
                        flex flex-wrap items-center gap-x-5 gap-y-1">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">IED Legend</span>
                <span class="flex items-center gap-1.5 text-[11px] text-slate-500">
                    <x-feedback-status.ied-badge level="I" /> Introduced
                </span>
                <span class="flex items-center gap-1.5 text-[11px] text-slate-500">
                    <x-feedback-status.ied-badge level="E" /> Expanded
                </span>
                <span class="flex items-center gap-1.5 text-[11px] text-slate-500">
                    <x-feedback-status.ied-badge level="D" /> Demonstrated
                </span>
            </div>
        @endif

    </div>

</div>
