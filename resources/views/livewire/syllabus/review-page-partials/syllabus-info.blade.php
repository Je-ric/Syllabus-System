{{-- Partial: review-page-partials/syllabus-info.blade.php --}}
<x-layout.card-section title="Syllabus Info" icon="bx-info-circle">

    <div class="space-y-3 text-[13px]">
        <div class="flex items-start gap-2.5">
            <i class="bx bx-user text-[#C1C8D4] text-base mt-0.5 shrink-0"></i>
            <div>
                <p class="text-[11px] text-[#93A1AF] font-medium">Prepared by</p>
                <p class="text-[#394056] font-semibold">{{ $syllabus->preparer->name ?? '—' }}</p>
            </div>
        </div>
        <div class="flex items-start gap-2.5">
            <i class="bx bx-book text-[#C1C8D4] text-base mt-0.5 shrink-0"></i>
            <div>
                <p class="text-[11px] text-[#93A1AF] font-medium">Program</p>
                <p class="text-[#394056]">{{ $syllabus->course->program->name ?? '—' }}</p>
            </div>
        </div>
        <div class="flex items-start gap-2.5">
            <i class="bx bx-calendar text-[#C1C8D4] text-base mt-0.5 shrink-0"></i>
            <div>
                <p class="text-[11px] text-[#93A1AF] font-medium">Academic Year</p>
                <p class="text-[#394056]">
                    {{ $syllabus->academicCalendar?->academic_year ?? '—' }}
                    {{ $syllabus->academicCalendar?->semester ?? '' }}
                </p>
            </div>
        </div>
        @if ($reviewForm?->classification)
            <div class="flex items-start gap-2.5">
                <i class="bx bx-tag text-[#C1C8D4] text-base mt-0.5 shrink-0"></i>
                <div>
                    <p class="text-[11px] text-[#93A1AF] font-medium">Classification</p>
                    <x-feedback-status.status-indicator
                        :variant="$reviewForm->classification === 'revision' ? 'blue' : 'emerald'">
                        {{ ucfirst($reviewForm->classification) }}
                    </x-feedback-status.status-indicator>
                </div>
            </div>
        @endif
    </div>

    <div class="mt-4 pt-4 border-t border-[#F1F3F5]">
        <a href="{{ route('syllabus.review-form.preview', $syllabus) }}"
           target="_blank"
           class="inline-flex items-center gap-1.5 text-xs font-semibold
                  text-[#00965F] hover:text-[#006B44] transition-colors">
            <i class="bx bx-link-external text-sm"></i>
            View F.003 Review Form
        </a>
    </div>

</x-layout.card-section>
