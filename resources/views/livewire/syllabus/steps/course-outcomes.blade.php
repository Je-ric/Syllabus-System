<div x-data="courseOutcomesManager(@entangle('courseOutcomes'))" class="space-y-4 text-slate-800">

    {{-- ── Header with Save All button ───────────────────────────────────────── --}}
    <x-wizard.step-header
        title="Course Outcomes"
        icon="book-open"
        description="Define what students should be able to do after completing this course.
                        Align each outcome with the most relevant Program Outcomes —
                        not every CO needs to cover all POs.">

        {{--
            Save All — uses same variant as Course Evaluation's Save Evaluation button.
            Alpine isSaving drives the spinner. :disabled prevents double-clicks.
        --}}
        <button @click="saveCos()" type="button" :disabled="isSaving"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg
                    transition-colors duration-150
                    disabled:opacity-50 disabled:pointer-events-none
                    bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100">
            <span x-show="!isSaving" class="inline-flex items-center gap-1.5">
                <i class="bx bx-save" aria-hidden="true"></i> Save All
            </span>
            <span x-show="isSaving" x-cloak class="inline-flex items-center gap-1.5">
                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Saving…
            </span>
        </button>
    </x-wizard.step-header>

    {{-- ── CO rows ─────────────────────────────────────────────────────────── --}}
    <x-wizard.section title="Build Outcomes" icon="list-check" color="emerald">

        <div class="space-y-4">
            <template x-for="(co, index) in cos" :key="co.id ? 'saved-' + co.id : 'new-' + index">

                {{--
                    Row border/bg transitions immediately when co.id changes.
                    After saveCos() succeeds, this.cos is replaced with the server
                    response (all rows now have real IDs) — Alpine re-renders each row
                    from amber → emerald in the same tick. No page navigation needed.
                --}}
                <div :class="co.id
                        ? 'border-slate-200 bg-white/90'
                        : 'border-amber-200 bg-amber-50/40'"
                    class="rounded-2xl border shadow-sm p-4 transition-colors duration-200">

                    <div class="flex items-start gap-3">

                        {{-- CO code badge — amber when unsaved, emerald when saved --}}
                        <div class="shrink-0 pt-0.5">
                            <span :class="co.id
                                    ? 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200'
                                    : 'bg-amber-100 text-amber-700 ring-1 ring-amber-200'"
                                class="inline-flex items-center justify-center
                                       min-w-11 h-8 px-2 rounded-xl text-xs font-bold uppercase
                                       transition-colors duration-200">
                                <span x-text="'CO' + (index + 1)"></span>
                            </span>
                        </div>

                        {{-- Textarea — pure Alpine, no wire:model, no blur sync --}}
                        <div class="flex-1 min-w-0">
                            <textarea
                                x-model="co.description"
                                rows="2"
                                placeholder="Describe what students will be able to do after completing this course…"
                                class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-3 py-2
                                       text-sm text-slate-800 placeholder:text-slate-300
                                       focus:bg-white focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300
                                       focus:outline-none transition-colors"></textarea>

                            {{-- Amber "unsaved" hint — disappears the instant co.id is set --}}
                            <p x-show="!co.id"
                                x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="mt-1.5 flex items-center gap-1.5 text-xs text-amber-600">
                                <i class="bx bx-error-circle text-sm shrink-0" aria-hidden="true"></i>
                                Unsaved — click <strong>Save All</strong> to persist this row.
                            </p>
                        </div>

                        {{-- DELETE saved row — calls Livewire with confirmation --}}
                        <button x-show="co.id"
                            @click="
                                if (confirm('Remove this Course Outcome? CO codes will be re-sequenced.')) {
                                    $wire.call('removeSavedOutcome', co.id);
                                }
                            "
                            type="button"
                            class="mt-0.5 p-2 text-slate-400 hover:text-rose-600
                                   hover:bg-rose-50 rounded-lg transition-colors"
                            title="Delete saved CO">
                            <i class="bx bx-trash text-base" aria-hidden="true"></i>
                        </button>

                        {{-- REMOVE unsaved row — Alpine only, no server round-trip --}}
                        <button x-show="!co.id"
                            x-cloak
                            @click="cos.splice(index, 1)"
                            type="button"
                            class="mt-0.5 p-2 text-slate-400 hover:text-rose-600
                                   hover:bg-rose-50 rounded-lg transition-colors"
                            title="Remove unsaved CO">
                            <i class="bx bx-x text-base" aria-hidden="true"></i>
                        </button>

                    </div>
                </div>

            </template>
        </div>

        {{-- Empty state --}}
        <template x-if="cos.length === 0">
            <div class="flex flex-col items-center gap-2 py-10 text-center">
                <i class="bx bx-book-open text-3xl text-slate-200" aria-hidden="true"></i>
                <p class="text-sm font-medium text-slate-500">No Course Outcomes yet</p>
                <p class="text-xs text-slate-400">Add the first one below.</p>
            </div>
        </template>

    {{-- Validation hint --}}
    @if ($coAddError)
        <p class="text-xs text-red-600 flex items-center gap-1">
            <i class="bx bx-error-circle"></i> {{ $coAddError }}
        </p>
    @endif

        {{-- Add CO button — full width dashed --}}
        <div class="pt-1">
            <button @click="addCo()" type="button"
                class="flex w-full items-center justify-center gap-2 px-4 py-3
                       border-2 border-dashed border-emerald-300 rounded-2xl
                       text-sm font-semibold text-emerald-700
                       hover:border-emerald-500 hover:bg-emerald-50
                       transition-colors duration-150">
                <i class="bx bx-plus text-base" aria-hidden="true"></i>
                Add Course Outcome
            </button>
        </div>

    </x-wizard.section>

    {{-- ── Program Outcomes reference panel ──────────────────────────────────── --}}
    @if (count($programOutcomes) > 0)
        <x-wizard.section title="Program Outcomes Reference" icon="list-check" color="slate">
            <div class="space-y-2">
                @foreach ($programOutcomes as $po)
                    <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                        <span class="mt-0.5 shrink-0 inline-flex items-center justify-center
                                    w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700
                                    text-xs font-bold uppercase ring-1 ring-emerald-200">
                        {{ $po['po_code'] }}.
                    </span>
                        <p class="text-sm text-slate-600 leading-relaxed flex-1">{{ $po['po_text'] }}</p>
                        @if (! empty($po['ied']))
                            <x-feedback-status.ied-badge :level="$po['ied']" />
                        @endif
                    </div>
                @endforeach
            </div>
        </x-wizard.section>
    @else
        <x-empty-state
            icon="list-check"
            title="No Program Outcomes"
            description="Program Outcomes are defined at the program level. If you think there should be POs here, check with your department's CSMS coordinator."
    @endif

</div>

<script>
function courseOutcomesManager(initialCos) {
    return {
        cos:      initialCos,
        isSaving: false,
        _wire:    null,   // set in init(); never call $wire directly in methods

        init() {
            // Capture $wire here — the ONE place it is reliably available
            // for a named-function Alpine component.
            this._wire = this.$wire;
        },

        addCo() {
            const hasBlank = this.cos.some(co => !co.description || !co.description.trim());
            if (hasBlank) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'warning', message: 'Fill in the blank CO before adding another.' }
                }));
                return;
            }
            this.cos.push({ id: null, temp_key: 'new_' + Date.now(), co_code: '', description: '' });
        },

        saveCos() {
            if (this.isSaving) return;   // guard against double-click
            this.isSaving = true;

            // Use this._wire (captured in init) — NOT $wire (undefined here)
            this._wire.call('saveCourseOutcomes', this.cos)
                .then((updated) => {
                    // updated = return value of PHP saveCourseOutcomes()
                    // It's the fresh array with real IDs and correct co_codes.
                    // Replacing this.cos triggers Alpine re-render:
                    //   co.id is now truthy → amber → emerald realtime.
                    if (Array.isArray(updated)) {
                        this.cos = updated;
                    }
                })
                .catch(() => {
                    // Swallow — PHP already dispatched an lw-toast for errors
                })
                .finally(() => {
                    this.isSaving = false;
                });
        },
    };
}
</script>
