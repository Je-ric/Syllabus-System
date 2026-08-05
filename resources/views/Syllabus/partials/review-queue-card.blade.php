{{--
    Partial: Syllabus/partials/review-queue-card.blade.php
    Variables: $assignment (SyllabusReviewer), $syllabus (Syllabus)
--}}
@php
    $isPending    = $assignment->status === 'pending';
    $isChair      = $assignment->role   === 'chair';
    $reviewForm   = $syllabus->reviewForm;

    // Syllabus-level status
    $syllabusStatus = $syllabus->status ?? 'unknown';
    $statusLabel = match ($syllabusStatus) {
        'under_review' => 'Under Review',
        'for_revision' => 'For Revision',
        'approved'     => 'Approved',
        default        => ucfirst(str_replace('_', ' ', $syllabusStatus)),
    };
    $statusVariant = match ($syllabusStatus) {
        'under_review' => 'blue',
        'for_revision' => 'rose',
        'approved'     => 'emerald',
        default        => 'slate',
    };

    // Reviewer-level status
    $reviewerLabel   = $isPending ? 'Pending' : 'Completed';
    $reviewerVariant = $isPending ? 'amber'   : 'emerald';

    // Role badge
    $roleLabel   = $isChair ? 'CQI Chair' : 'CQI Member';
    $roleVariant = $isChair ? 'violet'    : 'slate';

    // Review form state
    $formSubmitted    = $reviewForm?->submitted_at !== null;
    $classification   = $reviewForm?->classification;
    $decision         = $reviewForm?->decision;

    $decisionLabel = match ($decision) {
        'approved_as_updating'      => 'Approved as Updating',
        'approved_as_revision'      => 'Approved as Revision',
        'approved_with_corrections' => 'Approved w/ Corrections',
        'returned_for_revision'     => 'Returned for Revision',
        'reclassified_as_revision'  => 'Reclassified',
        default                     => null,
    };
    $decisionVariant = match ($decision) {
        'approved_as_updating',
        'approved_as_revision'      => 'emerald',
        'approved_with_corrections' => 'amber',
        'returned_for_revision'     => 'rose',
        'reclassified_as_revision'  => 'blue',
        default                     => null,
    };

    $barColor = $isPending ? 'bg-[#F5B126]' : 'bg-[#00965F]';
@endphp

<div class="flex flex-col rounded-xl bg-white border border-[#E3E8EB] overflow-hidden
            hover:border-[#009639] hover:shadow-[0_4px_16px_rgba(0,150,57,0.10)]
            transition-all duration-200"
     style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);">

    {{-- Top colour bar --}}
    <div class="h-0.75 w-full {{ $barColor }}"></div>

    {{-- Header --}}
    <div class="px-4 py-3 bg-[#FAFDFB] border-b border-[#E3E8EB]">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <h3 class="font-bold text-[#394056] font-mono text-[13px] leading-none mb-1">
                    {{ $syllabus->course->course_code ?? '—' }}
                </h3>
                <p class="text-[12px] text-[#72809E] leading-relaxed">
                    {{ \Illuminate\Support\Str::limit($syllabus->course->course_title ?? '', 52) }}
                </p>
            </div>
            <div class="flex flex-col items-end gap-1 shrink-0">
                <x-feedback-status.status-indicator :variant="$statusVariant">
                    {{ $statusLabel }}
                </x-feedback-status.status-indicator>
                <x-feedback-status.status-indicator :variant="$roleVariant" size="xs">
                    {{ $roleLabel }}
                </x-feedback-status.status-indicator>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="flex-1 px-4 py-3 space-y-2">

        {{-- Academic year --}}
        @if ($syllabus->academicCalendar)
            <div class="flex items-center gap-1.5 text-[12px] text-[#72809E]">
                <i class="bx bx-calendar text-[#C1C8D4] text-sm leading-none"></i>
                {{ $syllabus->academicCalendar->academic_year }}
                &nbsp;·&nbsp;
                {{ $syllabus->academicCalendar->semester }}
            </div>
        @endif

        {{-- Program --}}
        @if ($syllabus->course->program ?? null)
            <div class="flex items-start gap-1.5 text-[12px] text-[#72809E]">
                <i class="bx bx-book text-[#C1C8D4] text-sm mt-0.5 shrink-0 leading-none"></i>
                <span class="leading-relaxed truncate">{{ $syllabus->course->program->name }}</span>
            </div>
        @endif

        {{-- Preparer --}}
        @if ($syllabus->preparer ?? null)
            <div class="flex items-center gap-1.5 text-[12px] text-[#72809E]">
                <i class="bx bx-user text-[#C1C8D4] text-sm leading-none"></i>
                {{ $syllabus->preparer->name }}
            </div>
        @endif

        {{-- Review form state --}}
        <div class="pt-1 flex items-center flex-wrap gap-1.5">
            @if ($formSubmitted)
                <x-feedback-status.status-indicator variant="emerald" icon="bx bx-file">
                    F.003 Submitted
                </x-feedback-status.status-indicator>
            @else
                <x-feedback-status.status-indicator variant="slate" icon="bx bx-file">
                    F.003 Not submitted
                </x-feedback-status.status-indicator>
            @endif

            @if ($classification)
                <x-feedback-status.status-indicator
                    :variant="$classification === 'revision' ? 'blue' : 'emerald'">
                    {{ ucfirst($classification) }}
                </x-feedback-status.status-indicator>
            @endif

            @if ($decisionLabel && $decisionVariant)
                <x-feedback-status.status-indicator :variant="$decisionVariant">
                    {{ $decisionLabel }}
                </x-feedback-status.status-indicator>
            @endif
        </div>

    </div>

    {{-- Footer --}}
    <div class="px-3 pb-3 pt-1 flex gap-2">
        <x-ui.button
            href="{{ route('syllabus.reviewer.show', ['syllabus' => $syllabus->id]) }}"
            variant="primary"
            class="flex-1 justify-center">
            <i class="bx bx-clipboard-check text-base leading-none"></i>
            Review
        </x-ui.button>
        <x-ui.button
            href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}"
            variant="cancel"
            class="flex-1 justify-center">
            Preview
        </x-ui.button>
    </div>

</div>
