{{-- outcomes-partials/po-reference-drawer.blade.php
     Requires: $programOutcomes array from CourseOutcomesStep
     Alpine state in parent: poRefOpen
--}}
<x-offcanvas title="Program Outcomes" subtitle="Reference — align your COs to these POs" icon="bx-list-check"
    open="poRefOpen" class="max-w-lg">

    <x-slot:footer>
        <div class="flex flex-wrap items-center gap-x-5 gap-y-1">
            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">IED</span>
            <span class="flex items-center gap-1.5 text-[11px] text-slate-500"><x-feedback-status.ied-badge
                    level="I" /> Introductory</span>
            <span class="flex items-center gap-1.5 text-[11px] text-slate-500"><x-feedback-status.ied-badge
                    level="E" /> Enabling</span>
            <span class="flex items-center gap-1.5 text-[11px] text-slate-500"><x-feedback-status.ied-badge
                    level="D" /> Demonstrative</span>
        </div>
    </x-slot:footer>

    @if (!empty($courseInfo['program_title']))
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-0.5">Program</p>
            <p class="text-[13px] text-slate-600">{{ $courseInfo['program_title'] }}</p>
        </div>
    @endif
    @if (count($programOutcomes) > 0)
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-2">CO → PO IED Mapping</p>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th
                                class="px-3 py-2 text-left text-[10px] font-bold uppercase tracking-widest text-slate-400 w-16">
                                PO</th>
                            <th
                                class="px-3 py-2 text-left text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                Description</th>
                            <th
                                class="px-3 py-2 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400 w-12">
                                IED</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($courseInfo['po_rows'] as $po)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-3 py-2.5 align-top">
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-600 text-[11px] font-bold ring-1 ring-slate-200">
                                        {{ $po['po_code'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 align-top">
                                    <p class="text-[11px] text-slate-600 leading-relaxed">{{ $po['po_text'] }}</p>
                                </td>
                                <td class="px-3 py-2.5 text-center align-middle">
                                    @if (!empty($po['ied']))
                                        <x-feedback-status.ied-badge :level="$po['ied']" />
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <x-empty-state icon="bx bx-list-check" title="No Program Outcomes"
            message="POs are defined at the program level by your Department Chair." />
    @endif

</x-offcanvas>
