<div>

    <x-wizard.step-header
        title="Course Evaluation"
        description="Set the weight (%) for each assessment task. Rows are pulled from Weekly Coverage. The 60% passing standard applies per semester."
        :step="$stepNumber" />

    @if (count($rows) === 0)
        <x-feedback-status.empty-state
            icon="bx-calendar-event"
            title="No assessment tasks yet"
            message="Fill in assessment tasks in the Weekly Coverage step first. Exam weeks are auto-detected from calendar events." />
    @else

    {{-- Alpine owns the live totals. wire:model.blur syncs to Livewire on blur (for saving).
         x-model keeps Alpine in sync as the user types — no network requests. --}}
    <div x-data="{
        lec: @js(collect($rows)->mapWithKeys(fn($r) => [$r['lec']['week_content_id'] ?? '__' => (int)($inputs[$r['lec']['week_content_id'] ?? '']['weight'] ?? 0)])->filter(fn($v,$k) => $k !== '__')->all()),
        lab: @js(collect($rows)->mapWithKeys(fn($r) => [$r['lab']['week_content_id'] ?? '__' => (int)($inputs[$r['lab']['week_content_id'] ?? '']['weight'] ?? 0)])->filter(fn($v,$k) => $k !== '__')->all()),
        get lecTotal() { return Object.values(this.lec).reduce((s,v) => s + (parseInt(v)||0), 0); },
        get labTotal() { return Object.values(this.lab).reduce((s,v) => s + (parseInt(v)||0), 0); },
        lecStd: {{ $lecStdNum }},
        labStd: {{ $labStdNum }},
        hasLab: {{ $courseHasLab ? 'true' : 'false' }},
        evalNotesOpen: false,
        async flushToWire() {
            const weights = {};
            Object.entries(this.lec).forEach(([id, v]) => { weights[id] = parseInt(v) || 0; });
            Object.entries(this.lab).forEach(([id, v]) => { weights[id] = parseInt(v) || 0; });
            await $wire.setAllWeights(weights);
        },
    }"
    x-on:request-eval-flush.window="await flushToWire()"
    x-on:request-eval-flush-and-navigate.window="
        await flushToWire();
        await $wire.save();
        $dispatch('navigate-after-save', { step: $event.detail.toStep });
    "
    x-on:open-eval-notes-drawer.window="evalNotesOpen = true"
    x-on:sidebar-save-evaluation.window="$wire.save()">

        @include('livewire.syllabus.steps.evaluation-partials.notes-drawer')
        @include('livewire.syllabus.steps.evaluation-partials.table')

        {{-- Sticky save bar — status lives in the totals row above; bar is save-only --}}
        <div class="sticky bottom-0 z-10 mt-4 flex items-center justify-end gap-3 px-5 py-3
                    rounded-xl border border-[#dedee2] bg-white/95 backdrop-blur-sm"
             style="box-shadow: 0 -2px 16px rgba(0,0,0,.10);">

            {{-- Unsaved changes indicator — visible only when totals are non-zero but not yet saved --}}
            <span class="flex items-center gap-1.5 text-xs text-amber-600 font-medium"
                x-show="(lecTotal > 0 || labTotal > 0) && !$wire.__instance.effects.dirty?.length === false"
                x-cloak>
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                Unsaved changes
            </span>

            <x-ui.button variant="sm-add" wire:click="save" wireTarget="save" loading="Saving…">
                <i class="bx bx-save"></i> Save Evaluation
            </x-ui.button>

        </div>

    </div>{{-- /x-data --}}

    @endif

</div>
