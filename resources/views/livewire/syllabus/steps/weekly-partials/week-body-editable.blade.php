{{--
    Partial: weekly-partials/week-body-editable.blade.php
    ──────────────────────────────────────────────────────
    Full editable body for a normal (unlocked) week.
    Renders: MVGO notice (week 1 only), calendar events strip,
    CO select / MVGO badge, four content textareas,
    references panel, materials panel, reset + save footer.

    Passed by week-accordion.blade.php:
        $week   SyllabusWeek
        $wKey   string   e.g. 'w3'
        $events array    Calendar events for this week (may be empty)
        $isMvgo bool     true when week_no === 1

    Inherits from parent component view (via Blade scope):
        $weekInputs      array
        $courseOutcomes  array
        $activeComponent string
--}}

{{-- ── MVGO notice (Week 1 only) ───────────────────────────────────────────── --}}
@if ($isMvgo)
    <x-feedback-status.alert type="info" :showTitle="false" class="mb-4">
        <strong>Week 1 — MVGO.</strong>
        This week covers the Mission, Vision, Goals &amp; Objectives.
        All fields are editable. Assessment task is optional — if entered,
        it will appear in Course Evaluation.
    </x-feedback-status.alert>
@endif

{{-- ── Calendar events strip ────────────────────────────────────────────────── --}}
@if (count($events) > 0)
    <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
        <div class="text-xs font-semibold text-slate-600 mb-1.5">
            <i class="bx bx-calendar-event"></i> Events this week
        </div>
        <ul class="space-y-1">
            @foreach ($events as $ev)
                @php
                    $evDot = match ($ev['type']) {
                        'holiday' => 'bg-emerald-400',
                        'break'   => 'bg-blue-400',
                        default   => 'bg-amber-400',
                    };
                @endphp
                <li class="flex items-center gap-1.5 text-xs text-slate-600">
                    <span class="w-1.5 h-1.5 rounded-full {{ $evDot }} shrink-0"></span>
                    <span class="font-medium">{{ $ev['name'] }}</span>
                    <span class="text-slate-400">({{ $ev['date_display'] }})</span>
                    <x-feedback-status.status-indicator variant="slate" size="sm">
                        {{ str_replace('_', ' ', $ev['type']) }}
                    </x-feedback-status.status-indicator>
                </li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ── Content fields ───────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 rounded-lg border border-slate-200 bg-slate-50 p-3">

    {{-- CO field: MVGO badge for Week 1, select for all others --}}
    <div class="md:col-span-2">
        @if ($isMvgo)
            <x-form.label>Outcome</x-form.label>
            <div class="flex items-center gap-2 mt-1 px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50">
                <x-feedback-status.status-indicator variant="slate" size="sm">MVGO</x-feedback-status.status-indicator>
                <span class="text-xs text-slate-500">Mission-Vision-Goals-Objectives</span>
            </div>
        @else
            <x-form.label for="co_{{ $wKey }}">Course Outcome</x-form.label>
            <x-form.select id="co_{{ $wKey }}"
                wire:model.defer="weekInputs.{{ $wKey }}.course_outcome_id">
                <option value="">— Select Course Outcome —</option>
                @foreach ($courseOutcomes as $outcome)
                    <option value="{{ $outcome['id'] }}">
                        {{ $outcome['co_code'] }} – {{ \Illuminate\Support\Str::limit($outcome['description'], 70) }}
                    </option>
                @endforeach
            </x-form.select>
        @endif
    </div>

    {{-- Unit Learning Outcomes --}}
    <div>
        <x-form.label for="lo_{{ $wKey }}">Unit Learning Outcomes</x-form.label>
        <x-form.textarea id="lo_{{ $wKey }}" rows="4"
            placeholder="Enter learning outcomes…"
            wire:model.defer="weekInputs.{{ $wKey }}.learning_outcomes" />
    </div>

    {{-- Assessment Task --}}
    <div>
        <x-form.label for="at_{{ $wKey }}">
            Assessment Task
            @if ($isMvgo)
                <span class="text-slate-400 font-normal text-xs ml-1">(optional)</span>
            @endif
        </x-form.label>
        <x-form.textarea id="at_{{ $wKey }}" rows="4"
            placeholder="{{ $isMvgo ? 'Optional — e.g. Orientation Quiz' : 'Enter assessment task…' }}"
            wire:model.defer="weekInputs.{{ $wKey }}.assessment_task" />
    </div>

    {{-- Topics --}}
    <div>
        <x-form.label for="tp_{{ $wKey }}">Topics</x-form.label>
        <x-form.textarea id="tp_{{ $wKey }}" rows="4"
            placeholder="Enter topics covered…"
            wire:model.defer="weekInputs.{{ $wKey }}.topic" />
    </div>

    {{-- Teaching & Learning Activities --}}
    <div>
        <x-form.label for="tla_{{ $wKey }}">Teaching &amp; Learning Activities</x-form.label>
        <x-form.textarea id="tla_{{ $wKey }}" rows="4"
            placeholder="Enter teaching activities…"
            wire:model.defer="weekInputs.{{ $wKey }}.teaching_activities" />
    </div>

</div>

{{-- ── References & Materials ───────────────────────────────────────────────── --}}
<div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- References --}}
    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
        <div class="flex items-center justify-between mb-2.5">
            <x-form.label>
                <i class="bx bx-book-open text-slate-500"></i> References
            </x-form.label>
            <x-button variant="sm-primary"
                wire:click="addReference({{ $week->week_no }})">
                <i class="bx bx-plus text-sm"></i> Add
            </x-button>
        </div>
        <div class="space-y-2">
            @foreach ($weekInputs[$wKey]['references'] ?? [['text' => '']] as $rIdx => $ref)
                <div class="flex items-center gap-2" wire:key="ref-{{ $wKey }}-{{ $rIdx }}">
                    <input type="text"
                        wire:model.defer="weekInputs.{{ $wKey }}.references.{{ $rIdx }}.text"
                        placeholder="e.g. Author (Year). Title. Publisher."
                        class="flex-1 text-xs rounded-lg border border-slate-300 bg-white px-3 py-1.5
                               focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none
                               placeholder:text-slate-300" />
                    @if (count($weekInputs[$wKey]['references'] ?? []) > 1)
                        <button type="button"
                            wire:click="removeReference({{ $week->week_no }}, {{ $rIdx }})"
                            class="shrink-0 p-1.5 text-slate-400 hover:text-rose-500
                                   hover:bg-rose-50 rounded-md transition-colors"
                            title="Remove">
                            <i class="bx bx-trash text-sm"></i>
                        </button>
                    @else
                        <span class="w-7 shrink-0"></span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Online Materials --}}
    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
        <div class="flex items-center justify-between mb-2.5">
            <x-form.label>
                <i class="bx bx-link text-slate-500"></i> Online Materials
            </x-form.label>
            <x-button variant="sm-primary"
                wire:click="addMaterial({{ $week->week_no }})">
                <i class="bx bx-plus text-sm"></i> Add
            </x-button>
        </div>
        <div class="space-y-3">
            @foreach ($weekInputs[$wKey]['materials'] ?? [['name' => '', 'url' => '']] as $mIdx => $mat)
                <div class="flex items-start gap-2" wire:key="mat-{{ $wKey }}-{{ $mIdx }}">
                    <div class="flex-1 space-y-1.5">
                        <input type="text"
                            wire:model.defer="weekInputs.{{ $wKey }}.materials.{{ $mIdx }}.name"
                            placeholder="Name (e.g. Week {{ $week->week_no }} Slides)"
                            class="w-full text-xs rounded-lg border border-slate-300 bg-white px-3 py-1.5
                                   focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none
                                   placeholder:text-slate-300" />
                        <input type="url"
                            wire:model.defer="weekInputs.{{ $wKey }}.materials.{{ $mIdx }}.url"
                            placeholder="https://…"
                            class="w-full text-xs rounded-lg border border-slate-300 bg-white px-3 py-1.5
                                   focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none
                                   placeholder:text-slate-300" />
                    </div>
                    @if (count($weekInputs[$wKey]['materials'] ?? []) > 1)
                        <button type="button"
                            wire:click="removeMaterial({{ $week->week_no }}, {{ $mIdx }})"
                            class="shrink-0 mt-1 p-1.5 text-slate-400 hover:text-rose-500
                                   hover:bg-rose-50 rounded-md transition-colors"
                            title="Remove">
                            <i class="bx bx-trash text-sm"></i>
                        </button>
                    @else
                        <span class="w-7 shrink-0 mt-1"></span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ── Per-week footer: hint + reset + save ────────────────────────────────── --}}
<div class="flex items-center justify-between mt-5 pt-3 border-t border-slate-100">

    <x-feedback-status.alert type="info" :showTitle="false" class="text-xs">
        <span>Changes are saved automatically when you collapse or use Save All above.</span>
    </x-feedback-status.alert>

    <div class="flex items-center gap-2">
        <x-button variant="sm-cancel"
            wire:click="resetWeek({{ $week->week_no }})"
            wireTarget="resetWeek({{ $week->week_no }})"
            wire:confirm="Reset Week {{ $week->week_no }}? This will clear all content for this week. Cannot be undone."
            loading="Resetting…">
            <i class="bx bx-reset"></i> Reset
        </x-button>

        <x-button variant="sm-success"
            wire:click="saveWeek({{ $week->week_no }})"
            wireTarget="saveWeek({{ $week->week_no }})"
            loading="Saving…">
            <i class="bx bx-save"></i> Save Week {{ $week->week_no }}
        </x-button>
    </div>

</div>
