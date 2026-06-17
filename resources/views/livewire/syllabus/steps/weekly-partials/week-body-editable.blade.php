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
        class="mb-3 flex items-center justify-between gap-3 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2.5">
        <div class="min-w-0">
            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-0.5">Course Outcome</p>
            @if ($coLabel)
                <p class="text-[13px] text-[#0f172a] truncate">{{ $coLabel }}</p>
            @else
                <p class="text-[13px] text-[#94a3b8] italic">Not selected</p>
            @endif
        </div>
        <button type="button"
            x-on:click="$dispatch('open-week-modal', {
                weekNo: {{ $week->week_no }},
                weekDates: '{{ $weekDatesStr }}',
                isMvgo: false,
                field: 'learning_outcomes',
                fields: {{ Js::from($baseFields) }}
            })"
            class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[12px] font-semibold
                   text-[#16a34a] border border-[#bbf7d0] bg-white hover:bg-[#f0fdf4] transition-colors">
            <i class="bx bx-edit text-sm"></i> Edit
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

<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
    @foreach ($richFields as $rf)
        <div class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2.5 flex flex-col gap-2">
            <div class="flex items-center justify-between gap-2">
                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#475569]">{{ $rf['label'] }}</p>
                <button type="button"
                    x-on:click="$dispatch('open-week-modal', {
                        weekNo:    {{ $week->week_no }},
                        weekDates: '{{ $weekDatesStr }}',
                        isMvgo:    {{ $isMvgo ? 'true' : 'false' }},
                        field:     '{{ $rf['key'] }}',
                        fields:    {{ Js::from($baseFields) }}
                    })"
                    class="shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-md text-[11px] font-semibold
                           text-[#16a34a] border border-[#bbf7d0] bg-white hover:bg-[#f0fdf4] transition-colors">
                    <i class="bx bx-edit text-xs"></i> Edit
                </button>
            </div>
            @if ($rf['value'] && trim(strip_tags($rf['value'])) !== '')
                <div
                    class="text-[13px] text-[#0f172a] leading-relaxed prose prose-sm max-w-none
                            [&_ul]:list-disc [&_ul]:pl-4 [&_ol]:list-decimal [&_ol]:pl-4
                            [&_strong]:font-bold [&_em]:italic [&_u]:underline
                            [&_p]:m-0 [&_p+p]:mt-2">
                    {!! htmlspecialchars_decode($rf['value'], ENT_QUOTES) !!}</div>
            @else
                <p class="text-[13px] text-[#94a3b8] italic">No content yet.</p>
            @endif
        </div>
    @endforeach
</div>

{{-- References & Materials --}}
@if (count($refs) > 0 || count($mats) > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
        @if (count($refs) > 0)
            <div class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2.5">
                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#166534] mb-1.5">
                    <i class="bx bx-book-open text-[#16a34a]"></i> References
                </p>
                <ul class="space-y-1">
                    @foreach ($refs as $ref)
                        <li class="text-[13px] text-[#0f172a] flex items-start gap-1.5">
                            <span class="mt-1.5 w-1 h-1 rounded-full bg-[#16a34a] shrink-0"></span>
                            {{ $ref['text'] }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (count($mats) > 0)
            <div class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2.5">
                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#1e40af] mb-1.5">
                    <i class="bx bx-link text-[#3b82f6]"></i> Online Materials
                </p>
                <ul class="space-y-1">
                    @foreach ($mats as $mat)
                        <li class="text-[13px] text-[#0f172a]">
                            {{ $mat['name'] }}
                            @if ($mat['url'])
                                <a href="{{ $mat['url'] }}" target="_blank" rel="noopener"
                                    class="text-[#3b82f6] underline underline-offset-2 break-all text-[12px] ml-1">
                                    {{ \Illuminate\Support\Str::limit($mat['url'], 50) }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
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
