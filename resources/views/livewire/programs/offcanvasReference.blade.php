<x-offcanvas title="PEO Reference" open="peoDrawer" icon="bx-graduation" width="max-w-sm">
    @if(count($peos) > 0)
        <div class="space-y-2.5">
            <p class="text-[12px] text-[#a1a1aa]">
                Check the PEOs that each PO maps to. Click a PEO chip on the PO row to toggle the mapping.
            </p>
            @foreach($peos as $index => $peo)
                <div class="flex items-start gap-3 rounded-[14px] border border-[#e4e4e7] bg-white px-4 py-3
                            hover:border-[#d1fae5] hover:bg-[#f0fdf4] transition-colors">
                    <span class="mt-0.5 shrink-0 inline-flex items-center justify-center
                                w-9 h-9 rounded-[10px] bg-[#dcfce7] text-[#166534]
                                text-[12px] font-bold border border-[#86efac]">
                        {{ strtoupper($peo['peo_code']) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[#16a34a] mb-0.5">
                            PEO {{ $index + 1 }}
                        </p>
                        <p class="text-[13px] text-[#3f3f46] leading-relaxed">
                            {{ $peo['peo_text'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <i class="bx bx-graduation text-4xl text-[#d4d4d8]"></i>
            <p class="mt-3 text-[13px] font-semibold text-[#52525b]">No PEOs defined yet</p>
            <p class="text-[12px] text-[#a1a1aa] mt-1">Go to the PEOs tab and add them first.</p>
        </div>
    @endif
</x-offcanvas>
