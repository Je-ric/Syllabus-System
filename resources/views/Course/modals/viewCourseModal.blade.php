<x-modal.dialog id="viewCourseModal_{{ $course->id }}" maxWidth="max-w-5xl" width="w-11/12">

    <x-modal.header modalId="viewCourseModal_{{ $course->id }}">
        <div class="flex items-center gap-3 min-w-0">
            <div class="min-w-0">
                <p class="font-semibold text-slate-800 truncate">{{ $course->course_title }}</p>
                <span class="inline-flex items-center rounded-lg bg-slate-100 px-2 py-0.5
                            text-xs font-mono font-bold text-slate-600 ring-1 ring-slate-200">
                    {{ $course->course_code }}
                </span>
            </div>
        </div>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-5">

            {{-- ── Course info grid ──────────────────────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">

                <div class="rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-0.5">Program</p>
                    <p class="font-medium text-slate-800">{{ $course->program->name ?? 'N/A' }}</p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-0.5">Credit Units</p>
                    <p class="font-medium text-slate-800">{{ $course->credit_units }}</p>
                </div>

                @if ($course->year_level)
                    <div class="rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-0.5">Year Level</p>
                        <p class="font-medium text-slate-800">Year {{ $course->year_level }}</p>
                    </div>
                @endif

                @if ($course->semester)
                    <div class="rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-0.5">Semester</p>
                        <p class="font-medium text-slate-800">Semester {{ $course->semester }}</p>
                    </div>
                @endif

                <div class="rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-1.5">Lecture / Laboratory</p>
                    {{-- status-indicator instead of inline badge classes --}}
                    <x-feedback-status.status-indicator
                        :status="$course->has_lec_lab ? 'success' : 'neutral'"
                        :label="$course->has_lec_lab ? 'Has Lab' : 'Lecture Only'" />
                </div>

                @if ($course->prerequisite)
                    <div class="rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-0.5">Prerequisite</p>
                        <p class="font-medium text-slate-800 font-mono text-sm">{{ $course->prerequisite }}</p>
                    </div>
                @endif

                @if ($course->corequisite)
                    <div class="rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-0.5">Corequisite</p>
                        <p class="font-medium text-slate-800 font-mono text-sm">{{ $course->corequisite }}</p>
                    </div>
                @endif

                @if ($course->creator)
                    <div class="rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-0.5">Created By</p>
                        <p class="font-medium text-slate-800">{{ $course->creator->name ?? 'N/A' }}</p>
                    </div>
                @endif
            </div>

            {{-- Course description --}}
            @if ($course->course_description)
                <div class="rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-1.5">Course Description</p>
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $course->course_description }}</p>
                </div>
            @endif

            {{-- ── PO Mapping table ──────────────────────────────────────── --}}
            <div class="rounded-2xl border border-slate-200 overflow-hidden">

                <div class="px-5 py-3.5 border-b border-slate-200 bg-emerald-50/70 flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-800">
                        Program Outcomes Mapping (IED)
                    </p>
                    <span class="flex items-center gap-2 text-xs text-slate-400 whitespace-nowrap shrink-0">
                        I&nbsp;– Introductory &nbsp;·&nbsp; E&nbsp;– Enabling &nbsp;·&nbsp; D&nbsp;– Demonstrating
                    </span>
                </div>

                @if ($course->programOutcomes->isEmpty())
                    <div class="p-5">
                        <x-empty-state
                            icon="bx-notepad"
                            title="No outcomes mapped"
                            message="No program outcomes have been mapped to this course yet." />
                    </div>
                @else
                    <x-table.container class="rounded-none border-0 bg-transparent shadow-none">
                        <x-table.table class="w-full text-sm">
                            <x-table.head class="border-b border-slate-200">
                                <x-table.row>
                                    <x-table.th class="border-0 w-20">PO</x-table.th>
                                    <x-table.th class="border-0">Program Outcome</x-table.th>
                                    <x-table.th align="center" class="border-0 w-20">IED</x-table.th>
                                </x-table.row>
                            </x-table.head>
                            <x-table.body class="divide-y divide-slate-100">
                                @foreach ($course->programOutcomes as $outcome)
                                    <x-table.row class="hover:bg-slate-50/60 transition-colors">
                                        <x-table.td class="border-0 font-mono font-bold text-xs text-slate-700">
                                            {{ $outcome->po_code }}
                                        </x-table.td>
                                        <x-table.td class="border-0 text-slate-700 leading-relaxed">
                                            {{ $outcome->po_text }}
                                        </x-table.td>
                                        <x-table.td align="center" class="border-0">
                                            <x-feedback-status.ied-badge :level="$outcome->pivot->ied" />
                                        </x-table.td>
                                    </x-table.row>
                                @endforeach
                            </x-table.body>
                        </x-table.table>
                    </x-table.container>
                @endif

            </div>
        </div>
    </x-modal.body>

    <x-modal.footer>
        {{-- "cancel" variant is the correct choice for a close/dismiss button --}}
        <x-modal.close-button
            :modalId="'viewCourseModal_' . $course->id"
            text="Close"
            variant="cancel" />
    </x-modal.footer>

</x-modal.dialog>
