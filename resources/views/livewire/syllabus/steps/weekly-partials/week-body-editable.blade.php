{{-- weekly-partials/week-body-editable.blade.php --}}

@if ($isMvgo)
    <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-[12px] border border-[#e9d5ff] bg-[#faf5ff]">
        <span class="flex items-center justify-center w-7 h-7 rounded-[8px] bg-[#ede9fe] text-[#7c3aed] shrink-0">
            <i class="bx bx-star text-sm leading-none"></i>
        </span>
        <p class="text-[13px] text-[#581c87]">
            <strong class="font-semibold">Week 1 — MVGO.</strong>
            This week covers Mission, Vision, Goals &amp; Objectives.
        </p>
    </div>
@endif

{{-- Calendar events strip --}}
@if (count($events) > 0)
    <div class="mb-4 rounded-[12px] border border-[#e4e4e7] bg-[#fafafa] px-4 py-3">
        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#a1a1aa] mb-2 flex items-center gap-1.5">
            <i class="bx bx-calendar-event text-sm"></i> Events this week
        </p>
        <ul class="space-y-1.5">
            @foreach ($events as $ev)
                <li class="flex items-center gap-2.5 text-[13px]">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 bg-[#16a34a]"></span>
                    <span class="font-medium text-[#18181b]">{{ $ev['name'] }}</span>
                    <span class="text-[#d4d4d8]">·</span>
                    <span class="text-[#a1a1aa] text-[11px]">{{ $ev['date_display'] }}</span>
                    <span class="ml-auto text-[11px] font-semibold px-2 py-0.5 rounded-full
                                 bg-[#f0fdf4] text-[#166534] border border-[#86efac] uppercase tracking-wide shrink-0">
                        {{ str_replace('_', ' ', $ev['type']) }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $lo   = $weekInputs[$wKey]['learning_outcomes']   ?? '';
    $at   = $weekInputs[$wKey]['assessment_task']     ?? '';
    $tp   = $weekInputs[$wKey]['topic']               ?? '';
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

    $refs = array_filter($weekInputs[$wKey]['references'] ?? [], fn($r) => trim($r['text'] ?? '') !== '');
    $mats = array_filter($weekInputs[$wKey]['materials'] ?? [], fn($m) => trim($m['name'] ?? '') !== '' || trim($m['url'] ?? '') !== '');

    $baseFields = $weekInputs[$wKey] ?? [
        'course_outcome_id'   => '',
        'learning_outcomes'   => '',
        'assessment_task'     => '',
        'topic'               => '',
        'teaching_activities' => '',
        'references'          => [['text' => '']],
        'materials'           => [['name' => '', 'url' => '']],
    ];

    $weekDatesStr = \Carbon\Carbon::parse($week->start_date)->format('M d') . '–' .
                   \Carbon\Carbon::parse($week->end_date)->format('M d, Y');

    $richFields = [
        ['key' => 'learning_outcomes',   'label' => 'Unit Learning Outcomes',         'value' => $lo,  'icon' => 'bx-target-lock'],
        ['key' => 'assessment_task',     'label' => 'Assessment Task',                'value' => $at,  'icon' => 'bx-checkbox-checked'],
        ['key' => 'topic',               'label' => 'Topics',                         'value' => $tp,  'icon' => 'bx-list-ul'],
        ['key' => 'teaching_activities', 'label' => 'Teaching & Learning Activities', 'value' => $tla, 'icon' => 'bx-chalkboard'],
    ];
@endphp

<div x-data="{
        weekNo:    {{ $week->week_no }},
        weekDates: @js($weekDatesStr),
        isMvgo:    {{ $isMvgo ? 'true' : 'false' }},
        fields:    @js($baseFields),
        openModal(field) {
            $dispatch('open-week-modal', {
                weekNo:    this.weekNo,
                weekDates: this.weekDates,
                isMvgo:    this.isMvgo,
                field:     field,
                fields:    JSON.parse(JSON.stringify(this.fields))
            });
        }
    }">

    {{-- CO mapping row --}}
    @if (!$isMvgo)
        <div class="mb-4 flex items-center gap-3 rounded-[12px] border border-[#d1fae5] bg-[#f0fdf4] px-4 py-3">
            <span class="flex items-center justify-center w-7 h-7 rounded-[8px] bg-[#dcfce7] text-[#16a34a] shrink-0">
                <i class="bx bx-link-alt text-sm leading-none"></i>
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#16a34a] mb-0.5">Linked Course Outcome</p>
                @if ($coLabel)
                    <p class="text-[13px] text-[#18181b] truncate">{{ $coLabel }}</p>
                @else
                    <p class="text-[13px] italic text-[#a1a1aa]">No course outcome linked</p>
                @endif
            </div>
            <button type="button" x-on:click="openModal('learning_outcomes')"
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[8px]
                       text-[12px] font-semibold text-[#166534] bg-white
                       border border-[#86efac] hover:bg-[#f0fdf4]
                       transition-colors duration-150">
                <i class="bx bx-edit-alt text-sm"></i> Change
            </button>
        </div>
    @endif

    {{-- Rich-text field grid --}}
    <div class="rounded-[12px] border border-[#e4e4e7] bg-white overflow-hidden mb-4
                divide-x divide-[#e4e4e7] grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4"
         style="box-shadow: 0 1px 4px rgba(0,0,0,0.04);">

        @foreach ($richFields as $rf)
            <div class="flex flex-col border-b border-[#e4e4e7] xl:border-b-0 last:border-b-0">

                <div class="flex items-center justify-between gap-2 px-4 py-2.5 bg-[#fafafa] border-b border-[#e4e4e7] shrink-0">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <i class="bx {{ $rf['icon'] }} text-[#a1a1aa] text-sm shrink-0"></i>
                        <span class="text-[11px] font-semibold text-[#52525b] truncate">{{ $rf['label'] }}</span>
                    </div>
                    <button type="button" x-on:click="openModal('{{ $rf['key'] }}')"
                        class="shrink-0 flex items-center justify-center w-7 h-7 rounded-[8px]
                               text-[#d4d4d8] hover:text-[#16a34a] hover:bg-[#f0fdf4]
                               transition-colors duration-150">
                        <i class="bx bx-edit-alt text-sm"></i>
                    </button>
                </div>

                <div class="p-4 flex-1 min-h-[120px]">
                    @if ($rf['value'] && trim(strip_tags($rf['value'])) !== '')
                        <div class="text-[13px] text-[#18181b] leading-relaxed
                                    prose prose-sm max-w-none
                                    [&_ul]:list-disc [&_ul]:pl-4 [&_ul]:my-1
                                    [&_ol]:list-decimal [&_ol]:pl-4 [&_ol]:my-1
                                    [&_li]:my-0.5 [&_p]:m-0 [&_p+p]:mt-1.5
                                    [&_strong]:font-semibold [&_em]:italic">
                            {!! $rf['value'] !!}
                        </div>
                    @else
                        <p class="text-[13px] italic text-[#d4d4d8] select-none">
                            No content yet —
                            <button type="button" x-on:click="openModal('{{ $rf['key'] }}')"
                                class="not-italic text-[#16a34a] hover:text-[#15803d] hover:underline transition-colors">
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
                <div class="rounded-[12px] border border-[#e4e4e7] bg-white px-4 py-3.5">
                    <div class="flex items-center gap-2 mb-2.5">
                        <i class="bx bx-book-open text-[#16a34a] text-base"></i>
                        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#a1a1aa]">References</p>
                    </div>
                    <ul class="space-y-2">
                        @foreach ($refs as $ref)
                            <li class="flex items-start gap-2.5 text-[13px] text-[#3f3f46]">
                                <span class="mt-1.5 w-1 h-1 rounded-full bg-[#16a34a] shrink-0"></span>
                                <span class="leading-snug">{{ $ref['text'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (count($mats) > 0)
                <div class="rounded-[12px] border border-[#e4e4e7] bg-white px-4 py-3.5">
                    <div class="flex items-center gap-2 mb-2.5">
                        <i class="bx bx-link text-[#2563eb] text-base"></i>
                        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#a1a1aa]">Online Materials</p>
                    </div>
                    <ul class="space-y-1.5">
                        @foreach ($mats as $mat)
                            <li>
                                <a href="{{ preg_match('#^https?://#i', $mat['url']) ? $mat['url'] : '#' }}" target="_blank" rel="noopener"
                                    class="group flex items-center justify-between gap-2
                                           rounded-[8px] border border-[#e4e4e7] px-3 py-2
                                           hover:border-[#bfdbfe] hover:bg-[#eff6ff]
                                           transition-colors duration-150">
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-medium text-[#18181b] truncate">
                                            {{ $mat['name'] ?: $mat['url'] }}
                                        </p>
                                        @if ($mat['name'] && $mat['url'])
                                            <p class="text-[11px] text-[#a1a1aa] truncate">{{ $mat['url'] }}</p>
                                        @endif
                                    </div>
                                    <i class="bx bx-link-external text-sm shrink-0
                                              text-[#d4d4d8] group-hover:text-[#2563eb]
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
    <div class="flex items-center justify-between gap-2 pt-3 border-t border-[#f4f4f5]">
        <x-button variant="sm-cancel"
            wire:click="resetWeek({{ $week->week_no }})"
            wireTarget="resetWeek({{ $week->week_no }})"
            wire:confirm="Reset Week {{ $week->week_no }}? All content will be cleared and cannot be undone."
            loading="Resetting…">
            <i class="bx bx-reset text-sm"></i> Reset
        </x-button>

        <button type="button" x-on:click="openModal('learning_outcomes')"
            class="inline-flex items-center gap-2 px-4 py-2 text-[13px] font-semibold rounded-[10px]
                   bg-[#16a34a] text-white border border-[#15803d]
                   hover:bg-[#15803d] transition-colors duration-150
                   focus:ring-2 focus:outline-none focus:ring-[#16a34a]/30">
            <i class="bx bx-edit text-sm leading-none"></i> Edit Week
        </button>
    </div>

</div>
