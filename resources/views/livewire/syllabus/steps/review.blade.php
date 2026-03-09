<div>
    <x-wizard.step-header
        title="Review & Submit"
        description="Review all details before submitting for approval." />

    <div class="space-y-6">

        <x-wizard.section title="Previews" icon="show" color="slate">
            <div class="flex flex-col sm:flex-row gap-2">
                <x-button
                    href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}"
                    variant="outline"
                    target="_blank"
                    rel="noopener"
                    class="flex-1 justify-center">
                    Complete
                </x-button>

                <x-button
                    href="{{ route('syllabus.preview.abridged', ['syllabus' => $syllabus->id]) }}"
                    variant="outline"
                    target="_blank"
                    rel="noopener"
                    class="flex-1 justify-center">
                    Abridged
                </x-button>

                <x-button
                    href="{{ route('syllabus.preview.assessment', ['syllabus' => $syllabus->id]) }}"
                    variant="outline"
                    target="_blank"
                    rel="noopener"
                    class="flex-1 justify-center">
                    Assessment Plan
                </x-button>
            </div>
        </x-wizard.section>

        {{-- ── Academic Calendar ─────────────────────────────────────────── --}}
        <x-wizard.section title="Academic Calendar" icon="calendar" color="slate">
            @php
                $calendar = $academicCalendars->firstWhere('id', $academic_calendar_id);
                $calendarLabel = $calendar
                    ? ($calendar->academic_year . ' - ' . $calendar->getFormattedSemester())
                    : null;
                $calendarPeriod = $calendar
                    ? (($calendar->start_date?->format('M d, Y') ?? '—') . ' — ' . ($calendar->end_date?->format('M d, Y') ?? '—'))
                    : null;
            @endphp

            @if ($calendar)
                <x-wizard.info-card color="slate">
                    <x-wizard.info-row label="Academic Year & Semester" :value="$calendarLabel" bold />
                    <x-wizard.info-row label="Period" :value="$calendarPeriod" muted />
                </x-wizard.info-card>
            @else
                <x-feedback-status.alert type="error" title="Not selected">
                    Select an academic calendar to proceed.
                </x-feedback-status.alert>
            @endif
        </x-wizard.section>

        {{-- ── Course Components ─────────────────────────────────────────── --}}
        <x-wizard.section title="Course Components" icon="notepad" color="slate">
            @php
                $lec = $syllabus?->components?->firstWhere('type', 'LEC');
                $lab = $syllabus?->components?->firstWhere('type', 'LAB');
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <x-wizard.info-card title="Lecture (LEC)" icon="book-open" color="emerald">
                    <x-wizard.info-row label="Instructor"   :value="$lec?->instructor_name" bold />
                    <x-wizard.info-row label="Email"        :value="$lec?->instructor_email" />
                    <x-wizard.info-row label="Phone"        :value="$lec?->phone" />
                    <x-wizard.info-row label="Office"       :value="$lec?->office" />
                    <x-wizard.info-row label="Class Hours"  :value="$lec?->class_hours" />
                    <x-wizard.info-row label="Schedule"     :value="$lec?->schedule" />
                    <x-wizard.info-row label="Consultation" :value="$lec?->consultation_hours" />
                    <x-wizard.info-row label="Performance"  :value="$lec?->performance_standard" muted />
                </x-wizard.info-card>

                @if ($course && $course->has_lec_lab)
                    <x-wizard.info-card title="Laboratory (LAB)" icon="test-tube" color="blue">
                        <x-wizard.info-row label="Instructor"   :value="$lab?->instructor_name" bold />
                        <x-wizard.info-row label="Email"        :value="$lab?->instructor_email" />
                        <x-wizard.info-row label="Phone"        :value="$lab?->phone" />
                        <x-wizard.info-row label="Office"       :value="$lab?->office" />
                        <x-wizard.info-row label="Class Hours"  :value="$lab?->class_hours" />
                        <x-wizard.info-row label="Schedule"     :value="$lab?->schedule" />
                        <x-wizard.info-row label="Consultation" :value="$lab?->consultation_hours" />
                        <x-wizard.info-row label="Performance"  :value="$lab?->performance_standard" muted />
                    </x-wizard.info-card>
                @endif
            </div>
        </x-wizard.section>

        {{-- ── Course Outcomes ───────────────────────────────────────────── --}}
        <x-wizard.section title="Course Outcomes" icon="list-check" color="slate">
            <p class="text-xs text-slate-500 mb-3">
                Total: <span class="font-semibold text-slate-700">{{ count($courseOutcomes) }}</span>
            </p>
            @if (count($courseOutcomes) > 0)
                <ul class="space-y-2 text-sm">
                    @foreach ($courseOutcomes as $co)
                        <li class="flex items-start gap-2">
                            <span class="shrink-0 font-semibold text-emerald-700">{{ $co['co_code'] }}:</span>
                            <span class="text-slate-700">{{ $co['description'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-slate-500">No course outcomes defined yet.</p>
            @endif
        </x-wizard.section>

        {{-- ── Weekly Coverage ───────────────────────────────────────────── --}}
        <x-wizard.section title="Weekly Coverage" icon="calendar-week" color="slate">
            @if (isset($syllabusWeeks) && $syllabusWeeks->count() > 0)
                <x-wizard.info-card color="slate">
                    <x-wizard.info-row label="Total weeks" :value="$syllabusWeeks->count()" bold />
                    <div class="mt-2 space-y-1 text-xs text-slate-700">
                        @php
                            $examLabels = [
                                'first_term'  => '1st Term Exam',
                                'second_term' => '2nd Term Exam',
                                'final_term'  => 'Final Term Exam',
                            ];
                        @endphp
                        @foreach ($examLabels as $key => $label)
                            @php $weekNo = $examWeeks[$key] ?? null; @endphp
                            <x-wizard.info-row
                                :label="$label"
                                :value="$weekNo ? ('Week ' . $weekNo) : 'Not set'"
                                :muted="! $weekNo" />
                        @endforeach
                    </div>
                </x-wizard.info-card>
            @else
                <p class="text-sm text-slate-500">Weekly coverage not generated yet.</p>
            @endif
        </x-wizard.section>

        {{-- ── Latest saved version ──────────────────────────────────────── --}}
        @if ($latestComplete)
            <x-wizard.section title="Latest Saved Version" icon="cloud-upload" color="emerald">
                <x-wizard.info-card color="emerald">
                    <x-wizard.info-row label="Version"       :value="'v' . $latestComplete->version" bold />
                    <x-wizard.info-row label="Academic Year" :value="$latestComplete->academic_year" />
                    <x-wizard.info-row label="Semester"      :value="$latestComplete->semester" />
                    <x-wizard.info-row label="Saved at"      :value="$latestComplete->created_at?->format('M d, Y H:i')" muted />
                    <x-wizard.info-row label="File"          :value="basename($latestComplete->pdf_path)" muted />
                </x-wizard.info-card>
            </x-wizard.section>
        @endif

    </div>

    {{--
        ── Save as Done ──────────────────────────────────────────────────────
        IMPORTANT: use a plain <button wire:click="..."> here.
        Do NOT wrap in <x-button> — Blade components swallow wire:click
        attributes when they render to a non-button root element, causing the
        "Public method not found" error.
    --}}
    <div class="mt-6 flex flex-wrap items-center gap-4">
        <button
            type="button"
            wire:click="saveAsDone"
            wire:loading.attr="disabled"
            wire:target="saveAsDone"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                   bg-emerald-600 text-white text-sm font-semibold shadow-sm
                   hover:bg-emerald-700 active:bg-emerald-800
                   disabled:opacity-60 disabled:cursor-not-allowed
                   transition-colors duration-150">

            {{-- Idle label --}}
            <span wire:loading.remove wire:target="saveAsDone"
                  class="inline-flex items-center gap-2">
                <i class="bx bx-save text-base"></i>
                Save as Done
            </span>

            {{-- Spinning label while Livewire request is in flight --}}
            <span wire:loading wire:target="saveAsDone"
                  class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Generating PDF…
            </span>
        </button>

        {{-- Inline hint shown only while saving --}}
        <p wire:loading wire:target="saveAsDone"
           class="text-xs text-slate-500 animate-pulse">
            Building the syllabus PDF — this may take a few seconds.
        </p>
    </div>

    {{-- ── Warning notice ────────────────────────────────────────────────── --}}
    <div class="mt-6">
        <x-feedback-status.alert
            type="warning"
            title="Before you submit"
            message="Once you submit, the syllabus will be sent for review by the department chair. Make sure all information is correct." />
    </div>
</div>
