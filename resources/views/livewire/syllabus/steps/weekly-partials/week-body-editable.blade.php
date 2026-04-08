{{-- weekly-partials/week-body-editable.blade.php --}}

{{-- MVGO notice --}}
@if ($isMvgo)
    <x-feedback-status.alert type="info" :showTitle="false" class="mb-4">
        <strong>Week 1 — MVGO.</strong>
        This week covers the Mission, Vision, Goals &amp; Objectives.
        All fields are editable. Assessment task is optional — if entered, it will appear in Course Evaluation.
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

{{-- Outcome selector --}}
<div class="mb-4">
    @if ($isMvgo)
        <x-form.label class="mb-1">Outcome</x-form.label>
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg border border-[#bbf7d0] bg-[#f0fdf4]">
            <x-feedback-status.status-indicator variant="brand" icon="bx bx-star">MVGO</x-feedback-status.status-indicator>
            <span class="text-[13px] text-[#475569]">Mission-Vision-Goals-Objectives</span>
        </div>
    @else
        <x-form.label for="co_{{ $wKey }}" class="mb-1">Course Outcome</x-form.label>
        <x-form.select id="co_{{ $wKey }}" wire:model.defer="weekInputs.{{ $wKey }}.course_outcome_id">
            <option value="">— Select Course Outcome —</option>
            @foreach ($courseOutcomes as $outcome)
                <option value="{{ $outcome['id'] }}">
                    {{ $outcome['co_code'] }} – {{ \Illuminate\Support\Str::limit($outcome['description'], 70) }}
                </option>
            @endforeach
        </x-form.select>
    @endif
</div>

{{-- Coverage fields --}}
<div class="mb-4">
    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">Coverage Details</p>
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
                    <span class="text-[#94a3b8] font-normal normal-case tracking-normal">(optional)</span>
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

{{-- References & Materials --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">

    {{-- References --}}
    <div>
        <div class="flex items-center justify-between mb-2">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#166534]">
                <i class="bx bx-book-open text-[#16a34a]"></i> References
            </p>
            <x-button variant="sm-add" wire:click="addReference({{ $week->week_no }})">
                <i class="bx bx-plus text-sm"></i> Add
            </x-button>
        </div>
        <div class="space-y-2">
            @foreach ($weekInputs[$wKey]['references'] ?? [['text' => '']] as $rIdx => $ref)
                <div class="flex items-center gap-2" wire:key="ref-{{ $wKey }}-{{ $rIdx }}">
                    <input type="text"
                        wire:model.defer="weekInputs.{{ $wKey }}.references.{{ $rIdx }}.text"
                        placeholder="e.g. Author (Year). Title. Publisher."
                        class="flex-1 text-[13px] rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-1.5
                               focus:border-[#16a34a] focus:outline-none focus:bg-white
                               placeholder:text-[#94a3b8] transition-colors"
                        style="box-shadow:none"
                        onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                        onblur="this.style.boxShadow='none'" />
                    @if (count($weekInputs[$wKey]['references'] ?? []) > 1)
                        <button type="button"
                            wire:click="removeReference({{ $week->week_no }}, {{ $rIdx }})"
                            class="shrink-0 p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition-colors"
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
    <div>
        <div class="flex items-center justify-between mb-2">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#1e40af]">
                <i class="bx bx-link text-[#3b82f6]"></i> Online Materials
            </p>
            <x-button variant="sm-add" wire:click="addMaterial({{ $week->week_no }})">
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
                            class="w-full text-[13px] rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-1.5
                                   focus:border-[#16a34a] focus:outline-none focus:bg-white
                                   placeholder:text-[#94a3b8] transition-colors"
                            style="box-shadow:none"
                            onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                            onblur="this.style.boxShadow='none'" />
                        <input type="url"
                            wire:model.defer="weekInputs.{{ $wKey }}.materials.{{ $mIdx }}.url"
                            placeholder="https://…"
                            class="w-full text-[13px] rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-1.5
                                   focus:border-[#16a34a] focus:outline-none focus:bg-white
                                   placeholder:text-[#94a3b8] transition-colors"
                            style="box-shadow:none"
                            onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                            onblur="this.style.boxShadow='none'" />
                    </div>
                    @if (count($weekInputs[$wKey]['materials'] ?? []) > 1)
                        <button type="button"
                            wire:click="removeMaterial({{ $week->week_no }}, {{ $mIdx }})"
                            class="shrink-0 mt-1 p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition-colors"
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

{{-- Footer --}}
<div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-[#e2e8f0]">
    <p class="text-[13px] text-[#94a3b8] flex items-center gap-1">
        <i class="bx bx-info-circle text-[#16a34a]"></i>
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
