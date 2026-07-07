<div>

    <x-wizard.step-header
        title="Course Evaluation"
        description="Set the weight (%) for each assessment task. Rows are pulled from Weekly Coverage. The 60% passing standard applies per semester." />

    @if (count($rows) === 0)
        <x-empty-state
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
    ">

        @include('livewire.syllabus.steps.evaluation-partials.table')
        @include('livewire.syllabus.steps.evaluation-partials.notes')

        {{-- Sticky save bar --}}
        <div class="sticky bottom-0 z-10 mt-4 flex items-center justify-between gap-4 px-5 py-3
                    rounded-xl border border-[#dedee2] bg-white/95 backdrop-blur-sm"
             style="box-shadow: 0 -2px 16px rgba(0,0,0,.10);">

            <div class="flex items-center gap-4 text-sm">
                {{-- LEC total --}}
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full transition-colors"
                        :class="lecTotal === lecStd && lecTotal > 0 ? 'bg-emerald-500' : (lecTotal > 0 ? 'bg-rose-400' : 'bg-slate-300')"></span>
                    <span class="font-semibold transition-colors"
                        :class="lecTotal === lecStd && lecTotal > 0 ? 'text-emerald-700' : (lecTotal > 0 ? 'text-rose-600' : 'text-slate-400')">
                        LEC <span x-text="lecTotal"></span>&thinsp;/&thinsp;{{ $lecStdNum }}%
                    </span>
                    <span class="text-xs text-rose-500 font-normal"
                        x-show="lecTotal > 0 && lecTotal !== lecStd"
                        x-text="'must equal {{ $lecStdNum }}%'"></span>
                </span>

                @if ($courseHasLab)
                    <span class="text-slate-200">|</span>
                    {{-- LAB total --}}
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full transition-colors"
                            :class="labTotal === labStd && labTotal > 0 ? 'bg-blue-500' : (labTotal > 0 ? 'bg-rose-400' : 'bg-slate-300')"></span>
                        <span class="font-semibold transition-colors"
                            :class="labTotal === labStd && labTotal > 0 ? 'text-blue-700' : (labTotal > 0 ? 'text-rose-600' : 'text-slate-400')">
                            LAB <span x-text="labTotal"></span>&thinsp;/&thinsp;{{ $labStdNum }}%
                        </span>
                        <span class="text-xs text-rose-500 font-normal"
                            x-show="labTotal > 0 && labTotal !== labStd"
                            x-text="'must equal {{ $labStdNum }}%'"></span>
                    </span>
                @endif
            </div>

            <x-button variant="sm-add" wire:click="save" wireTarget="save" loading="Saving…">
                <i class="bx bx-save"></i> Save Evaluation
            </x-button>

        </div>

    </div>{{-- /x-data --}}

    @endif

</div>
