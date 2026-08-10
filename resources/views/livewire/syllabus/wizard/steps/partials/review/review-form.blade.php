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
    
    // Calculate default course lead from LEC/LAB components
    $lecComp  = $syllabus->components->firstWhere('type', 'LEC');
    $labComp  = $syllabus->components->firstWhere('type', 'LAB');
    $defaultCourseLead = collect([$lecComp?->instructor_name, $labComp?->instructor_name])
        ->filter()->unique()->implode(' & ') ?: ($syllabus->preparer?->name ?? '—');

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
        open: false,
        classification: @js($classification),
        selectedChanges: @js($selectedChanges),
        attachments: @js($submittedAttachments->pluck('attachment_type')->toArray()),
        otherLabel: @js($submittedAttachments->firstWhere('attachment_type', 'other')?->other_label ?? ''),
        partHResponse: @js($rf?->part_h_faculty_response ?? ''),
        courseLeadName: @js($rf?->course_lead_name ?? $defaultCourseLead ?? ''),
        savingClass: false,
        savingChanges: false,
        savingAttachments: false,
        savingPartH: false,
        savingCourseLead: false,
        courseLeadSaved: false,
        submitting: false,
        resubmitting: false,
        showClassificationHint: false,

        get canSubmit() {
            return this.classification && 
                   this.selectedChanges.length > 0 && 
                   this.attachments.length > 0 &&
                   !(this.attachments.includes('other') && !this.otherLabel.trim());
        },

        get submitButtonText() {
            if (!this.classification) return 'Select classification first';
            if (this.selectedChanges.length === 0) return 'Select nature of change';
            if (this.attachments.length === 0) return 'Select attachments';
            if (this.attachments.includes('other') && !this.otherLabel.trim()) return 'Describe other attachment';
            return 'Submit for Review';
        },

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
            if (this.attachments.includes('other') && !this.otherLabel.trim()) {
                this.$dispatch('lw-toast', { type: 'error', message: 'Please describe the other attachment.' });
                return;
            }
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
        async resubmitForReview() {
            if (!this.partHResponse.trim()) return;
            this.resubmitting = true;
            await $wire.resubmitForReview(this.partHResponse);
            this.resubmitting = false;
        },
        async saveCourseLead() {
            if (!this.courseLeadName.trim()) return;
            this.savingCourseLead = true;
            this.courseLeadSaved = false;
            await $wire.saveCourseLeadName(this.courseLeadName);
            this.savingCourseLead = false;
            this.courseLeadSaved = true;
            setTimeout(() => this.courseLeadSaved = false, 2000);
        },
        async submitReviewForm() {
            if (!this.canSubmit) {
                this.$dispatch('lw-toast', { type: 'error', message: this.submitButtonText });
                return;
            }
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
                <p class="text-sm font-bold text-[#0f172a]">
                    F.003 Review Form
                    <span class="ml-1.5 text-xs font-medium text-slate-400 normal-case">(Optional)</span>
                </p>
                <p class="text-xs text-[#94a3b8] mt-0.5">
                    Integrated OBTL Syllabus Review Form
                    @if ($isSubmitted)
                        &middot; <span class="font-semibold text-emerald-600">Submitted {{ \Carbon\Carbon::parse($rf->submitted_at)->format('M d, Y') }}</span>
                    @elseif ($classification)
                        &middot; <span class="font-semibold text-amber-600">Draft — {{ ucfirst($classification) }}</span>
                    @else
                        &middot; <span class="text-slate-400 italic">Optional — fill in when ready</span>
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
                    
                    // Auto-populate course lead with both LEC and LAB instructors if available
                    $defaultCourseLead = collect([$lecComp?->instructor_name, $labComp?->instructor_name])
                        ->filter()->unique()->implode(' & ') ?: ($syllabus->preparer?->name ?? '—');
                    
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
                
                {{-- Editable Course Lead Field --}}
                <div class="mt-3 pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs text-slate-400">Course Lead</p>
                        <p class="text-[10px] text-emerald-600">
                            <i class="bx bx-edit-alt mr-0.5"></i>Editable by author
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" 
                               x-model="courseLeadName"
                               x-on:change="saveCourseLead()"
                               placeholder="Enter course lead name…"
                               class="flex-1 text-sm rounded-lg border border-[#e2e8f0] px-3 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-400">
                        <i x-show="savingCourseLead" x-cloak class="bx bx-loader-alt animate-spin text-emerald-600"></i>
                        <i x-show="!savingCourseLead && courseLeadSaved" x-cloak class="bx bx-check-circle text-emerald-600"></i>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">
                        Auto-populated from LEC/LAB instructors. Edit if different.
                    </p>
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
                @if (!$classification)
                <div class="p-3 rounded-lg bg-blue-50 border border-blue-200 mb-3">
                    <p class="text-xs text-blue-800">
                        <i class="bx bx-lightbulb mr-1"></i>
                        <strong>Remember:</strong> The classification determines the review process. Updating is reviewed by CQI Chair only; Revision requires full committee review.
                    </p>
                </div>
                @endif
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
                <div x-show="selectedChanges.length === 0 && classification" x-cloak class="p-3 rounded-lg bg-amber-50 border border-amber-200 mb-3">
                    <p class="text-xs text-amber-800">
                        <i class="bx bx-info-circle mr-1"></i>
                        <strong>Note:</strong> Select all applicable options. This helps reviewers understand the scope of changes.
                    </p>
                </div>
                <div x-show="classification === 'updating'" x-cloak>
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
                <div x-show="!classification" class="p-3 rounded-lg bg-amber-50 border border-amber-200">
                    <p class="text-xs text-amber-700">
                        <i class="bx bx-info-circle mr-1"></i>
                        Select a classification above to see the applicable nature of change options.
                    </p>
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
                <div x-show="attachments.length === 0" x-cloak class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 mb-3">
                    <p class="text-xs text-emerald-800">
                        <i class="bx bx-check-shield mr-1"></i>
                        <strong>Tip:</strong> Ensure all supporting documents are available. Reviewers may request additional evidence if needed.
                    </p>
                </div>
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
                           x-bind:class="(attachments.includes('other') && !otherLabel.trim()) ? 'border-rose-300 focus:ring-rose-400' : 'border-[#e2e8f0] focus:ring-emerald-400'"
                           class="w-full text-sm rounded-lg border px-3 py-2 focus:outline-none focus:ring-1">
                    <p x-show="attachments.includes('other') && !otherLabel.trim()" x-cloak class="text-xs text-rose-600 mt-1">
                        <i class="bx bx-error-circle mr-1"></i>Please describe the other attachment.
                    </p>
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
                        <div x-show="!canSubmit" x-cloak class="mt-2 p-2 rounded-lg bg-emerald-100 border border-emerald-200">
                            <p class="text-[11px] text-emerald-800">
                                <i class="bx bx-check-double mr-1"></i>
                                <strong>Before submitting:</strong> Ensure classification, nature of change, and attachments are complete.
                            </p>
                        </div>
                    </div>
                    <x-ui.button type="button" variant="primary"
                        x-on:click="submitReviewForm()"
                        x-bind:disabled="submitting || !canSubmit"
                        x-bind:class="!canSubmit ? 'opacity-70 cursor-not-allowed' : ''">
                        <i x-show="!submitting" class="bx bx-send leading-none"></i>
                        <svg x-show="submitting" x-cloak class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="submitting ? 'Submitting…' : (canSubmit ? 'Submit for Review' : submitButtonText)"></span>
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

                {{-- Decision maker info --}}
                @if ($rf->decision_made_by)
                    <div class="mt-2 flex items-center gap-2 text-xs text-slate-600">
                        <i class="bx bx-user text-slate-400"></i>
                        <span>Decision by: <strong>{{ $rf->decisionMaker?->name ?? '—' }}</strong></span>
                    </div>
                @endif

                {{-- Decision-specific guidance --}}
                @if ($decision === 'approved_with_corrections' && !$rf->part_h_verified_at)
                    <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200">
                        <p class="text-xs text-amber-800">
                            <i class="bx bx-info-circle mr-1"></i>
                            <strong>Next Step:</strong> Complete Part H below to describe the corrections you made.
                        </p>
                    </div>
                @elseif ($decision === 'returned_for_revision')
                    <div class="mt-3 p-3 rounded-xl bg-rose-50 border border-rose-200">
                        <p class="text-xs text-rose-800">
                            <i class="bx bx-info-circle mr-1"></i>
                            <strong>Next Step:</strong> Make the required revisions and resubmit for review.
                        </p>
                    </div>
                @elseif ($decision === 'reclassified_as_revision')
                    <div class="mt-3 p-3 rounded-xl bg-blue-50 border border-blue-200">
                        <p class="text-xs text-blue-800">
                            <i class="bx bx-info-circle mr-1"></i>
                            <strong>Next Step:</strong> Assign new reviewers for the revision track.
                        </p>
                    </div>
                @endif

                @if ($decision === 'returned_for_revision' && $rf->required_actions)
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
            @if ($decision === 'approved_with_corrections' || $decision === 'returned_for_revision')
            <div class="px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3">
                    Part H — Action Taken on Review Comments
                </p>
                {{-- Faculty response display (always shown if exists) --}}
                @if ($rf->part_h_faculty_response)
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-bold text-slate-700">Faculty Response</p>
                            @if ($rf->part_h_faculty_response_updated_at)
                                <p class="text-[10px] text-slate-500">
                                    Submitted {{ \Carbon\Carbon::parse($rf->part_h_faculty_response_updated_at)->format('M d, Y') }}
                                </p>
                            @endif
                        </div>
                        <p class="text-xs text-slate-600 whitespace-pre-wrap">{{ $rf->part_h_faculty_response }}</p>
                    </div>
                @endif

                {{-- Verification status --}}
                @if ($rf->part_h_verified_at)
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 mb-3">
                        <i class="bx bx-check-circle text-lg shrink-0"></i>
                        <span>Verified by <strong>{{ $rf->partHVerifier?->name ?? '—' }}</strong>
                            on {{ \Carbon\Carbon::parse($rf->part_h_verified_at)->format('M d, Y') }}</span>
                    </div>
                @endif

                {{-- Input form (shown if not verified or can be updated) --}}
                @if (!$rf->part_h_verified_at)
                    @if (!$rf->part_h_faculty_response)
                    <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 mb-2">
                        <p class="text-xs text-amber-800">
                            <i class="bx bx-error-alt mr-1"></i>
                            <strong>Required:</strong> Your response is needed before the syllabus can be fully approved.
                        </p>
                    </div>
                    @endif
                    <p class="text-xs text-slate-500 mb-2">
                        @if ($decision === 'returned_for_revision')
                            Describe the revisions you made in response to the required actions.
                        @else
                            Describe the corrections you made in response to the committee's required actions.
                        @endif
                    </p>
                    @if (!$rf->part_h_faculty_response)
                    <textarea x-model="partHResponse" rows="4"
                        placeholder="{{ $decision === 'returned_for_revision' ? 'Describe the revisions made…' : 'Describe the corrections made…' }}"
                        class="w-full text-sm rounded-xl border border-[#e2e8f0] px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-400 resize-none"></textarea>
                    <div class="mt-2 flex justify-end gap-2">
                        @if ($decision === 'returned_for_revision')
                            <x-ui.button type="button" variant="sm-add"
                                x-on:click="savePartH()"
                                x-bind:disabled="savingPartH || !partHResponse.trim()">
                                <i x-show="!savingPartH" class="bx bx-save leading-none"></i>
                                <svg x-show="savingPartH" x-cloak class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span x-text="savingPartH ? 'Saving…' : 'Save Response'"></span>
                            </x-ui.button>
                            <x-ui.button type="button" variant="primary"
                                x-on:click="resubmitForReview()"
                                x-bind:disabled="resubmitting || !partHResponse.trim()"
                                x-bind:class="!partHResponse.trim() ? 'opacity-50 cursor-not-allowed' : ''">
                                <i x-show="!resubmitting" class="bx bx-send leading-none"></i>
                                <svg x-show="resubmitting" x-cloak class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span x-text="resubmitting ? 'Resubmitting…' : 'Resubmit for Review'"></span>
                            </x-ui.button>
                        @else
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
                        @endif
                    </div>
                    @endif
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
                @if (!$rf->approved_by_dean_id && $rf->recommended_by_chair_id)
                    <div class="mt-3 p-3 rounded-lg bg-blue-50 border border-blue-200">
                        <p class="text-xs text-blue-800">
                            <i class="bx bx-time mr-1"></i>
                            <strong>Next Step:</strong> Waiting for dean approval to complete the review process.
                        </p>
                    </div>
                @elseif ($rf->approved_by_dean_id)
                    <div class="mt-3 p-3 rounded-lg bg-emerald-50 border border-emerald-200">
                        <p class="text-xs text-emerald-800">
                            <i class="bx bx-check-circle mr-1"></i>
                            <strong>Complete:</strong> The syllabus has been fully approved and is ready for implementation.
                        </p>
                    </div>
                @endif
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
