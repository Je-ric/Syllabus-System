<x-layout.offcanvas title="PEO Reference" open="peoDrawer" icon="bx-medal" width="max-w-xl">
    @if(count($peos) > 0)
        <div class="space-y-2.5">
            <p class="text-[12px]" style="color:#93A1AF;">
                Check the PEOs that each PO maps to. Click a PEO chip on the PO row to toggle the mapping.
            </p>
            @foreach($peos as $index => $peo)
                <div class="flex items-start gap-3 rounded-2xl border px-4 py-3 transition-colors"
                     style="border-color:#E3E8EB; background:#FFFFFF;"
                     onmouseenter="this.style.borderColor='#AEFFE2'; this.style.background='#EDFFF8';"
                     onmouseleave="this.style.borderColor='#E3E8EB'; this.style.background='#FFFFFF';">
                    {{-- PEO code chip — Emerald 100/800/200 --}}
                    <span class="mt-0.5 shrink-0 inline-flex items-center justify-center
                                w-9 h-9 rounded-[10px] text-[12px] font-bold border"
                          style="background:#D5FFF0; color:#06754E; border-color:#AEFFE2;">
                        {{ strtoupper($peo['peo_code']) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-0.5" style="color:#00965F;">
                            PEO {{ $index + 1 }}
                        </p>
                        <p class="text-[13px] leading-relaxed" style="color:#394056;">
                            {{ $peo['peo_text'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-3"
                  style="background:#F1F3F5;">
                <i class="bx bx-graduation text-3xl" style="color:#C1C8D4;"></i>
            </span>
            <p class="mt-1 text-[13px] font-semibold" style="color:#394056;">No PEOs defined yet</p>
            <p class="text-[12px] mt-1" style="color:#93A1AF;">Go to the PEOs tab and add them first.</p>
        </div>
        {{-- empty state component --}}
    @endif
</x-layout.offcanvas>
