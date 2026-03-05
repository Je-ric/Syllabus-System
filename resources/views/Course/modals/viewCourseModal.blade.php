<x-modal.dialog id="viewCourseModal_{{ $course->id }}" maxWidth="max-w-3xl" width="w-11/12">

    <x-modal.header modalId="viewCourseModal_{{ $course->id }}">
        <div class="flex items-center gap-3 min-w-0">
            <div class="shrink-0 flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700">
                <i class="bx bx-book text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="font-bold text-slate-800 leading-tight truncate">{{ $course->course_title }}</p>
                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                    <span class="font-mono text-[11px] font-bold text-emerald-700 bg-emerald-50
                                 px-1.5 py-0.5 rounded-md ring-1 ring-emerald-200/60">
                        {{ $course->course_code }}
                    </span>
                    @if ($course->program)
                        <span class="text-xs text-slate-400">{{ $course->program->name }}</span>
                    @endif
                </div>
            </div>
        </div>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-5">

            {{-- ── Key facts row ─────────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">

                <div class="rounded-xl bg-slate-50 border border-slate-200/80 px-4 py-3 text-center">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Units</p>
                    <p class="text-2xl font-bold text-slate-800 leading-none">{{ $course->credit_units }}</p>
                </div>

                @if ($course->year_level)
                    <div class="rounded-xl bg-slate-50 border border-slate-200/80 px-4 py-3 text-center">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Year</p>
                        <p class="text-2xl font-bold text-slate-800 leading-none">{{ $course->year_level }}</p>
                    </div>
                @endif

                @if ($course->semester)
                    <div class="rounded-xl bg-slate-50 border border-slate-200/80 px-4 py-3 text-center">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Semester</p>
                        <p class="text-2xl font-bold text-slate-800 leading-none">{{ $course->semester }}</p>
                    </div>
                @endif

                <div class="rounded-xl border border-slate-200/80 px-4 py-3 text-center
                            {{ $course->has_lec_lab ? 'bg-blue-50' : 'bg-emerald-50' }}">
                    <p class="text-[10px] font-semibold uppercase tracking-widest
                               {{ $course->has_lec_lab ? 'text-blue-400' : 'text-emerald-400' }} mb-1">Type</p>
                    <p class="text-sm font-bold {{ $course->has_lec_lab ? 'text-blue-700' : 'text-emerald-700' }}">
                        {{ $course->has_lec_lab ? 'LEC + LAB' : 'Lecture' }}
                    </p>
                </div>
            </div>

            {{-- ── Meta row (pre/co-req, created by) ────────────────────────── --}}
            @if ($course->prerequisite || $course->corequisite || $course->creator)
                <div class="flex flex-wrap gap-3 text-sm">
                    @if ($course->prerequisite)
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-50
                                    border border-amber-200/60 text-amber-800">
                            <i class="bx bx-git-branch text-amber-500 text-sm"></i>
                            <span class="text-xs font-semibold uppercase tracking-wider text-amber-500">Pre:</span>
                            <span class="font-mono text-xs font-bold">{{ $course->prerequisite }}</span>
                        </div>
                    @endif
                    @if ($course->corequisite)
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-50
                                    border border-blue-200/60 text-blue-800">
                            <i class="bx bx-git-compare text-blue-500 text-sm"></i>
                            <span class="text-xs font-semibold uppercase tracking-wider text-blue-500">Co-req:</span>
                            <span class="font-mono text-xs font-bold">{{ $course->corequisite }}</span>
                        </div>
                    @endif
                    @if ($course->creator)
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-50
                                    border border-slate-200 text-slate-600 ml-auto">
                            <i class="bx bx-user text-slate-400 text-sm"></i>
                            <span class="text-xs text-slate-400">Created by</span>
                            <span class="text-xs font-semibold">{{ $course->creator->name }}</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ── Course description ─────────────────────────────────────────── --}}
            @if ($course->course_description)
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">
                        Course Description
                    </p>
                    <p class="text-sm text-slate-600 leading-relaxed bg-slate-50
                               rounded-xl border border-slate-200/80 px-4 py-3">
                        {{ $course->course_description }}
                    </p>
                </div>
            @endif

            {{-- ── PO Mapping ─────────────────────────────────────────────────── --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                        Program Outcomes Mapping
                    </p>
                    <span class="text-[10px] text-slate-400">
                        I — Introductory · E — Enabling · D — Demonstrating
                    </span>
                </div>

                @if ($course->programOutcomes->isEmpty())
                    <div class="rounded-xl border border-slate-200 border-dashed px-4 py-8 text-center">
                        <i class="bx bx-notepad text-2xl text-slate-200 block mb-2"></i>
                        <p class="text-sm text-slate-400">No program outcomes mapped yet.</p>
                    </div>
                @else
                    <div class="rounded-xl border border-slate-200 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-emerald-50/70 border-b border-slate-200">
                                    <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-widest text-emerald-700 w-16">PO</th>
                                    <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-widest text-emerald-700">Program Outcome</th>
                                    <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-widest text-emerald-700 w-16">IED</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($course->programOutcomes as $outcome)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-4 py-2.5 font-mono font-bold text-xs text-slate-700">
                                            {{ $outcome->po_code }}
                                        </td>
                                        <td class="px-4 py-2.5 text-slate-600 leading-relaxed text-sm">
                                            {{ $outcome->po_text }}
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <x-feedback-status.ied-badge :level="$outcome->pivot->ied" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button
            :modalId="'viewCourseModal_' . $course->id"
            text="Close"
            variant="cancel" />
    </x-modal.footer>

</x-modal.dialog>