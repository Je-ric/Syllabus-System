<x-offcanvas id="peo-reference-drawer" title="PEO Reference" width="w-96">
    @if(count($peos) > 0)
        <div class="space-y-3">
            <p class="text-[12px] text-[#94a3b8]">
                Check the PEOs that each PO maps to. Click a PEO chip on the PO row to toggle the mapping.
            </p>
            @foreach($peos as $index => $peo)
                <div class="flex items-start gap-3 rounded-xl border border-[#e2e8f0] bg-white px-4 py-3
                            hover:border-emerald-200 hover:bg-emerald-50/30 transition-colors">
                    <span class="mt-0.5 shrink-0 inline-flex items-center justify-center
                                w-9 h-9 rounded-lg bg-[#dcfce7] text-[#166534]
                                text-[12px] font-bold ring-1 ring-[#bbf7d0]">
                        {{ strtoupper($peo['peo_code']) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-0.5">
                            PEO {{ $index + 1 }}
                        </p>
                        <p class="text-[13px] text-[#475569] leading-relaxed">
                            {{ $peo['peo_text'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <i class="bx bx-graduation text-4xl text-[#94a3b8]"></i>
            <p class="mt-3 text-[13px] font-semibold text-[#475569]">No PEOs defined yet</p>
            <p class="text-[12px] text-[#94a3b8] mt-1">Go to the PEOs tab and add them first.</p>
        </div>
    @endif
</x-offcanvas>
