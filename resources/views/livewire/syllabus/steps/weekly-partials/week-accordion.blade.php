{{--
    Partial: weekly-partials/week-accordion.blade.php
--}}
{{-- Week edit modal (single instance, outside the loop) --}}
{{-- Confirm dialog is included inside the modal file at z-[60] --}}
@include('livewire.syllabus.steps.weekly-partials.week-edit-modal')

@php
    // Precompute incomplete (non-locked, non-MVGO, missing content) week numbers for jump-to-incomplete
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
        allWeeks:       @js($allWeekNos),
        incompleteWeeks: @js($incompleteWeekNos),

        expandAll() {
            // Expand every week — open the first one now, done
            // (All weeks render their body via x-show, so we use a sentinel value
            //  of -1 to mean 'all open' — but Alpine's x-show checks openWeek === weekNo.
            //  Instead we set openWeek to null and dispatch one-by-one via a flag.)
            // Simplest correct approach: use a separate 'allOpen' flag.
            this.openWeek = '__all__';
        },
        collapseAll() { this.openWeek = null; },

        jumpToIncomplete() {
            if (this.incompleteWeeks.length === 0) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'info', message: 'All editable weeks are complete!' }
                }));
                return;
            }
            // Find the next incomplete week after the currently open one
            const cur = this.openWeek;
            const after = this.incompleteWeeks.find(n => cur === null || cur === '__all__' || n > cur);
            const target = after ?? this.incompleteWeeks[0]; // wrap around
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
            $refCount   = count(array_filter(
                $weekInputs[$wKey]['references'] ?? [],
                fn ($r) => trim($r['text'] ?? '') !== ''
            ));
            $matCount   = count(array_filter(
                $weekInputs[$wKey]['materials'] ?? [],
                fn ($m) => trim($m['name'] ?? '') !== '' || trim($m['url'] ?? '') !== ''
            ));

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

            // Completion: a normal week is "complete" when it has a topic, a CO/MVGO, and an assessment task
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

            // Accent bar colors — rose = locked, brand green = normal
            $accentClosed = $isLocked ? 'bg-rose-300' : 'bg-[#16a34a]';
            $accentOpen   = $isLocked ? 'bg-rose-500' : 'bg-[#15803d]';

            $openHeaderClass  = $isLocked ? 'bg-rose-50 ring-1 ring-inset ring-rose-200' : 'bg-[#f0fdf4] ring-1 ring-inset ring-[#bbf7d0]';
            $closedRowClass   = $isLocked ? 'bg-rose-50/20 border-l-4 border-rose-500' : 'bg-white border-l-4 border-emerald-500';
            $bodyBgClass      = $isLocked ? 'bg-rose-50/20' : 'bg-[#f0fdf4]/20';
            $bodyBorderClass  = $isLocked ? 'border-rose-100' : 'border-[#e2e8f0]';
        @endphp

        <div
            id="week-row-{{ $week->week_no }}"
            wire:key="week-{{ $week->week_no }}-{{ $activeComponent }}"
            class="bg-white border rounded-xl overflow-hidden shadow-sm transition-all duration-200"

            :class="(openWeek === {{ $week->week_no }} || openWeek === '__all__')
                ? '{{ $isLocked
                    ? 'border-rose-500 shadow-lg'
                    : 'border-emerald-600 shadow-lg' }}'
                : 'border-[#e2e8f0]'">

            {{-- Left accent bar --}}
            {{-- <span class="absolute left-0 top-0 bottom-0 w-1"
                :class="openWeek === {{ $week->week_no }} ? '{{ $accentOpen }}' : '{{ $accentClosed }}'"></span> --}}

            {{-- Accordion Header --}}
            <button type="button"
                @click="openWeek = (openWeek === {{ $week->week_no }} && openWeek !== '__all__') ? null : {{ $week->week_no }}"
                :class="(openWeek === {{ $week->week_no }} || openWeek === '__all__') ? '{{ $openHeaderClass }}' : '{{ $closedRowClass }}'"
                @class([
                    'w-full flex items-center pl-6 pr-5 py-3.5 transition-colors duration-100 focus:outline-none text-left',
                    'hover:bg-rose-50'    => $isLocked,
                    'hover:bg-[#f0fdf4]' => ! $isLocked,
                ])>

                <div class="flex items-center gap-3 min-w-0 flex-1">

                    {{-- Week number circle --}}
                    <span
                        class="inline-flex items-center justify-center rounded-full shadow-md w-8 h-8 text-xs font-bold shrink-0 transition-colors duration-200"

                        :class="openWeek === {{ $week->week_no }}
                            ? '{{ $isLocked
                                ? 'bg-rose-500 text-white'
                                : 'bg-emerald-600 text-white' }}'
                            : '{{ $isLocked
                                ? 'bg-rose-100 text-rose-700 ring-1 ring-rose-300'
                                : 'text-[#15803d] ring-1 ring-[#15803d]' }}'">
                        {{ $week->week_no }}
                    </span>

                    <div class="flex items-center gap-2 flex-wrap min-w-0">
                        <span class="text-sm font-semibold shrink-0 {{ $isLocked ? 'text-rose-700' : 'text-[#0f172a]' }}">
                            Week {{ $week->week_no }}
                        </span>
                        <span class="text-slate-300 mx-1">·</span>
                        <span class="text-sm text-slate-500 shrink-0">
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
                                <span class="text-sm text-[#94a3b8] truncate max-w-xs hidden md:block">
                                    — {{ \Illuminate\Support\Str::limit($savedTopic, 55) }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Right-side meta badges + completion dot + chevron --}}
                <div class="flex items-center gap-1.5 shrink-0 ml-3">
                    @if (! $isLocked)
                        {{-- Completion dot --}}
                        @if ($isComplete)
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Complete"></span>
                        @elseif ($isPartial)
                            <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0" title="Incomplete"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-slate-300 shrink-0" title="Empty"></span>
                        @endif
                        @if (count($events) > 0)
                            <x-feedback-status.status-indicator variant="brand" size="sm">
                                {{ count($events) }} event{{ count($events) !== 1 ? 's' : '' }}
                            </x-feedback-status.status-indicator>
                        @endif
                        @if ($refCount > 0)
                            <x-feedback-status.status-indicator variant="brand" size="sm">
                                {{ $refCount }} {{ $refCount !== 1 ? 'references' : 'reference' }}
                            </x-feedback-status.status-indicator>
                        @endif
                        @if ($matCount > 0)
                            <x-feedback-status.status-indicator variant="brand" size="sm">
                                {{ $matCount }} {{ $matCount !== 1 ? 'materials' : 'material' }}
                            </x-feedback-status.status-indicator>
                        @endif
                    @endif
                    <i class="bx text-[#94a3b8] text-lg transition-transform duration-200"
                        :class="(openWeek === {{ $week->week_no }} || openWeek === '__all__') ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                </div>

            </button>

            {{-- Accordion Body --}}
            <div x-show="openWeek === {{ $week->week_no }} || openWeek === '__all__'" x-cloak
                class="pl-6 pr-5 pb-5 pt-4 border-t {{ $bodyBorderClass }} {{ $bodyBgClass }}">

                @if ($isLocked)
                    @include('livewire.syllabus.steps.weekly-partials.week-body-locked', [
                        'week'      => $week,
                        'events'    => $events,
                        'lockType'  => $lockType,
                        'lockLabel' => $lockLabel,
                    ])
                @else
                    @include('livewire.syllabus.steps.weekly-partials.week-body-editable', [
                        'week'   => $week,
                        'wKey'   => $wKey,
                        'events' => $events,
                        'isMvgo' => $isMvgo,
                    ])
                @endif

            </div>

        </div>{{-- /wire:key --}}
    @endforeach

</div>{{-- /accordion --}}
