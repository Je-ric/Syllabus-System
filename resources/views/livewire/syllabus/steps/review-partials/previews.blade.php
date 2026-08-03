<x-wizard.section title="Syllabus Previews" icon="show" color="brand">

    <p class="text-[13px] text-slate-500 mb-5 leading-relaxed">
        Open a read-only preview in a new tab before submitting.
    </p>

    <div class="flex flex-col gap-3">

        {{-- Complete --}}
        <div class="flex items-center justify-between gap-4 px-4 py-3.5
                    rounded-[12px] border border-[#e2f5ec] bg-[#f6fef9]">
            <div class="flex items-center gap-3 min-w-0">
                <span class="shrink-0 flex items-center justify-center w-9 h-9 rounded-[10px]
                             bg-[#dcfce7] text-[#16a34a]">
                    <i class="bx bx-file-blank text-[18px] leading-none"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[13.5px] font-semibold text-[#0f172a]">Complete Syllabus</p>
                    <p class="text-[12px] text-slate-400 mt-0.5 leading-snug truncate">
                        Full OBTL format — official submission &amp; department records
                    </p>
                </div>
            </div>
            <x-ui.button
                href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}"
                variant="preview-complete"
                target="_blank"
                rel="noopener"
                class="shrink-0">
                <i class="bx bx-show text-[15px]"></i>
                Preview
                <i class="bx bx-link-external text-[11px] opacity-60"></i>
            </x-ui.button>
        </div>

        {{-- Abridged --}}
        <div class="flex items-center justify-between gap-4 px-4 py-3.5
                    rounded-[12px] border border-[#dbeafe] bg-[#f5f9ff]">
            <div class="flex items-center gap-3 min-w-0">
                <span class="shrink-0 flex items-center justify-center w-9 h-9 rounded-[10px]
                             bg-[#dbeafe] text-[#1d4ed8]">
                    <i class="bx bx-file text-[18px] leading-none"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[13.5px] font-semibold text-[#0f172a]">Abridged Syllabus</p>
                    <p class="text-[12px] text-slate-400 mt-0.5 leading-snug truncate">
                        Condensed topics &amp; outcomes — student handouts
                    </p>
                </div>
            </div>
            <x-ui.button
                href="{{ route('syllabus.preview.abridged', ['syllabus' => $syllabus->id]) }}"
                variant="preview-abridged"
                target="_blank"
                rel="noopener"
                class="shrink-0">
                <i class="bx bx-show text-[15px]"></i>
                Preview
                <i class="bx bx-link-external text-[11px] opacity-60"></i>
            </x-ui.button>
        </div>

        {{-- Assessment Plan --}}
        <div class="flex items-center justify-between gap-4 px-4 py-3.5
                    rounded-[12px] border border-[#e2e8f0] bg-[#f8fafc]">
            <div class="flex items-center gap-3 min-w-0">
                <span class="shrink-0 flex items-center justify-center w-9 h-9 rounded-[10px]
                             bg-[#e2e8f0] text-[#475569]">
                    <i class="bx bx-clipboard text-[18px] leading-none"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[13.5px] font-semibold text-[#0f172a]">Assessment Plan</p>
                    <p class="text-[12px] text-slate-400 mt-0.5 leading-snug truncate">
                        Weighted grading table — CQI &amp; program review
                    </p>
                </div>
            </div>
            <x-ui.button
                href="{{ route('syllabus.preview.assessment', ['syllabus' => $syllabus->id]) }}"
                variant="preview-assessment"
                target="_blank"
                rel="noopener"
                class="shrink-0">
                <i class="bx bx-show text-[15px]"></i>
                Preview
                <i class="bx bx-link-external text-[11px] opacity-60"></i>
            </x-ui.button>
        </div>

    </div>

</x-wizard.section>
