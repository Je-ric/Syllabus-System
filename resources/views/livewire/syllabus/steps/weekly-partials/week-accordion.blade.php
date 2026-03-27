{{--
    Partial: weekly-partials/week-accordion.blade.php

    Alpine-driven accordion that wraps every week row.
    Auto-saves the week that just collapsed via $watch.

    Each body is delegated to:
        week-body-locked.blade.php   — exam / non-teaching weeks
        week-body-editable.blade.php — normal editable weeks

    Inherits from parent component view:
        $syllabusWeeks   Collection
        $weekEvents      array
        $lockedWeeks     array
        $weekInputs      array
        $courseOutcomes  array
        $activeComponent string
--}}
<div x-data="{
        openWeek: {{ $syllabusWeeks->first()?->week_no ?? 1 }},
        init() {
            this.$watch('openWeek', (newVal, oldVal) => {
                if (oldVal !== null && oldVal !== newVal) {
                    $wire.saveWeek(oldVal);
                }
            });
        }
     }"
     class="rounded-xl border border-emerald-200 bg-white shadow-sm divide-y divide-emerald-100/60">

    @foreach ($syllabusWeeks as $week)
        @php
            $wKey     = 'w' . $week->week_no;
            $start    = \Carbon\Carbon::parse($week->start_date);
            $end      = \Carbon\Carbon::parse($week->end_date);
            $events   = $weekEvents[$week->week_no] ?? [];
            $isLocked = isset($lockedWeeks[$week->week_no]);
            $lockType = $lockedWeeks[$week->week_no] ?? null;

            // Week 1 is always the MVGO week
            $isMvgo = ((int) $week->week_no === 1);

            $savedTopic = $weekInputs[$wKey]['topic'] ?? '';
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

            // Resolve the CO code badge for the header (not shown for MVGO)
            $coId   = $isMvgo ? null : ($weekInputs[$wKey]['course_outcome_id'] ?? null);
            $coCode = null;
            if ($coId) {
                foreach ($courseOutcomes as $co) {
                    if ($co['id'] == $coId) { $coCode = $co['co_code']; break; }
                }
            }

            // UI accents
            $accentClosed = $isLocked ? 'bg-rose-300' : 'bg-emerald-300';
            $accentOpen   = $isLocked ? 'bg-rose-600' : 'bg-emerald-600';

            $openHeaderClass = $isLocked
                ? 'bg-rose-50 ring-1 ring-inset ring-rose-300'
                : 'bg-emerald-50 ring-1 ring-inset ring-emerald-200';

            $bodyBgClass = $isLocked ? 'bg-rose-50/20' : 'bg-emerald-50/10';
        @endphp

        <div wire:key="week-{{ $week->week_no }}-{{ $activeComponent }}" class="relative">

            {{-- Left accent bar (helps differentiate header vs body on open) --}}
            <span class="absolute left-0 top-0 bottom-0 w-1"
                :class="openWeek === {{ $week->week_no }} ? '{{ $accentOpen }}' : '{{ $accentClosed }}'"></span>

            {{-- Accordion Header --}}
            <button type="button"
                @click="openWeek = openWeek === {{ $week->week_no }} ? null : {{ $week->week_no }}"
                :class="openWeek === {{ $week->week_no }} ? '{{ $openHeaderClass }}' : ''"
                @class([
                    'w-full flex items-center pl-6 pr-5 py-3.5 transition-colors duration-100 focus:outline-none text-left',
                    'hover:bg-rose-50 bg-rose-50/20'     => $isLocked,
                    'hover:bg-emerald-50/40'              => ! $isLocked,
                ])>

                <div class="flex items-center gap-3 min-w-0 flex-1">

                    {{-- Week number circle — rose = locked, emerald = normal --}}
                    <span @class([
                        'inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold shrink-0',
                        'bg-rose-100 text-rose-700 ring-1 ring-rose-300'       => $isLocked,
                        'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200' => ! $isLocked,
                    ])>{{ $week->week_no }}</span>

                    <div class="flex items-center gap-2 flex-wrap min-w-0">
                        <span class="font-semibold text-sm shrink-0 {{ $isLocked ? 'text-rose-700' : 'text-emerald-800' }}">
                            Week {{ $week->week_no }}
                        </span>
                        <span class="text-xs text-slate-400 shrink-0">
                            {{ $start->format('M d') }}–{{ $end->format('M d, Y') }}
                        </span>

                        @if ($isLocked)
                            @if ($lockType === 'exam')
                                <x-feedback-status.status-indicator variant="rose" icon="bx bx-clipboard" size="sm">
                                    {{ $lockLabel }}
                                </x-feedback-status.status-indicator>
                            @else
                                <x-feedback-status.status-indicator variant="rose" icon="bx bx-lock-alt" size="sm">
                                    {{ $lockLabel }}
                                </x-feedback-status.status-indicator>
                            @endif
                        @else
                            @if ($isMvgo)
                                <x-feedback-status.status-indicator variant="emerald" size="sm">MVGO</x-feedback-status.status-indicator>
                            @elseif ($coCode)
                                <x-feedback-status.status-indicator variant="emerald" size="sm">{{ $coCode }}</x-feedback-status.status-indicator>
                            @endif
                            @if ($savedTopic)
                                <span class="text-xs text-slate-400 truncate max-w-xs hidden md:block">
                                    — {{ \Illuminate\Support\Str::limit($savedTopic, 55) }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Right-side meta badges + chevron --}}
                <div class="flex items-center gap-1.5 shrink-0 ml-3">
                    @if (! $isLocked)
                        @if (count($events) > 0)
                            <x-feedback-status.status-indicator variant="amber" size="sm">
                                {{ count($events) }} event{{ count($events) !== 1 ? 's' : '' }}
                            </x-feedback-status.status-indicator>
                        @endif
                        @if ($refCount > 0)
                            <x-feedback-status.status-indicator variant="emerald" size="sm">
                                {{ $refCount }} ref{{ $refCount !== 1 ? 's' : '' }}
                            </x-feedback-status.status-indicator>
                        @endif
                        @if ($matCount > 0)
                            <x-feedback-status.status-indicator variant="blue" size="sm">
                                {{ $matCount }} mat{{ $matCount !== 1 ? 's' : '' }}
                            </x-feedback-status.status-indicator>
                        @endif
                    @endif
                    <i class="bx text-slate-400 text-lg transition-transform duration-200"
                        :class="openWeek === {{ $week->week_no }} ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                </div>

            </button>

            {{-- Accordion Body --}}
            <div x-show="openWeek === {{ $week->week_no }}" x-cloak
                class="pl-6 pr-5 pb-5 pt-4 border-t border-slate-100 {{ $bodyBgClass }}">

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
