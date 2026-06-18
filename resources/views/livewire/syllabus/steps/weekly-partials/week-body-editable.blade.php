{{-- weekly-partials/week-body-editable.blade.php --}}

@if ($isMvgo)
    <x-feedback-status.alert type="info" :showTitle="false" class="mb-4">
        <strong>Week 1 — MVGO.</strong>
        This week covers the Mission, Vision, Goals &amp; Objectives.
    </x-feedback-status.alert>
@endif

{{-- Calendar events strip --}}
@if (count($events) > 0)
    <div class="mb-4 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2.5">
        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1.5">
            <i class="bx bx-calendar-event"></i> Events this week
        </p>
        <ul class="space-y-1">
            @foreach ($events as $ev)
                <li class="flex items-center gap-2">
                    <x-feedback-status.status-indicator variant="brand" :dot="true">
                        {{ str_replace('_', ' ', ucfirst($ev['type'])) }}
                    </x-feedback-status.status-indicator>
                    <span class="text-[13px] font-medium text-[#0f172a]">{{ $ev['name'] }}</span>
                    <span class="text-[13px] text-[#94a3b8]">{{ $ev['date_display'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $lo = $weekInputs[$wKey]['learning_outcomes'] ?? '';
    $at = $weekInputs[$wKey]['assessment_task'] ?? '';
    $tp = $weekInputs[$wKey]['topic'] ?? '';
    $tla = $weekInputs[$wKey]['teaching_activities'] ?? '';
    $coId = $isMvgo ? null : $weekInputs[$wKey]['course_outcome_id'] ?? null;
    $coLabel = null;
    if ($coId) {
        foreach ($courseOutcomes as $co) {
            if ($co['id'] == $coId) {
                $coLabel = $co['co_code'] . ' – ' . \Illuminate\Support\Str::limit($co['description'], 70);
                break;
            }
        }
    }
    $refs = array_filter($weekInputs[$wKey]['references'] ?? [], fn($r) => trim($r['text'] ?? '') !== '');
    $mats = array_filter(
        $weekInputs[$wKey]['materials'] ?? [],
        fn($m) => trim($m['name'] ?? '') !== '' || trim($m['url'] ?? '') !== '',
    );

    // Base dispatch data for this week (shared by all edit buttons)
    $baseFields = $weekInputs[$wKey] ?? [
        'course_outcome_id' => '',
        'learning_outcomes' => '',
        'assessment_task' => '',
        'topic' => '',
        'teaching_activities' => '',
        'references' => [['text' => '']],
        'materials' => [['name' => '', 'url' => '']],
    ];
    $weekDatesStr =
        \Carbon\Carbon::parse($week->start_date)->format('M d') .
        '–' .
        \Carbon\Carbon::parse($week->end_date)->format('M d, Y');
@endphp

{{-- CO row (non-MVGO) --}}
@if (!$isMvgo)
    <div
        class="mb-3 flex items-center justify-between gap-3 rounded-xl
               border border-emerald-200 bg-emerald-50/60 px-3 py-3">

        <div class="flex items-start gap-3 min-w-0">

            <div
                class="flex items-center justify-center
                       w-8 h-8 rounded-lg
                       bg-white border border-emerald-200
                       text-emerald-600 shrink-0">
                <i class="bx bx-link-alt text-sm"></i>
            </div>

            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-emerald-700">
                    Linked Course Outcome
                </p>

                @if ($coLabel)
                    <p class="mt-1 text-[13px] text-slate-800 truncate">
                        {{ $coLabel }}
                    </p>
                @else
                    <p class="mt-1 text-[13px] italic text-slate-400">
                        No course outcome selected
                    </p>
                @endif
            </div>

        </div>

        <button type="button"
            x-on:click="$dispatch('open-week-modal', {
                weekNo: {{ $week->week_no }},
                weekDates: '{{ $weekDatesStr }}',
                isMvgo: false,
                field: 'learning_outcomes',
                fields: {{ Js::from($baseFields) }}
            })"
            class="shrink-0 inline-flex items-center gap-1.5
                   px-3 py-1.5 rounded-lg
                   text-[12px] font-semibold
                   text-emerald-700 bg-white
                   border border-emerald-200
                   hover:bg-emerald-50 transition">
            <i class="bx bx-edit-alt"></i>
            Edit
        </button>

    </div>
@endif

{{-- Rich-text field cards --}}
@php
    $richFields = [
        ['key' => 'learning_outcomes', 'label' => 'Unit Learning Outcomes', 'value' => $lo],
        ['key' => 'assessment_task', 'label' => 'Assessment Task', 'value' => $at],
        ['key' => 'topic', 'label' => 'Topics', 'value' => $tp],
        ['key' => 'teaching_activities', 'label' => 'Teaching & Learning Activities', 'value' => $tla],
    ];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    @foreach ($richFields as $rf)
        <div
            class="group rounded-2xl border border-slate-200 bg-white
                   p-4 transition-all duration-200
                   hover:border-emerald-200 hover:shadow-sm">

            <div class="flex items-start justify-between gap-3 mb-3">

                <div>
                    <p
                        class="text-[10px] font-bold uppercase tracking-[0.14em]
                              text-slate-500">
                        {{ $rf['label'] }}
                    </p>
                </div>

                <button type="button"
                    x-on:click="$dispatch('open-week-modal', {
                        weekNo: {{ $week->week_no }},
                        weekDates: '{{ $weekDatesStr }}',
                        isMvgo: {{ $isMvgo ? 'true' : 'false' }},
                        field: '{{ $rf['key'] }}',
                        fields: {{ Js::from($baseFields) }}
                    })"
                    class="opacity-0 group-hover:opacity-100
                           transition-opacity
                           flex items-center justify-center
                           w-8 h-8 rounded-lg
                           border border-slate-200
                           hover:border-emerald-200
                           hover:bg-emerald-50">
                    <i class="bx bx-edit-alt text-sm text-slate-600"></i>
                </button>

            </div>

            @if ($rf['value'] && trim(strip_tags($rf['value'])) !== '')
                <div
                    class="text-[13px] text-slate-700 leading-relaxed
                           prose prose-sm max-w-none
                           [&_ul]:list-disc [&_ul]:pl-4
                           [&_ol]:list-decimal [&_ol]:pl-4
                           [&_p]:m-0 [&_p+p]:mt-2">
                    {!! htmlspecialchars_decode($rf['value'], ENT_QUOTES) !!}
                </div>
            @else
                <div
                    class="flex items-center justify-center
                           min-h-20
                           rounded-xl border border-dashed
                           border-slate-200">
                    <span class="text-[12px] italic text-slate-400">
                        No content yet
                    </span>
                </div>
            @endif

        </div>
    @endforeach
</div>

{{-- References & Materials --}}
@if (count($refs) > 0 || count($mats) > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

        {{-- References --}}
        @if (count($refs) > 0)
            <div class="rounded-2xl border border-slate-200 bg-white p-4">

                <div class="flex items-center gap-2 mb-3">
                    <div
                        class="flex items-center justify-center
                                w-8 h-8 rounded-lg
                                bg-emerald-50 text-emerald-600">
                        <i class="bx bx-book-open"></i>
                    </div>

                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500">
                            References
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    @foreach ($refs as $ref)
                        <div class="flex items-start gap-2 text-[13px] text-slate-700">

                            <span class="mt-2 w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0">
                            </span>

                            <span>{{ $ref['text'] }}</span>

                        </div>
                    @endforeach
                </div>

            </div>
        @endif

        {{-- Online Materials --}}
        @if (count($mats) > 0)
            <div class="rounded-2xl border border-slate-200 bg-white p-4">

                <div class="flex items-center gap-2 mb-3">
                    <div
                        class="flex items-center justify-center
                                w-8 h-8 rounded-lg
                                bg-blue-50 text-blue-600">
                        <i class="bx bx-link"></i>
                    </div>

                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500">
                        Online Materials
                    </p>
                </div>

                <div class="space-y-2">

                    @foreach ($mats as $mat)
                        <a href="{{ $mat['url'] }}" target="_blank" rel="noopener"
                            class="group flex items-center justify-between
                                   rounded-xl border border-slate-200
                                   px-3 py-2 hover:border-blue-200
                                   hover:bg-blue-50/50 transition">

                            <div class="min-w-0">
                                <p class="text-[13px] font-medium text-slate-800 truncate">
                                    {{ $mat['name'] }}
                                </p>

                                @if ($mat['url'])
                                    <p class="text-[11px] text-slate-500 truncate">
                                        {{ $mat['url'] }}
                                    </p>
                                @endif
                            </div>

                            <i
                                class="bx bx-link-external text-slate-400
                                       group-hover:text-blue-600">
                            </i>

                        </a>
                    @endforeach

                </div>

            </div>
        @endif

    </div>
@endif

{{-- Footer: Edit All + Reset --}}
<div class="flex items-center justify-between gap-2 pt-3 border-t border-[#e2e8f0]">
    <button type="button"
        x-on:click="$dispatch('open-week-modal', {
            weekNo:    {{ $week->week_no }},
            weekDates: '{{ $weekDatesStr }}',
            isMvgo:    {{ $isMvgo ? 'true' : 'false' }},
            field:     'learning_outcomes',
            fields:    {{ Js::from($baseFields) }}
        })"
        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-[13px] font-semibold
               bg-[#16a34a] text-white hover:bg-[#15803d] transition-colors">
        <i class="bx bx-edit text-sm leading-none"></i> Edit Week
    </button>
    <x-button variant="sm-cancel" wire:click="resetWeek({{ $week->week_no }})"
        wireTarget="resetWeek({{ $week->week_no }})"
        wire:confirm="Reset Week {{ $week->week_no }}? This will clear all content. Cannot be undone."
        loading="Resetting…">
        <i class="bx bx-reset"></i> Reset
    </x-button>
</div>
