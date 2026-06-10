<x-offcanvas
    id="program-outcomes-drawer"
    title="Program Outcomes Reference"
    width="w-96">

    @if ($program && $program->outcomes->count() > 0)
        <div class="space-y-3">
            <p class="text-[12px] text-[#94a3b8]">
                Reference list of all Program Outcomes (POs) for this program.
            </p>

            @foreach ($program->outcomes as $index => $outcome)
                <div class="flex items-start gap-3 rounded-xl border border-[#e2e8f0] bg-white px-4 py-3
                            hover:border-emerald-200 hover:bg-emerald-50/30 transition-colors">

                    <span class="mt-0.5 shrink-0 inline-flex items-center justify-center
                                min-w-12 h-9 rounded-lg bg-[#dcfce7] text-[#166534]
                                text-[12px] font-bold ring-1 ring-[#bbf7d0]">
                        {{ strtoupper($outcome->po_code) }}
                    </span>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-0.5">
                            Program Outcome {{ $index + 1 }}
                        </p>

                        <p class="text-[13px] text-[#475569] leading-relaxed">
                            {{ $outcome->po_text }}
                        </p>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <i class="bx bx-book-open text-4xl text-[#94a3b8]"></i>

            <p class="mt-3 text-[13px] font-semibold text-[#475569]">
                No Program Outcomes found
            </p>

            <p class="text-[12px] text-[#94a3b8] mt-1">
                Add Program Outcomes to this program first.
            </p>
        </div>
    @endif

</x-offcanvas>
