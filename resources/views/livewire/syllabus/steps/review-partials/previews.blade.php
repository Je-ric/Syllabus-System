<x-wizard.section title="Syllabus Previews" icon="show" color="brand">
    <p class="text-sm text-slate-500 mb-4">Open a read-only preview before submitting. Each version opens in a new tab.</p>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

        <a href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}"
           target="_blank" rel="noopener"
           class="group relative flex flex-col gap-2.5 p-4 rounded-xl border border-[#e2e8f0] bg-[#f8fafc]
                  hover:border-[var(--clsu-green)] hover:bg-[#f0fdf4] transition-all duration-150">
            <span class="absolute top-0 left-5 right-5 h-[3px] rounded-b-full scale-x-0 group-hover:scale-x-100 origin-center transition-transform duration-200"
                  style="background: var(--clsu-yellow);"></span>
            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-[#e2e8f0]
                         group-hover:border-[var(--clsu-green)]/30 group-hover:bg-[#dcfce7] transition-all">
                <i class="bx bx-file-blank text-lg text-slate-400 group-hover:text-[var(--clsu-green)] transition-colors"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-700 group-hover:text-[var(--clsu-cobra)]">Complete</p>
                <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">Full syllabus with all fields — suitable for official submission and department records.</p>
            </div>
            <i class="bx bx-link-external text-xs text-slate-300 group-hover:text-[var(--clsu-green)] mt-auto self-end transition-colors"></i>
        </a>

        <a href="{{ route('syllabus.preview.abridged', ['syllabus' => $syllabus->id]) }}"
           target="_blank" rel="noopener"
           class="group relative flex flex-col gap-2.5 p-4 rounded-xl border border-[#e2e8f0] bg-[#f8fafc]
                  hover:border-[var(--clsu-green)] hover:bg-[#f0fdf4] transition-all duration-150">
            <span class="absolute top-0 left-5 right-5 h-[3px] rounded-b-full scale-x-0 group-hover:scale-x-100 origin-center transition-transform duration-200"
                  style="background: var(--clsu-yellow);"></span>
            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-[#e2e8f0]
                         group-hover:border-[var(--clsu-green)]/30 group-hover:bg-[#dcfce7] transition-all">
                <i class="bx bx-file text-lg text-slate-400 group-hover:text-[var(--clsu-green)] transition-colors"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-700 group-hover:text-[var(--clsu-cobra)]">Abridged</p>
                <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">Condensed version showing key topics and outcomes — suitable for student handouts.</p>
            </div>
            <i class="bx bx-link-external text-xs text-slate-300 group-hover:text-[var(--clsu-green)] mt-auto self-end transition-colors"></i>
        </a>

        <a href="{{ route('syllabus.preview.assessment', ['syllabus' => $syllabus->id]) }}"
           target="_blank" rel="noopener"
           class="group relative flex flex-col gap-2.5 p-4 rounded-xl border border-[#e2e8f0] bg-[#f8fafc]
                  hover:border-[var(--clsu-green)] hover:bg-[#f0fdf4] transition-all duration-150">
            <span class="absolute top-0 left-5 right-5 h-[3px] rounded-b-full scale-x-0 group-hover:scale-x-100 origin-center transition-transform duration-200"
                  style="background: var(--clsu-yellow);"></span>
            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-[#e2e8f0]
                         group-hover:border-[var(--clsu-green)]/30 group-hover:bg-[#dcfce7] transition-all">
                <i class="bx bx-clipboard text-lg text-slate-400 group-hover:text-[var(--clsu-green)] transition-colors"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-700 group-hover:text-[var(--clsu-cobra)]">Assessment Plan</p>
                <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">Weighted grading table — suitable for program review and CQI documentation.</p>
            </div>
            <i class="bx bx-link-external text-xs text-slate-300 group-hover:text-[var(--clsu-green)] mt-auto self-end transition-colors"></i>
        </a>

    </div>
</x-wizard.section>