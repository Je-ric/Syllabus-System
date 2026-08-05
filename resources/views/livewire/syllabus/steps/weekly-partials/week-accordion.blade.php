{{-- weekly-partials/week-accordion.blade.php --}}
@include('livewire.syllabus.steps.weekly-partials.week-edit-modal')

@php
    $incompleteWeekNos = [];
    foreach ($syllabusWeeks as $_w) {
        $_wKey     = 'w' . $_w->week_no;
        $_isLocked = isset($lockedWeeks[$_w->week_no]);
        $_isMvgo   = ((int) $_w->week_no === 1);
        if (!$_isLocked && !$_isMvgo) {
            $_hasTopic  = !empty(strip_tags($weekInputs[$_wKey]['topic'] ?? ''));
            $_hasCo     = !empty($weekInputs[$_wKey]['course_outcome_id'] ?? null);
            $_hasAssess = !empty(strip_tags($weekInputs[$_wKey]['assessment_task'] ?? ''));
            if (!($_hasTopic && $_hasCo && $_hasAssess)) {
                $incompleteWeekNos[] = $_w->week_no;
            }
        }
    }
    $allWeekNos = $syllabusWeeks->pluck('week_no')->values()->all();
@endphp

<div
    x-data="{
        openWeek: null,
        allWeeks:        @js($allWeekNos),
        incompleteWeeks: @js($incompleteWeekNos),

        expandAll()   { this.openWeek = '__all__'; },
        collapseAll() { this.openWeek = null; },

        jumpToIncomplete() {
            if (this.incompleteWeeks.length === 0) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'info', message: 'All editable weeks are complete!' }
                }));
                return;
            }
            const cur    = this.openWeek;
            const after  = this.incompleteWeeks.find(n => cur === null || cur === '__all__' || n > cur);
            const target = after ?? this.incompleteWeeks[0];
            this.openWeek = target;
            this.$nextTick(() => {
                const el = document.getElementById('week-row-' + target);
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
    }"
    x-on:expand-all-weeks.window="expandAll()"
    x-on:collapse-all-weeks.window="collapseAll()"
    x-on:jump-to-incomplete-week.window="jumpToIncomplete()"
    class="space-y-2">

    @foreach ($syllabusWeeks as $week)
        @php
            $wKey     = 'w' . $week->week_no;
            $start    = \Carbon\Carbon::parse($week->start_date);
            $end      = \Carbon\Carbon::parse($week->end_date);
            $events   = $weekEvents[$week->week_no] ?? [];
            $isLocked = isset($lockedWeeks[$week->week_no]);
            $lockType = $lockedWeeks[$week->week_no] ?? null;
            $isMvgo   = ((int) $week->week_no === 1);

            $savedTopic = strip_tags($weekInputs[$wKey]['topic'] ?? '');

            $lockLabel = match ($lockType) {
                'exam'         => 'Exam Week',
                'non_teaching' => 'Non-Teaching Week',
                default        => 'Locked',
            };

            $coId   = $isMvgo ? null : ($weekInputs[$wKey]['course_outcome_id'] ?? null);
            $coCode = null;
            if ($coId) {
                foreach ($courseOutcomes as $co) {
                    if ($co['id'] == $coId) { $coCode = $co['co_code']; break; }
                }
            }

            $isComplete = $isLocked || $isMvgo || (
                !empty(strip_tags($weekInputs[$wKey]['topic'] ?? '')) &&
                !empty($weekInputs[$wKey]['course_outcome_id'] ?? null) &&
                !empty(strip_tags($weekInputs[$wKey]['assessment_task'] ?? ''))
            );
            $isPartial = !$isLocked && !$isComplete && (
                !empty(strip_tags($weekInputs[$wKey]['topic'] ?? '')) ||
                !empty($weekInputs[$wKey]['course_outcome_id'] ?? null) ||
                !empty(strip_tags($weekInputs[$wKey]['assessment_task'] ?? ''))
            );

            // Border/bg tokens
            $openBorder   = $isLocked ? 'border-[#fda4af]'  : 'border-[#86efac]';
            $closedBorder = 'border-[#e4e4e7]';
            $openHeader   = $isLocked ? 'bg-[#fff1f2]'      : 'bg-[#f0fdf4]';
            $closedHeader = $isLocked ? 'bg-[#fff1f2]/60 border-l-[3px] border-l-[#e11d48]' : 'bg-white border-l-[3px] border-l-[#16a34a]';
            $bodyBg       = $isLocked ? 'bg-[#fff1f2]/30'   : 'bg-[#f0fdf4]/20';
            $bodyBorder   = $isLocked ? 'border-[#fecdd3]'  : 'border-[#e4e4e7]';
        @endphp

        <div
            id="week-row-{{ $week->week_no }}"
            wire:key="week-{{ $week->week_no }}-{{ $activeComponent }}"
            class="bg-white rounded-[14px] border overflow-hidden transition-all duration-200"
            :class="(openWeek === {{ $week->week_no }} || openWeek === '__all__')
                ? '{{ $openBorder }} shadow-sm'
                : '{{ $closedBorder }}'">

            {{-- Accordion Header --}}
            <button type="button"
                @click="openWeek = (openWeek === {{ $week->week_no }} && openWeek !== '__all__') ? null : {{ $week->week_no }}"
                :class="(openWeek === {{ $week->week_no }} || openWeek === '__all__') ? '{{ $openHeader }}' : '{{ $closedHeader }}'"
                @class([
                    'w-full flex items-center pl-5 pr-4 py-3 transition-colors duration-100 focus:outline-none text-left',
                    'hover:bg-[#fff1f2]'  => $isLocked,
                    'hover:bg-[#f0fdf4]'  => !$isLocked,
                ])>

                <div class="flex items-center gap-3 min-w-0 flex-1">

                    {{-- Week number --}}
                    <span class="inline-flex items-center justify-center rounded-full w-7 h-7 text-[11px] font-bold shrink-0 transition-colors duration-200"
                        :class="openWeek === {{ $week->week_no }}
                            ? '{{ $isLocked ? 'bg-[#e11d48] text-white' : 'bg-[#16a34a] text-white' }}'
                            : '{{ $isLocked ? 'bg-[#ffe4e6] text-[#e11d48] border border-[#fecdd3]' : 'bg-[#f0fdf4] text-[#16a34a] border border-[#86efac]' }}'">
                        {{ $week->week_no }}
                    </span>

                    <div class="flex items-center gap-2 flex-wrap min-w-0">
                        <span class="text-[13px] font-semibold shrink-0 {{ $isLocked ? 'text-[#e11d48]' : 'text-[#09090b]' }}">
                            Week {{ $week->week_no }}
                        </span>
                        <span class="text-[#d4d4d8] mx-0.5">·</span>
                        <span class="text-[12px] text-[#71717a] shrink-0">
                            {{ $start->format('M d') }}–{{ $end->format('M d, Y') }}
                        </span>

                        @if ($isLocked)
                            <x-feedback-status.status-indicator variant="rose"
                                icon="{{ $lockType === 'exam' ? 'bx bx-clipboard' : 'bx bx-lock-alt' }}" size="sm">
                                {{ $lockLabel }}
                            </x-feedback-status.status-indicator>
                        @else
                            @if ($isMvgo)
                                <x-feedback-status.status-indicator variant="brand" size="sm">MVGO</x-feedback-status.status-indicator>
                            @elseif ($coCode)
                                <x-feedback-status.status-indicator variant="brand" size="sm">{{ $coCode }}</x-feedback-status.status-indicator>
                            @endif
                            @if ($savedTopic)
                                <span class="text-[12px] text-[#a1a1aa] truncate max-w-xs hidden md:block">
                                    — {{ \Illuminate\Support\Str::limit($savedTopic, 55) }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Right meta --}}
                <div class="flex items-center gap-1.5 shrink-0 ml-3">
                    @if (!$isLocked)
                        @if ($isComplete)
                            <span class="w-2 h-2 rounded-full bg-[#16a34a] shrink-0" title="Complete"></span>
                        @elseif ($isPartial)
                            <span class="w-2 h-2 rounded-full bg-[#d97706] shrink-0" title="Incomplete"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-[#d4d4d8] shrink-0" title="Empty"></span>
                        @endif
                    @endif
                    <i class="bx text-[#a1a1aa] text-lg transition-transform duration-200"
                        :class="(openWeek === {{ $week->week_no }} || openWeek === '__all__') ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                </div>

            </button>

            {{-- Accordion Body --}}
            <div x-show="openWeek === {{ $week->week_no }} || openWeek === '__all__'" x-cloak
                class="pl-5 pr-4 pb-5 pt-4 border-t {{ $bodyBorder }} {{ $bodyBg }}">

                @if ($isLocked)
                    @include('livewire.syllabus.steps.weekly-partials.week-body-locked', [
                        'week' => $week, 'events' => $events, 'lockType' => $lockType, 'lockLabel' => $lockLabel,
                    ])
                @else
                    @include('livewire.syllabus.steps.weekly-partials.week-body-editable', [
                        'week' => $week, 'wKey' => $wKey, 'events' => $events, 'isMvgo' => $isMvgo,
                    ])
                @endif

            </div>

        </div>
    @endforeach

</div>
