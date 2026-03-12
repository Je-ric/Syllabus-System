<div x-data="{ open: true }"
        class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Panel header --}}
        <button type="button"
            x-on:click="open = !open"
            class="w-full flex items-center justify-between px-5 py-4
                hover:bg-slate-50 transition-colors text-left">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                            bg-slate-100 text-slate-500">
                    <i class="bx bx-list-check text-base leading-none"></i>
                </span>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Program Outcomes</p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Reference — align your COs to these POs
                    </p>
                </div>
            </div>
            <i class="bx text-slate-400 text-xl transition-transform duration-200"
                x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
        </button>

        {{-- Panel body --}}
        <div x-show="open" x-collapse>
            <div class="border-t border-slate-100">
                @if (count($programOutcomes) > 0)
                    {{-- Table with sticky header --}}
                    <div class="overflow-auto max-h-130">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 sticky top-0 z-10">
                                    <th class="px-4 py-2.5 text-left text-[10px] font-bold
                                            uppercase tracking-widest text-slate-400
                                            border-b border-slate-100 w-14">
                                        PO
                                    </th>
                                    <th class="px-4 py-2.5 text-left text-[10px] font-bold
                                            uppercase tracking-widest text-slate-400
                                            border-b border-slate-100">
                                    Description
                                    </th>
                                    <th class="px-4 py-2.5 text-center text-[10px] font-bold
                                            uppercase tracking-widest text-slate-400
                                            border-b border-slate-100 w-24">
                                        IED Level
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($programOutcomes as $po)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-4 py-3 align-top">
                                            <span class="inline-flex items-center justify-center
                                                        w-9 h-9 rounded-lg bg-slate-100
                                                        text-slate-600 text-xs font-bold
                                                        ring-1 ring-slate-200">
                                                {{ $po['po_code'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <p class="text-xs text-slate-600 leading-relaxed">
                                                {{ $po['po_text'] }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-3 align-middle text-center">
                                            @if (! empty($po['ied']))
                                                <x-feedback-status.ied-badge :level="$po['ied']" />
                                            @else
                                                <span class="text-slate-300 text-sm">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                        {{-- IED color legend --}}
                    <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100
                                flex flex-wrap items-center gap-x-5 gap-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            IED Legend
                        </span>
                        <span class="flex items-center gap-1.5 text-[11px] text-slate-500">
                            <x-feedback-status.ied-badge level="I" />
                            Introduced
                        </span>
                        <span class="flex items-center gap-1.5 text-[11px] text-slate-500">
                            <x-feedback-status.ied-badge level="E" />
                                Expanded
                            </span>
                            <span class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                <x-feedback-status.ied-badge level="D" />
                                Demonstrated
                            </span>
                        </div>
                @else
                    <x-empty-state
                        icon="bx bx-list-check"
                        title="No Program Outcomes"
                        description="POs are defined at the program level by your Department Dhair."
                    />
                @endif
            </div>
    </div>

</div>
