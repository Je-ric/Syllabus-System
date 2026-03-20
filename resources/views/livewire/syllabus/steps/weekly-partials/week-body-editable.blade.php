{{--
    Partial: weekly-partials/week-body-editable.blade.php
    Full editable body for a normal (unlocked) week.
--}}

{{-- ── MVGO notice (Week 1 only) ──────────────────────────────────────────── --}}
@if ($isMvgo)
    <x-feedback-status.alert type="info" :showTitle="false" class="mb-4">
        <strong>Week 1 — MVGO.</strong>
        This week covers the Mission, Vision, Goals &amp; Objectives.
        All fields are editable. Assessment task is optional — if entered, it will appear in Course Evaluation.
    </x-feedback-status.alert>
@endif

{{-- ── Calendar events strip ──────────────────────────────────────────────── --}}
@if (count($events) > 0)
    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50/60 px-3 py-2.5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-amber-700 mb-1.5">
            <i class="bx bx-calendar-event"></i> Events this week
        </p>
        <ul class="space-y-1">
            @foreach ($events as $ev)
                @php
                    $evVariant = match ($ev['type']) {
                        'holiday' => 'emerald',
                        'break'   => 'blue',
                        default   => 'amber',
                    };
                @endphp
                <li class="flex items-center gap-2 text-xs text-slate-700">
                    <x-feedback-status.status-indicator :variant="$evVariant" :dot="true">
                        {{ str_replace('_', ' ', ucfirst($ev['type'])) }}
                    </x-feedback-status.status-indicator>
                    <span class="font-medium">{{ $ev['name'] }}</span>
                    <span class="text-slate-400">{{ $ev['date_display'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ── Outcome selector ────────────────────────────────────────────────────── --}}
<div class="mb-3 rounded-lg border border-slate-200 bg-white px-3 py-2.5">
    @if ($isMvgo)
        <x-form.label class="mb-1">Outcome</x-form.label>
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-100 bg-slate-50">
            <x-feedback-status.status-indicator variant="emerald" icon="bx bx-star">MVGO</x-feedback-status.status-indicator>
            <span class="text-xs text-slate-500">Mission-Vision-Goals-Objectives</span>
        </div>
    @else
        <x-form.label for="co_{{ $wKey }}" class="mb-1">Course Outcome</x-form.label>
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

{{-- ── Content fields ──────────────────────────────────────────────────────── --}}
<div class="rounded-lg border border-slate-200 bg-white p-3 mb-3">
    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3">Coverage Details</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

        <div>
            <x-form.label for="lo_{{ $wKey }}" class="mb-1">Unit Learning Outcomes</x-form.label>
            <x-form.textarea id="lo_{{ $wKey }}" rows="4"
                placeholder="Enter learning outcomes…"
                wire:model.defer="weekInputs.{{ $wKey }}.learning_outcomes" />
        </div>

        <div>
            <x-form.label for="at_{{ $wKey }}" class="mb-1">
                Assessment Task
                @if ($isMvgo)
                    <span class="text-slate-400 font-normal text-xs ml-1">(optional)</span>
                @endif
            </x-form.label>
            <x-form.textarea id="at_{{ $wKey }}" rows="4"
                placeholder="{{ $isMvgo ? 'Optional — e.g. Orientation Quiz' : 'Enter assessment task…' }}"
                wire:model.defer="weekInputs.{{ $wKey }}.assessment_task" />
        </div>

        <div>
            <x-form.label for="tp_{{ $wKey }}" class="mb-1">Topics</x-form.label>
            <x-form.textarea id="tp_{{ $wKey }}" rows="4"
                placeholder="Enter topics covered…"
                wire:model.defer="weekInputs.{{ $wKey }}.topic" />
        </div>

        <div>
            <x-form.label for="tla_{{ $wKey }}" class="mb-1">Teaching &amp; Learning Activities</x-form.label>
            <x-form.textarea id="tla_{{ $wKey }}" rows="4"
                placeholder="Enter teaching activities…"
                wire:model.defer="weekInputs.{{ $wKey }}.teaching_activities" />
        </div>

    </div>
</div>

{{-- ── References & Materials ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">

    {{-- References --}}
    <div class="rounded-lg border border-slate-200 bg-white p-3">
        <div class="flex items-center justify-between mb-2">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                <i class="bx bx-book-open text-slate-400"></i> References
            </p>
            <x-button variant="sm-add"
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
                        class="flex-1 text-xs rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5
                               focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none
                               focus:bg-white placeholder:text-slate-300 transition-colors" />
                    @if (count($weekInputs[$wKey]['references'] ?? []) > 1)
                        <button type="button"
                            wire:click="removeReference({{ $week->week_no }}, {{ $rIdx }})"
                            class="shrink-0 p-1.5 text-slate-300 hover:text-rose-500
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
    <div class="rounded-lg border border-slate-200 bg-white p-3">
        <div class="flex items-center justify-between mb-2">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                <i class="bx bx-link text-slate-400"></i> Online Materials
            </p>
            <x-button variant="sm-add"
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
                            class="w-full text-xs rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5
                                   focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none
                                   focus:bg-white placeholder:text-slate-300 transition-colors" />
                        <input type="url"
                            wire:model.defer="weekInputs.{{ $wKey }}.materials.{{ $mIdx }}.url"
                            placeholder="https://…"
                            class="w-full text-xs rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5
                                   focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none
                                   focus:bg-white placeholder:text-slate-300 transition-colors" />
                    </div>
                    @if (count($weekInputs[$wKey]['materials'] ?? []) > 1)
                        <button type="button"
                            wire:click="removeMaterial({{ $week->week_no }}, {{ $mIdx }})"
                            class="shrink-0 mt-1 p-1.5 text-slate-300 hover:text-rose-500
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

{{-- ── Footer: autosave hint + reset + save ───────────────────────────────── --}}
<div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">

    <p class="text-[11px] text-slate-400 flex items-center gap-1">
        <i class="bx bx-info-circle text-slate-300"></i>
        Auto-saved when you collapse this week or click Save All.
    </p>

    <div class="flex items-center gap-2 shrink-0">
        <x-button variant="sm-cancel"
            wire:click="resetWeek({{ $week->week_no }})"
            wireTarget="resetWeek({{ $week->week_no }})"
            wire:confirm="Reset Week {{ $week->week_no }}? This will clear all content for this week. Cannot be undone."
            loading="Resetting…">
            <i class="bx bx-reset"></i> Reset
        </x-button>

        <x-button variant="sm-add"
            wire:click="saveWeek({{ $week->week_no }})"
            wireTarget="saveWeek({{ $week->week_no }})"
            loading="Saving…">
            <i class="bx bx-save"></i> Save Week {{ $week->week_no }}
        </x-button>
    </div>

</div>
