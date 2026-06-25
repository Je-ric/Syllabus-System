{{-- weekly-partials/week-body-editable.blade.php --}}

@if ($isMvgo)
    <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-xl border border-violet-200 bg-violet-50/70"
         style="box-shadow: 0 1px 4px rgba(124,58,237,.08);">
        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-violet-100 text-violet-600 shrink-0">
            <i class="bx bx-star text-sm leading-none"></i>
        </span>
        <p class="text-sm text-violet-700">
            <strong class="font-semibold">Week 1 — MVGO.</strong>
            This week covers Mission, Vision, Goals &amp; Objectives.
        </p>
    </div>
@endif

{{-- Calendar events strip --}}
@if (count($events) > 0)
    <div class="mb-4 rounded-xl border border-slate-200 bg-[#f8fafc] px-4 py-3">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 flex items-center gap-1.5">
            <i class="bx bx-calendar-event text-sm"></i>
            Events this week
        </p>
        <ul class="space-y-1.5">
            @foreach ($events as $ev)
                <li class="flex items-center gap-2.5 text-sm">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 bg-emerald-400"></span>
                    <span class="font-medium text-slate-700">{{ $ev['name'] }}</span>
                    <span class="text-slate-300">·</span>
                    <span class="text-slate-400 text-xs">{{ $ev['date_display'] }}</span>
                    <span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full
                                 bg-emerald-50 text-emerald-700 uppercase tracking-wide shrink-0">
                        {{ str_replace('_', ' ', $ev['type']) }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $lo   = $weekInputs[$wKey]['learning_outcomes']  ?? '';
    $at   = $weekInputs[$wKey]['assessment_task']    ?? '';
    $tp   = $weekInputs[$wKey]['topic']              ?? '';
    $tla  = $weekInputs[$wKey]['teaching_activities'] ?? '';
    $coId = $isMvgo ? null : ($weekInputs[$wKey]['course_outcome_id'] ?? null);

    $coLabel = null;
    if ($coId) {
        foreach ($courseOutcomes as $co) {
            if ($co['id'] == $coId) {
                $coLabel = $co['co_code'] . ' – ' . \Illuminate\Support\Str::limit($co['description'], 72);
                break;
            }
        }
    }

    $refs = array_filter($weekInputs[$wKey]['references'] ?? [], fn ($r) => trim($r['text'] ?? '') !== '');
    $mats = array_filter(
        $weekInputs[$wKey]['materials'] ?? [],
        fn ($m) => trim($m['name'] ?? '') !== '' || trim($m['url'] ?? '') !== '',
    );

    $baseFields = $weekInputs[$wKey] ?? [
        'course_outcome_id'   => '',
        'learning_outcomes'   => '',
        'assessment_task'     => '',
        'topic'               => '',
        'teaching_activities' => '',
        'references'          => [['text' => '']],
        'materials'           => [['name' => '', 'url' => '']],
    ];

    $weekDatesStr =
        \Carbon\Carbon::parse($week->start_date)->format('M d') . '–' .
        \Carbon\Carbon::parse($week->end_date)->format('M d, Y');

    $richFields = [
        ['key' => 'learning_outcomes',   'label' => 'Unit Learning Outcomes',         'value' => $lo,  'icon' => 'bx-target-lock'],
        ['key' => 'assessment_task',     'label' => 'Assessment Task',                'value' => $at,  'icon' => 'bx-checkbox-checked'],
        ['key' => 'topic',               'label' => 'Topics',                         'value' => $tp,  'icon' => 'bx-list-ul'],
        ['key' => 'teaching_activities', 'label' => 'Teaching & Learning Activities', 'value' => $tla, 'icon' => 'bx-chalkboard'],
    ];
@endphp

{{-- CO mapping row --}}
@if (!$isMvgo)
    <div class="mb-4 flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50/60 px-4 py-3">

        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 shrink-0">
            <i class="bx bx-link-alt text-sm leading-none"></i>
        </span>

        <div class="flex-1 min-w-0">
            <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-0.5">
                Linked Course Outcome
            </p>
            @if ($coLabel)
                <p class="text-sm text-slate-700 truncate">{{ $coLabel }}</p>
            @else
                <p class="text-sm italic text-slate-400">No course outcome linked</p>
            @endif
        </div>

        <button type="button"
            x-on:click="$dispatch('open-week-modal', {
                weekNo:    {{ $week->week_no }},
                weekDates: '{{ $weekDatesStr }}',
                isMvgo:    false,
                field:     'learning_outcomes',
                fields:    {{ Js::from($baseFields) }}
            })"
            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                   text-xs font-semibold text-emerald-700 bg-white
                   border border-emerald-200 hover:bg-emerald-50
                   transition-colors duration-150">
            <i class="bx bx-edit-alt text-sm"></i>
            Change
        </button>

    </div>
@endif

{{-- Rich-text field grid --}}
<div class="rounded-xl border border-slate-200 bg-white overflow-hidden mb-4
            divide-x divide-slate-200 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4"
     style="box-shadow: 0 1px 4px rgba(0,0,0,.05);">

    @foreach ($richFields as $rf)
        <div class="flex flex-col border-b border-slate-200 xl:border-b-0 last:border-b-0">

            {{-- Card header --}}
            <div class="flex items-center justify-between gap-2 px-4 py-2.5
                        bg-[#f8fafc] border-b border-slate-200 shrink-0">

                <div class="flex items-center gap-1.5 min-w-0">
                    <i class="bx {{ $rf['icon'] }} text-slate-400 text-sm shrink-0"></i>
                    <span class="text-xs font-semibold text-slate-600 truncate">
                        {{ $rf['label'] }}
                    </span>
                </div>

                <button
                    type="button"
                    x-on:click="$dispatch('open-week-modal', {
                        weekNo:    {{ $week->week_no }},
                        weekDates: '{{ $weekDatesStr }}',
                        isMvgo:    {{ $isMvgo ? 'true' : 'false' }},
                        field:     '{{ $rf['key'] }}',
                        fields:    {{ Js::from($baseFields) }}
                    })"
                    class="shrink-0 flex items-center justify-center w-7 h-7 rounded-lg
                           text-slate-300 hover:text-emerald-600 hover:bg-emerald-50
                           transition-colors duration-150">
                    <i class="bx bx-edit-alt text-sm"></i>
                </button>

            </div>

            {{-- Card content --}}
            <div class="p-4 flex-1 min-h-[120px]">
                @if ($rf['value'] && trim(strip_tags($rf['value'])) !== '')
                    <div class="text-sm text-slate-700 leading-relaxed
                                prose prose-sm max-w-none
                                [&_ul]:list-disc [&_ul]:pl-4 [&_ul]:my-1
                                [&_ol]:list-decimal [&_ol]:pl-4 [&_ol]:my-1
                                [&_li]:my-0.5
                                [&_p]:m-0 [&_p+p]:mt-1.5
                                [&_strong]:font-semibold [&_em]:italic">
                        {!! $rf['value'] !!}
                    </div>
                @else
                    <p class="text-sm italic text-slate-300 select-none">
                        No content yet —
                        <button type="button"
                            x-on:click="$dispatch('open-week-modal', {
                                weekNo:    {{ $week->week_no }},
                                weekDates: '{{ $weekDatesStr }}',
                                isMvgo:    {{ $isMvgo ? 'true' : 'false' }},
                                field:     '{{ $rf['key'] }}',
                                fields:    {{ Js::from($baseFields) }}
                            })"
                            class="not-italic text-emerald-500 hover:text-emerald-700
                                   hover:underline transition-colors">
                            add now
                        </button>
                    </p>
                @endif
            </div>

        </div>
    @endforeach

</div>

{{-- References & Materials --}}
@if (count($refs) > 0 || count($mats) > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">

        @if (count($refs) > 0)
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3.5">
                <div class="flex items-center gap-2 mb-2.5">
                    <i class="bx bx-book-open text-emerald-500 text-base"></i>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">References</p>
                </div>
                <ul class="space-y-2">
                    @foreach ($refs as $ref)
                        <li class="flex items-start gap-2.5 text-sm text-slate-600">
                            <span class="mt-1.5 w-1 h-1 rounded-full bg-emerald-400 shrink-0"></span>
                            <span class="leading-snug">{{ $ref['text'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (count($mats) > 0)
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3.5">
                <div class="flex items-center gap-2 mb-2.5">
                    <i class="bx bx-link text-blue-500 text-base"></i>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Online Materials</p>
                </div>
                <ul class="space-y-1.5">
                    @foreach ($mats as $mat)
                        <li>
                            <a href="{{ $mat['url'] }}" target="_blank" rel="noopener"
                                class="group flex items-center justify-between gap-2
                                       rounded-lg border border-slate-100 px-3 py-2
                                       hover:border-blue-200 hover:bg-blue-50/50
                                       transition-colors duration-150">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-700 truncate">
                                        {{ $mat['name'] ?: $mat['url'] }}
                                    </p>
                                    @if ($mat['name'] && $mat['url'])
                                        <p class="text-xs text-slate-400 truncate">{{ $mat['url'] }}</p>
                                    @endif
                                </div>
                                <i class="bx bx-link-external text-sm shrink-0
                                          text-slate-300 group-hover:text-blue-500
                                          transition-colors duration-150"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
@endif

{{-- Footer actions --}}
<div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100">

    <x-button variant="sm-cancel"
        wire:click="resetWeek({{ $week->week_no }})"
        wireTarget="resetWeek({{ $week->week_no }})"
        wire:confirm="Reset Week {{ $week->week_no }}? All content will be cleared and cannot be undone."
        loading="Resetting…">
        <i class="bx bx-reset text-sm"></i>
        Reset
    </x-button>

    <x-button type="button"
        x-on:click="$dispatch('open-week-modal', {
            weekNo:    {{ $week->week_no }},
            weekDates: '{{ $weekDatesStr }}',
            isMvgo:    {{ $isMvgo ? 'true' : 'false' }},
            field:     'learning_outcomes',
            fields:    {{ Js::from($baseFields) }}
        })"
        variant="add-button">
        <i class="bx bx-edit text-sm leading-none"></i>
        Edit Week
    </x-button>

</div>
