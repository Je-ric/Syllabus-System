<x-wizard.section title="Syllabus Previews" icon="show" color="brand">
    <p class="text-sm text-slate-500 mb-4">Open a read-only preview before submitting. Each version opens in a new tab.</p>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

        <a href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}"
           target="_blank" rel="noopener"
           class="group flex flex-col gap-2.5 p-4 rounded-xl border border-[#e2e8f0] bg-[#f8fafc]
                  hover:border-[#009639] hover:bg-[#f0fdf4] transition-all duration-150">
            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-[#e2e8f0]
                         group-hover:border-[#bbf7d0] group-hover:bg-[#dcfce7] transition-all">
                <i class="bx bx-file-blank text-lg text-slate-400 group-hover:text-[#16a34a] transition-colors"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-700 group-hover:text-[#166534]">Complete</p>
                <p class="text-xs text-slate-400 mt-0.5">Full syllabus document</p>
            </div>
            <i class="bx bx-link-external text-xs text-slate-300 group-hover:text-[#16a34a] mt-auto self-end transition-colors"></i>
        </a>

        <a href="{{ route('syllabus.preview.abridged', ['syllabus' => $syllabus->id]) }}"
           target="_blank" rel="noopener"
           class="group flex flex-col gap-2.5 p-4 rounded-xl border border-[#e2e8f0] bg-[#f8fafc]
                  hover:border-[#009639] hover:bg-[#f0fdf4] transition-all duration-150">
            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-[#e2e8f0]
                         group-hover:border-[#bbf7d0] group-hover:bg-[#dcfce7] transition-all">
                <i class="bx bx-file text-lg text-slate-400 group-hover:text-[#16a34a] transition-colors"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-700 group-hover:text-[#166534]">Abridged</p>
                <p class="text-xs text-slate-400 mt-0.5">Condensed version</p>
            </div>
            <i class="bx bx-link-external text-xs text-slate-300 group-hover:text-[#16a34a] mt-auto self-end transition-colors"></i>
        </a>

        <a href="{{ route('syllabus.preview.assessment', ['syllabus' => $syllabus->id]) }}"
           target="_blank" rel="noopener"
           class="group flex flex-col gap-2.5 p-4 rounded-xl border border-[#e2e8f0] bg-[#f8fafc]
                  hover:border-[#009639] hover:bg-[#f0fdf4] transition-all duration-150">
            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-[#e2e8f0]
                         group-hover:border-[#bbf7d0] group-hover:bg-[#dcfce7] transition-all">
                <i class="bx bx-clipboard text-lg text-slate-400 group-hover:text-[#16a34a] transition-colors"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-700 group-hover:text-[#166534]">Assessment Plan</p>
                <p class="text-xs text-slate-400 mt-0.5">Evaluation summary</p>
            </div>
            <i class="bx bx-link-external text-xs text-slate-300 group-hover:text-[#16a34a] mt-auto self-end transition-colors"></i>
        </a>

    </div>
</x-wizard.section>
