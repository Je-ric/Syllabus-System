<div x-data="courseOutcomesManager(@entangle('courseOutcomes'))" class="space-y-4 text-slate-800">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <x-wizard.step-header
        title="Course Outcomes"
        description="Define what students should be able to do after completing this course. Align each outcome with the most relevant Program Outcomes - not every CO needs to cover all POs.">
    </x-wizard.step-header>

    {{-- ── CO rows ─────────────────────────────────────────────────────────── --}}
    <template x-for="(co, index) in cos" :key="co.id ?? ('new-' + index)">

        <div :class="co.id
                ? 'border-slate-200 bg-white/90'
                : 'border-amber-200 bg-amber-50/40'"
            class="rounded-2xl border shadow-sm p-4 transition-colors duration-200">

            <div class="flex items-start gap-3">

                {{-- CO badge --}}
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

                {{-- Textarea — pure Alpine, no blur sync --}}
                <div class="flex-1 min-w-0">
                    <textarea
                        x-model="co.description"
                        rows="2"
                        placeholder="Describe what students will be able to do after completing this course…"
                        class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-3 py-2
                                text-sm text-slate-800 placeholder:text-slate-300
                                focus:bg-white focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300
                                focus:outline-none transition-colors"></textarea>

                    {{-- Unsaved hint --}}
                    <p x-show="!co.id"
                        x-cloak
                        class="mt-1.5 flex items-center gap-1.5 text-xs text-amber-600">
                        <i class="bx bx-error-circle text-sm shrink-0"></i>
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
                    <i class="bx bx-trash text-base"></i>
                </button>

                {{-- REMOVE unsaved row — splice from Alpine array only --}}
                <button x-show="!co.id"
                    x-cloak
                    @click="cos.splice(index, 1)"
                    type="button"
                    class="mt-0.5 p-2 text-slate-400 hover:text-rose-600
                            hover:bg-rose-50 rounded-lg transition-colors"
                    title="Remove unsaved CO">
                    <i class="bx bx-x text-base"></i>
                </button>

            </div>
        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="cos.length === 0">
        <x-empty-state
            icon="bx-book-open"
            title="No Course Outcomes yet"
            message="Add the first one below." />
    </template>

    {{-- Validation hint --}}
    @if ($coAddError)
        <p class="text-xs text-red-600 flex items-center gap-1">
            <i class="bx bx-error-circle"></i> {{ $coAddError }}
        </p>
    @endif

    {{-- ── Action buttons ──────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 pt-1">

        <button @click="addCo()" type="button"
            class="flex-1 inline-flex items-center justify-center gap-2
                    border-2 border-dashed border-emerald-300 rounded-2xl p-3
                    text-sm font-semibold text-emerald-700
                    hover:border-emerald-500 hover:bg-emerald-50
                    transition-colors duration-150">
            <i class="bx bx-plus text-base"></i>
            Add Course Outcome
        </button>

        <button @click="saveCos()" type="button" :disabled="isSaving"
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl
                    bg-emerald-600 text-white text-sm font-semibold shadow-sm
                    hover:bg-emerald-700 active:bg-emerald-800
                    disabled:opacity-50 disabled:cursor-not-allowed
                    transition-colors duration-150">
            <span x-show="!isSaving" class="inline-flex items-center gap-2">
                <i class="bx bx-save text-base"></i> Save All
            </span>
            <span x-show="isSaving" x-cloak class="inline-flex items-center gap-2">
                <i class="bx bx-loader-alt bx-spin text-base"></i> Saving…
            </span>
        </button>
    </div>

    {{-- ── Program Outcomes reference ───────────────────────────────────────── --}}
    <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50/40 p-4">
        <h4 class="text-sm font-semibold text-emerald-800 mb-3 flex items-center gap-1.5">
            <i class="bx bx-list-check text-emerald-600"></i>
            Program Outcomes Reference
        </h4>

        @if (count($programOutcomes) === 0)
            <p class="text-sm text-slate-500 italic">No program outcomes found for this course.</p>
        @else
            <div class="space-y-2">
                @foreach ($programOutcomes as $po)
                    <div class="flex items-start gap-2.5 bg-white border border-emerald-200
                                rounded-lg px-3 py-2.5 shadow-sm">
                        <span class="shrink-0 font-bold text-emerald-700 text-sm mt-0.5">
                            {{ $po['po_code'] }}.
                        </span>
                        <p class="text-slate-700 text-sm flex-1">{{ $po['po_text'] }}</p>
                        @if (!empty($po['ied']))
                            <x-feedback-status.ied-badge :level="$po['ied']" />
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<script>
function courseOutcomesManager(initialCos) {
    return {
        cos:      initialCos,
        isSaving: false,

        addCo() {
            // Block if any existing row has a blank description
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
            this.isSaving = true;
            @this.call('saveCourseOutcomes', this.cos)
                .finally(() => { this.isSaving = false; });
        },
    };
}
</script>
