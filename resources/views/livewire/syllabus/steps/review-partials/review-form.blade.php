{{--
    F.003 REVIEW FORM — AUTHOR PANEL
    Parts A–D (author fills), plus read-only status display for Parts F, H, I.
    Included in: livewire/syllabus/steps/review.blade.php
--}}

@php
    /** @var \App\Models\Syllabus $syllabus */
    /** @var \App\Models\SyllabusReviewForm|null $reviewForm */

    $rf             = $reviewForm;
    $classification = $rf?->classification;
    $isSubmitted    = $rf?->submitted_at !== null;
    $decision       = $rf?->decision;

    $updatingOptions = [
        'schedule_calendar'                     => 'Schedule/calendar changes',
        'faculty_contact'                       => 'Faculty/contact details',
        'references_textbooks'                  => 'References/textbooks/resources',
        'typographical_formatting'              => 'Typographical/formatting corrections',
        'minor_administrative'                  => 'Minor administrative updates',
        'other_updating'                        => 'Other',
    ];
    $revisionOptions = [
        'stakeholder_feedback'                  => 'Stakeholder feedback',
        'cqi_findings'                          => 'CQI findings/action plan',
        'policy_curricular'                     => 'Policy/curricular changes',
        'accreditation_qa'                      => 'Accreditation/QA recommendation',
        'change_in_cos_po_mapping'              => 'Change in COs/PO-CO mapping',
        'change_in_grading_assessments_content' => 'Change in grading/major assessments/core content',
        'other_revision'                        => 'Other',
    ];
    $attachmentOptions = [
        'draft_syllabus'   => 'Draft course syllabus for review',
        'cqi_report'       => 'CQI report (ISO audit, AUN-QA, accreditation findings)',
        'feedback_summary' => 'Feedback summary (student, stakeholder, alumni/employer)',
        'policy_memo'      => 'Policy memo (CHED, university, college/department)',
        'mapping_evidence' => 'Mapping/assessment evidence (curriculum map, PO-CO map)',
        'other'            => 'Other (specify)',
    ];

    $selectedChanges      = $rf?->natureOfChange?->pluck('change_type')->toArray() ?? [];
    $submittedAttachments = $rf?->attachments ?? collect();

    $decisionLabels = [
        'approved_as_updating'      => 'Approved as Updating',
        'approved_as_revision'      => 'Approved as Revision',
        'approved_with_corrections' => 'Approved with Corrections',
        'returned_for_revision'     => 'Returned for Revision',
        'reclassified_as_revision'  => 'Reclassified as Revision',
    ];
    $decisionColorClass = [
        'approved_as_updating'      => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'approved_as_revision'      => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'approved_with_corrections' => 'bg-amber-100 text-amber-800 border-amber-200',
        'returned_for_revision'     => 'bg-rose-100 text-rose-800 border-rose-200',
        'reclassified_as_revision'  => 'bg-blue-100 text-blue-800 border-blue-200',
    ];
    $decisionIcon = [
        'approved_as_updating'      => 'bx-check-circle',
        'approved_as_revision'      => 'bx-check-circle',
        'approved_with_corrections' => 'bx-error',
        'returned_for_revision'     => 'bx-x-circle',
        'reclassified_as_revision'  => 'bx-revision',
    ];
@endphp

<div
    x-data="{
        open: true,
        classification: @js($classification),
        selectedChanges: @js($selectedChanges),
        attachments: @js($submittedAttachments->pluck('attachment_type')->toArray()),
        otherLabel: @js($submittedAttachments->firstWhere('attachment_type', 'other')?->other_label ?? ''),
        partHResponse: @js($rf?->part_h_faculty_response ?? ''),
        savingClass: false,
        savingChanges: false,
        savingAttachments: false,
        savingPartH: false,
        submitting: false,

        toggleChange(key) {
            const i = this.selectedChanges.indexOf(key);
            i === -1 ? this.selectedChanges.push(key) : this.selectedChanges.splice(i, 1);
        },
        toggleAttachment(key) {
            const i = this.attachments.indexOf(key);
            i === -1 ? this.attachments.push(key) : this.attachments.splice(i, 1);
        },
        async saveClassification() {
            if (!this.classification) return;
            this.savingClass = true;
            await $wire.saveReviewFormClassification(this.classification);
            this.savingClass = false;
        },
        async saveChanges() {
            this.savingChanges = true;
            await $wire.saveReviewFormChanges(this.selectedChanges);
            this.savingChanges = false;
        },
        async saveAttachments() {
            this.savingAttachments = true;
            await $wire.saveReviewFormAttachments(this.attachments, this.otherLabel);
            this.savingAttachments = false;
        },
        async savePartH() {
            if (!this.partHResponse.trim()) return;
            this.savingPartH = true;
            await $wire.savePartHResponse(this.partHResponse);
            this.savingPartH = false;
        },
        async submitReviewForm() {
            this.submitting = true;
            await $wire.submitReviewForm();
            this.submitting = false;
        }
    }"
    class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden"
    style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

    {{-- Header --}}
    <button type="button" x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-[#f8fafc] transition-colors focus:outline-none">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg"
                  style="background:#f0fdf4; color:var(--clsu-green);">
                <i class="bx bx-file text-base leading-none"></i>
            </span>
            <div>
                <p class="text-sm font-bold text-[#0f172a]">F.003 Review Form</p>
                <p class="text-xs text-[#94a3b8] mt-0.5">
                    Integrated OBTL Syllabus Review Form
                    @if ($isSubmitted)
                        &middot; <span class="font-semibold text-emerald-600">Submitted {{ \Carbon\Carbon::parse($rf->submitted_at)->format('M d, Y') }}</span>
                    @elseif ($classification)
                        &middot; <span class="font-semibold text-amber-600">Draft — {{ ucfirst($classification) }}</span>
                    @else
                        &middot; <span class="text-slate-400">Not started</span>
                    @endif
                </p>
            </div>
        </div>
        <i class="bx text-[#94a3b8] text-lg transition-transform duration-200"
           x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
    </button>

    <div x-show="open" x-collapse>
        <div class="border-t border-[#e2e8f0] divide-y divide-[#e2e8f0]">

            {{-- PART A — Course Identification (read-only) --}}
            <div class="px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3">
                    Part A — Course Identification
                    <span class="text-[#94a3b8] font-normal normal-case tracking-normal">(auto-filled)</span>
                </p>
                @php
                    $course   = $syllabus->course;
                    $program  = $course?->program;
                    $dept     = $program?->departments?->first();
                    $college  = $dept?->college;
                    $calendar = $syllabus->academicCalendar;
                    $lecComp  = $syllabus->components->firstWhere('type', 'LEC');
                    $labComp  = $syllabus->components->firstWhere('type', 'LAB');
                    $facultyStr = collect([$lecComp?->instructor_name, $labComp?->instructor_name])
                        ->filter()->unique()->implode(', ') ?: ($syllabus->preparer?->name ?? '—');
                    $partARows = [
                        'Degree Program'   => $program?->name ?? '—',
                        'College'          => $college?->name ?? '—',
                        'Department'       => $dept?->name ?? '—',
                        'Course Code'      => $course?->course_code ?? '—',
                        'Course Title'     => $course?->course_title ?? '—',
                        'Prerequisite'     => $course?->prerequisite ?? 'None',
                        'Co-requisite'     => $course?->corequisite ?? 'None',
                        'Credit Units'     => ($course?->lec_units ?? 0).' lec'.($course?->lab_units ? ' + '.$course->lab_units.' lab' : ''),
                        'Class Hours/Week' => ($course?->lec_class_hours ?? 0).' hr lec'.($course?->lab_class_hours ? ' + '.$course->lab_class_hours.' hr lab' : ''),
                        'Semester & AY'    => ($calendar?->semester ?? '—').' '.($calendar?->academic_year ?? ''),
                        'Faculty Member/s' => $facultyStr,
                        'Course Lead'      => $rf?->course_lead_name ?? $syllabus->preparer?->name ?? '—',
                    ];
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-3">
                    @foreach ($partARows as $label => $value)
                        <div>
                            <p class="text-xs text-slate-400">{{ $label }}</p>
                            <p class="text-sm font-medium text-slate-800 truncate" title="{{ $value }}">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- PART B — Classification --}}
            <div class="px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-1">
                    Part B — Classification of Submission
                </p>
                <p class="text-xs text-slate-500 mb-3 leading-relaxed">
                    Select <strong>Updating</strong> for minor/routine changes only.
                    Select <strong>Revision</strong> when Course Outcomes, grading, core content, or CO-PO mapping changes, or when driven by stakeholder feedback, policy, or CQI findings.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label x-bind:class="classification === 'updating'
                            ? 'border-emerald-400 bg-emerald-50/60 ring-1 ring-emerald-300'
                            : 'border-[#e2e8f0] hover:border-emerald-200'"
                        class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all">
                        <input type="radio" name="rf_classification" value="updating"
                               x-model="classification" class="mt-0.5 accent-emerald-600">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Updating</p>
                            <p class="text-xs text-slate-500 mt-0.5">Minor, routine changes — reviewed by CQI Chair only</p>
                        </div>
                    </label>
                    <label x-bind:class="classification === 'revision'
                            ? 'border-blue-400 bg-blue-50/60 ring-1 ring-blue-300'
                            : 'border-[#e2e8f0] hover:border-blue-200'"
                        class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all">
                        <input type="radio" name="rf_classification" value="revision"
                               x-model="classification" class="mt-0.5 accent-blue-600">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Revision</p>
                            <p class="text-xs text-slate-500 mt-0.5">Substantive changes — reviewed by full CQI Committee</p>
                        </div>
                    </label>
                </div>
                <div class="mt-3 flex justify-end">
                    <x-ui.button type="button" variant="sm-add"
                        x-on:click="saveClassification()"
                        x-bind:disabled="savingClass || !classification">
                        <i x-show="!savingClass" class="bx bx-check leading-none"></i>
                        <svg x-show="savingClass" x-cloak class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="savingClass ? 'Saving…' : 'Save Classification'"></span>
                    </x-ui.button>
                </div>
            </div>

            {{-- PART C — Nature of Change --}}
            <div class="px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3">
                    Part C — Nature of Change
                </p>
                <div x-show="classification === 'updating' || !classification">
                    <p class="text-xs font-medium text-slate-500 mb-2">If Updating — check all applicable minor/routine changes:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
                        @foreach ($updatingOptions as $key => $label)
                            <label class="flex items-center gap-2.5 text-sm cursor-pointer py-1.5 px-2 rounded-lg hover:bg-slate-50">
                                <input type="checkbox" value="{{ $key }}"
                                       x-bind:checked="selectedChanges.includes('{{ $key }}')"
                                       x-on:change="toggleChange('{{ $key }}')"
                                       class="rounded accent-emerald-600">
                                <span class="text-slate-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div x-show="classification === 'revision'" x-cloak>
                    <p class="text-xs font-medium text-slate-500 mb-2">If Revision — check all applicable substantive bases:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
                        @foreach ($revisionOptions as $key => $label)
                            <label class="flex items-center gap-2.5 text-sm cursor-pointer py-1.5 px-2 rounded-lg hover:bg-slate-50">
                                <input type="checkbox" value="{{ $key }}"
                                       x-bind:checked="selectedChanges.includes('{{ $key }}')"
                                       x-on:change="toggleChange('{{ $key }}')"
                                       class="rounded accent-blue-600">
                                <span class="text-slate-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="mt-3 flex justify-end">
                    <x-ui.button type="button" variant="sm-add"
                        x-on:click="saveChanges()"
                        x-bind:disabled="savingChanges">
                        <i x-show="!savingChanges" class="bx bx-check leading-none"></i>
                        <svg x-show="savingChanges" x-cloak class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="savingChanges ? 'Saving…' : 'Save Changes'"></span>
                    </x-ui.button>
                </div>
            </div>

            {{-- PART D — Documentary Attachments --}}
            <div class="px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-1">
                    Part D — Documentary Attachments
                </p>
                <p class="text-xs text-slate-500 mb-3">Check all documents you are submitting with the syllabus.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
                    @foreach ($attachmentOptions as $key => $label)
                        <label class="flex items-center gap-2.5 text-sm cursor-pointer py-1.5 px-2 rounded-lg hover:bg-slate-50">
                            <input type="checkbox" value="{{ $key }}"
                                   x-bind:checked="attachments.includes('{{ $key }}')"
                                   x-on:change="toggleAttachment('{{ $key }}')"
                                   class="rounded accent-emerald-600">
                            <span class="text-slate-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <div x-show="attachments.includes('other')" x-cloak class="mt-2">
                    <input type="text" x-model="otherLabel"
                           placeholder="Describe the other attachment…"
                           class="w-full text-sm rounded-lg border border-[#e2e8f0] px-3 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-400">
                </div>
                <div class="mt-3 flex justify-end">
                    <x-ui.button type="button" variant="sm-add"
                        x-on:click="saveAttachments()"
                        x-bind:disabled="savingAttachments">
                        <i x-show="!savingAttachments" class="bx bx-check leading-none"></i>
                        <svg x-show="savingAttachments" x-cloak class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="savingAttachments ? 'Saving…' : 'Save Attachments'"></span>
                    </x-ui.button>
                </div>
            </div>

            {{-- Submit for Review --}}
            @if (!$isSubmitted)
            <div class="px-5 py-4 bg-emerald-50/40">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <p class="text-sm font-bold text-slate-800">Submit Review Form</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Reviewers will be notified. You can still edit the syllabus itself after submitting.
                        </p>
                    </div>
                    <x-ui.button type="button" variant="primary"
                        x-on:click="submitReviewForm()"
                        x-bind:disabled="submitting || !classification">
                        <i x-show="!submitting" class="bx bx-send leading-none"></i>
                        <svg x-show="submitting" x-cloak class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="submitting ? 'Submitting…' : 'Submit for Review'"></span>
                    </x-ui.button>
                </div>
            </div>
            @endif

            {{-- Part F — Decision (read-only) --}}
            @if ($decision)
            <div class="px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3">
                    Part F — Committee Decision
                </p>
                <div class="flex items-center gap-3 p-3 rounded-xl border {{ $decisionColorClass[$decision] ?? 'bg-slate-100 text-slate-800 border-slate-200' }}">
                    <i class="bx {{ $decisionIcon[$decision] ?? 'bx-info-circle' }} text-xl shrink-0"></i>
                    <div>
                        <p class="text-sm font-bold">{{ $decisionLabels[$decision] }}</p>
                        @if ($rf->decision_made_at)
                            <p class="text-xs mt-0.5 opacity-70">{{ \Carbon\Carbon::parse($rf->decision_made_at)->format('M d, Y') }}</p>
                        @endif
                    </div>
                </div>
                @if ($rf->required_actions)
                    <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200">
                        <p class="text-xs font-bold text-amber-800 mb-1">Required Actions:</p>
                        <p class="text-sm text-amber-900 whitespace-pre-wrap">{{ $rf->required_actions }}</p>
                        @if ($rf->target_compliance_date)
                            <p class="text-xs text-amber-700 mt-1 font-medium">
                                Deadline: {{ \Carbon\Carbon::parse($rf->target_compliance_date)->format('M d, Y') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
            @endif

            {{-- Part H — Faculty compliance response --}}
            @if ($decision === 'approved_with_corrections')
            <div class="px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3">
                    Part H — Action Taken on Review Comments
                </p>
                @if ($rf->part_h_verified_at)
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800">
                        <i class="bx bx-check-circle text-lg shrink-0"></i>
                        <span>Verified by <strong>{{ $rf->partHVerifier?->name ?? '—' }}</strong>
                            on {{ \Carbon\Carbon::parse($rf->part_h_verified_at)->format('M d, Y') }}</span>
                    </div>
                @else
                    <p class="text-xs text-slate-500 mb-2">
                        Describe the corrections you made in response to the committee's required actions.
                    </p>
                    <textarea x-model="partHResponse" rows="4"
                        placeholder="Describe the corrections made…"
                        class="w-full text-sm rounded-xl border border-[#e2e8f0] px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-400 resize-none"></textarea>
                    <div class="mt-2 flex justify-end">
                        <x-ui.button type="button" variant="sm-add"
                            x-on:click="savePartH()"
                            x-bind:disabled="savingPartH || !partHResponse.trim()">
                            <i x-show="!savingPartH" class="bx bx-check leading-none"></i>
                            <svg x-show="savingPartH" x-cloak class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span x-text="savingPartH ? 'Saving…' : 'Submit Response'"></span>
                        </x-ui.button>
                    </div>
                @endif
            </div>
            @endif

            {{-- Part I — Approval status (read-only) --}}
            @if ($rf?->recommended_by_chair_id || $rf?->approved_by_dean_id)
            <div class="px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3">
                    Part I — Approval Authority
                </p>
                <div class="space-y-2">
                    @if ($rf->recommended_by_chair_id)
                        <div class="flex items-center gap-2 text-sm">
                            <i class="bx bx-check text-emerald-500 text-base shrink-0"></i>
                            <span class="text-slate-600">Recommended by
                                <strong>{{ $rf->recommendedByChair?->name ?? '—' }}</strong>
                                on {{ \Carbon\Carbon::parse($rf->recommended_by_chair_at)->format('M d, Y') }}
                            </span>
                        </div>
                    @endif
                    @if ($rf->approved_by_dean_id)
                        <div class="flex items-center gap-2 text-sm">
                            <i class="bx bx-check-double text-emerald-600 text-base shrink-0"></i>
                            <span class="text-slate-600">Approved by Dean
                                <strong>{{ $rf->approvedByDean?->name ?? '—' }}</strong>
                                on {{ \Carbon\Carbon::parse($rf->approved_by_dean_at)->format('M d, Y') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Preview link --}}
            <div class="px-5 py-3 bg-slate-50/60 flex items-center justify-between gap-3">
                <p class="text-xs text-slate-400">
                    Preview the printable F.003 form to verify all information before submitting.
                </p>
                <a href="{{ route('syllabus.review-form.preview', ['syllabus' => $syllabus->id]) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition-colors shrink-0">
                    <i class="bx bx-link-external text-sm"></i> Preview F.003
                </a>
            </div>

        </div>
    </div>
</div>
