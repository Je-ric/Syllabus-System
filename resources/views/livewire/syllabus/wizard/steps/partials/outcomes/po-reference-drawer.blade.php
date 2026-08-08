{{-- outcomes-partials/po-reference-drawer.blade.php --}}
<x-layout.offcanvas title="Program Outcomes" subtitle="Align your COs to these POs" icon="bx-list-check" open="poRefOpen">

    <x-slot:footer>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#93A1AF]">IED</span>
            <span class="flex items-center gap-1.5 text-[11px] text-[#4F5D6B]">
                <x-feedback-status.ied-badge level="I" /> Introductory
            </span>
            <span class="flex items-center gap-1.5 text-[11px] text-[#4F5D6B]">
                <x-feedback-status.ied-badge level="E" /> Enabling
            </span>
            <span class="flex items-center gap-1.5 text-[11px] text-[#4F5D6B]">
                <x-feedback-status.ied-badge level="D" /> Demonstrative
            </span>
        </div>
    </x-slot:footer>

    @if (!empty($courseInfo['program_title']))
        <div class="rounded-2xl border border-[#E3E8EB] bg-[#F9FAFA] px-4 py-3 mb-4"
             style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);">
            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#93A1AF] mb-0.5">Program</p>
            <p class="text-[13px] font-medium text-[#1D2836]">{{ $courseInfo['program_title'] }}</p>
        </div>
    @endif

    @if (count($programOutcomes) > 0)
        <div class="overflow-hidden rounded-2xl border border-[#E3E8EB]"
             style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-[#F1F3F5] border-b border-[#E3E8EB]">
                        <th class="px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-[0.12em] text-[#93A1AF] w-14">PO</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-[0.12em] text-[#93A1AF]">Description</th>
                        <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-[0.12em] text-[#93A1AF] w-12">IED</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F1F3F5] bg-white">
                    @foreach ($courseInfo['po_rows'] as $po)
                        <tr class="hover:bg-[#F9FAFA] transition-colors">
                            <td class="px-3 py-3 align-top">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-[10px] bg-[#EDFFF8] border border-[#AEFFE2] text-[#06754E] text-[11px] font-bold">
                                    {{ $po['po_code'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 align-top">
                                <p class="text-[12px] text-[#4F5D6B] leading-relaxed">{{ $po['po_text'] }}</p>
                            </td>
                            <td class="px-3 py-3 text-center align-middle">
                                @if (!empty($po['ied']))
                                    <x-feedback-status.ied-badge :level="$po['ied']" />
                                @else
                                    <span class="text-[#C1C8D4]">—</span>
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