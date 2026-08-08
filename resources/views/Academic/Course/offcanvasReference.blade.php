<x-layout.offcanvas title="Program Outcomes Reference" open="poDrawer" icon="bx-book-open" width="max-w-xl">

    @if ($program && $program->outcomes->count() > 0)
        <div class="space-y-2.5">
            <p class="text-[12px] text-[#93A1AF]">
                Reference list of all Program Outcomes (POs) for this program.
            </p>

            @foreach ($program->outcomes as $index => $outcome)
                <div class="flex items-start gap-3 rounded-[14px] border border-[#E3E8EB] bg-white px-4 py-3
                            hover:border-[#AEFFE2] hover:bg-[#EDFFF8] transition-colors">

                    <span class="mt-0.5 shrink-0 inline-flex items-center justify-center
                                min-w-[3rem] h-9 rounded-[10px] bg-[#D5FFF0] text-[#076042]
                                text-[12px] font-bold border border-[#70FFCC]">
                        {{ strtoupper($outcome->po_code) }}
                    </span>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[#00965F] mb-0.5">
                            Program Outcome {{ $index + 1 }}
                        </p>
                        <p class="text-[13px] text-[#394056] leading-relaxed">
                            {{ $outcome->po_text }}
                        </p>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <i class="bx bx-book-open text-4xl text-[#C1C8D4]"></i>
            <p class="mt-3 text-[13px] font-semibold text-[#4F5D6B]">No Program Outcomes found</p>
            <p class="text-[12px] text-[#93A1AF] mt-1">Add Program Outcomes to this program first.</p>
        </div>
    @endif

</x-layout.offcanvas>
