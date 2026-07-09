{{-- outcomes-partials/po-reference-drawer.blade.php --}}
<x-layout.offcanvas title="Program Outcomes" subtitle="Align your COs to these POs" icon="bx-list-check" open="poRefOpen">

    <x-slot:footer>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#a1a1aa]">IED</span>
            <span class="flex items-center gap-1.5 text-[11px] text-[#52525b]">
                <x-feedback-status.ied-badge level="I" /> Introductory
            </span>
            <span class="flex items-center gap-1.5 text-[11px] text-[#52525b]">
                <x-feedback-status.ied-badge level="E" /> Enabling
            </span>
            <span class="flex items-center gap-1.5 text-[11px] text-[#52525b]">
                <x-feedback-status.ied-badge level="D" /> Demonstrative
            </span>
        </div>
    </x-slot:footer>

    @if (!empty($courseInfo['program_title']))
        <div class="rounded-[14px] border border-[#e4e4e7] bg-[#f4f4f5] px-4 py-3 mb-4">
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#a1a1aa] mb-0.5">Program</p>
            <p class="text-[13px] font-medium text-[#18181b]">{{ $courseInfo['program_title'] }}</p>
        </div>
    @endif

    @if (count($programOutcomes) > 0)
        <div class="overflow-hidden rounded-[14px] border border-[#e4e4e7]">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-[#f4f4f5] border-b border-[#e4e4e7]">
                        <th class="px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-widest text-[#a1a1aa] w-14">PO</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-widest text-[#a1a1aa]">Description</th>
                        <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-widest text-[#a1a1aa] w-12">IED</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f4f4f5] bg-white">
                    @foreach ($courseInfo['po_rows'] as $po)
                        <tr class="hover:bg-[#f4f4f5] transition-colors">
                            <td class="px-3 py-3 align-top">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-[10px] bg-[#f0fdf4] border border-[#d1fae5] text-[#16a34a] text-[11px] font-bold">
                                    {{ $po['po_code'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 align-top">
                                <p class="text-[12px] text-[#3f3f46] leading-relaxed">{{ $po['po_text'] }}</p>
                            </td>
                            <td class="px-3 py-3 text-center align-middle">
                                @if (!empty($po['ied']))
                                    <x-feedback-status.ied-badge :level="$po['ied']" />
                                @else
                                    <span class="text-[#d4d4d8]">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-feedback-status.empty-state icon="bx bx-list-check" title="No Program Outcomes"
            message="POs are defined at the program level by your Department Chair." />
    @endif

</x-layout.offcanvas>
