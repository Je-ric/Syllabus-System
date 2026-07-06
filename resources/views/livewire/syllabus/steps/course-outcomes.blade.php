<div>
    @include('livewire.programs.partials.confirm-modal', ['confirmNs' => 'co'])

    {{-- Drawers live here so they have access to $programOutcomes and $courseInfo --}}
    <div x-data="{ courseInfoOpen: false, poRefOpen: false }"
         x-on:open-course-info-drawer.window="courseInfoOpen = true"
         x-on:open-po-ref-drawer.window="poRefOpen = true">
        @include('livewire.syllabus.steps.outcomes-partials.course-info-drawer')
        @include('livewire.syllabus.steps.outcomes-partials.po-reference-drawer')
    </div>

    <div x-data="coManager(@js(collect($outcomes)->values()->all()), @js($syllabusId))"
         x-on:co-all-saved.window="onSaved($event.detail.outcomes)"
         x-on:co-save-failed.window="isSaving = false"
         x-on:request-co-flush-step.window="hasPending() ? saveAll() : $wire.dispatch('syllabus-step-saved', { step: 'course_outcomes' })"
         x-on:request-co-save-and-navigate.window="
             if (hasPending()) {
                 await saveAll();
             }
             await $wire.onCoSaveAndNavigate($event.detail.toStep);
         "
         class="space-y-5">

        <x-wizard.step-header
            title="Course Outcomes"
            description="Define what students will achieve. Add outcomes below — changes are staged until you click Save All." />

        {{-- Pending changes bar --}}
        <template x-if="hasPending()">
            <div class="flex flex-wrap items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-200 bg-amber-50 text-sm">
                <i class="bx bx-error-circle text-amber-500 shrink-0"></i>
                <span class="text-amber-700 font-semibold">Unsaved changes:</span>
                <template x-if="pendingSummary().added > 0">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">
                        <i class="bx bx-plus text-xs"></i>
                        <span x-text="pendingSummary().added + ' new'"></span>
                    </span>
                </template>
                <template x-if="pendingSummary().modified > 0">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                        <i class="bx bx-edit-alt text-xs"></i>
                        <span x-text="pendingSummary().modified + ' modified'"></span>
                    </span>
                </template>
                <span class="ml-auto text-xs text-amber-600">Click <strong>Save All</strong> to apply.</span>
            </div>
        </template>

        {{-- CO view-only modal --}}
        @include('livewire.syllabus.steps.outcomes-partials.co-view-modal')

        {{-- CO rows (compact) --}}
        <div class="rounded-xl border border-slate-200 overflow-hidden divide-y divide-slate-100" style="box-shadow:0 1px 4px rgba(0,0,0,.05);">
            <template x-for="(co, index) in outcomes" :key="co._key">
                <div class="flex items-center gap-3 px-4 py-3 transition-colors"
                    :class="{
                        'bg-emerald-50/50': !co.id,
                        'bg-amber-50/30':   co.id && co._dirty,
                        'bg-white':         co.id && !co._dirty
                    }">

                    <span class="shrink-0 w-1 h-8 rounded-full"
                        :class="{
                            'bg-emerald-400': !co.id,
                            'bg-amber-400':   co.id && co._dirty,
                            'bg-emerald-700': co.id && !co._dirty
                        }"></span>

                    <span class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-xl text-[11px] font-bold"
                        :class="{
                            'bg-emerald-100 text-emerald-700 ring-2 ring-emerald-400': !co.id,
                            'bg-amber-100 text-amber-700 ring-2 ring-amber-400':       co.id && co._dirty,
                            'bg-emerald-50 text-emerald-800 ring-2 ring-emerald-300':  co.id && !co._dirty
                        }"
                        x-text="co.co_code">
                </span>

                    <div class="flex-1 min-w-0">
                        <textarea
                            x-model="co.description"
                            x-on:input="markDirty(co); autoResize($el);"
                            x-init="autoResize($el)"
                            rows="2"
                            placeholder="Describe what students will be able to do after this outcome…"
                            x-bind:disabled="isSaving"
                            class="w-full rounded-lg border px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 transition-all resize-none leading-relaxed disabled:opacity-50"
                            :class="{
                                'border-amber-300 bg-amber-50/50 focus:border-amber-400 focus:ring-amber-100':       co.id && co._dirty,
                                'border-emerald-300 bg-emerald-50/50 focus:border-emerald-400 focus:ring-emerald-100': !co.id,
                                'border-slate-200 bg-white focus:border-emerald-400 focus:ring-emerald-100':           co.id && !co._dirty
                            }"></textarea>
                        <span x-show="!co.id" class="mt-0.5 flex items-center gap-1 text-xs text-emerald-600 font-medium">
                            <i class="bx bx-plus-circle text-sm shrink-0"></i> New — click <strong class="mx-0.5">Save All</strong>
                        </span>
                        <span x-show="co.id && co._dirty" x-cloak class="mt-0.5 flex items-center gap-1 text-xs text-amber-600 font-medium">
                            <i class="bx bx-edit-alt text-sm shrink-0"></i> Modified — not saved yet
                        </span>
                    </div>

                    {{-- View detail (saved & unmodified only) --}}
                    <button x-show="co.id && !co._dirty" type="button"
                        x-on:click="viewModal = { co_code: co.co_code, description: co.description }; $nextTick(() => document.getElementById('co-view-modal').showModal())"
                        x-bind:disabled="isSaving"
                        class="shrink-0 p-2 text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors disabled:opacity-40"
                        title="View full">
                        <i class="bx bx-expand-alt text-base"></i>
                    </button>

                    {{-- Delete saved row --}}
                    <button x-show="co.id" type="button"
                        x-on:click="deleteCo(co)"
                        x-bind:disabled="isSaving"
                        class="shrink-0 p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors disabled:opacity-40"
                        title="Delete">
                        <i class="bx bx-trash text-base"></i>
                    </button>

                    {{-- Remove unsaved row --}}
                    <button x-show="!co.id" x-cloak type="button"
                        x-on:click="removeUnsaved(co, index)"
                        x-bind:disabled="isSaving"
                        class="shrink-0 p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors disabled:opacity-40"
                        title="Remove">
                        <i class="bx bx-x text-lg"></i>
                    </button>
                </div>
            </template>

            {{-- Empty state --}}
            <template x-if="outcomes.length === 0">
                <div class="py-12 text-center bg-slate-50">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-100 mb-3">
                        <i class="bx bx-book-open text-3xl text-slate-300"></i>
                    </span>
                    <p class="text-sm font-semibold text-slate-500">No Course Outcomes yet</p>
                    <p class="text-xs text-slate-400 mt-0.5">Click <strong>Add Course Outcome</strong> below to get started.</p>
                </div>
            </template>
        </div>

        {{-- Action row: Add + Revert (left), Save All (right) --}}
        <div class="flex items-center justify-between gap-2 pt-3 border-t border-[#e2e8f0]">
            <div class="flex items-center gap-2">
                <x-button variant="add-dashed" type="button" x-on:click="addCo()" x-bind:disabled="isSaving">
                    <i class="bx bx-plus text-base"></i> Add Course Outcome
                </x-button>
                <x-button x-show="hasPending()" x-cloak variant="cancel" type="button" x-on:click="revert()">
                    <i class="bx bx-undo text-base leading-none"></i> Revert
                </x-button>
            </div>
            <x-button variant="add-button" type="button" x-on:click="saveAll()" x-bind:disabled="isSaving" class="whitespace-nowrap relative">
                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-amber-400 ring-2 ring-white animate-pulse"
                    x-show="hasPending()" x-cloak></span>
                <span x-show="!isSaving" class="inline-flex items-center gap-1.5 leading-none">
                    <i class="bx bx-save text-base leading-none"></i> Save All
                </span>
                <span x-show="isSaving" x-cloak class="inline-flex items-center gap-1.5 leading-none">
                    <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="leading-none">Saving…</span>
                </span>
            </x-button>
        </div>

    </div>

    <script>
    function coManager(initialOutcomes, syllabusId) {
        return {
            outcomes:    initialOutcomes.map((o, i) => ({
                ...o,
                _dirty:    false,
                _original: o.description,
                _key:      o.id ?? ('new-' + i),
            })),
            isSaving:    false,
            _keyCounter: initialOutcomes.length,
            viewModal:   { co_code: '', description: '' },

            // autoResize
            autoResize(el) {
                el.style.height = 'auto';
                el.style.height = el.scrollHeight + 'px';
            },

            markDirty(co) {
                if (co.id) co._dirty = (co.description !== co._original);
                this.$wire.dispatch('syllabus-step-dirty', { step: 'course_outcomes', dirty: this.hasPending() });
            },

            hasPending() {
                return this.outcomes.some(o => !o.id || o._dirty);
            },

            pendingSummary() {
                return {
                    added:    this.outcomes.filter(o => !o.id).length,
                    modified: this.outcomes.filter(o => o.id && o._dirty).length,
                    get total() { return this.added + this.modified; }
                };
            },

            resequenceCodes() {
                this.outcomes.forEach((o, i) => { o.co_code = 'CO' + (i + 1); });
            },

            addCo() {
                if (this.outcomes.some(o => !o.description?.trim())) {
                    window.dispatchEvent(new CustomEvent('lw-toast', {
                        detail: { type: 'warning', message: 'Fill in the blank CO before adding another.' }
                    }));
                    return;
                }
                this._keyCounter++;
                this.outcomes.push({
                    id: null,
                    co_code: 'CO' + (this.outcomes.length + 1),
                    description: '',
                    _dirty: false,
                    _original: '',
                    _key: 'new-' + this._keyCounter,
                });
                this.$wire.dispatch('syllabus-step-dirty', { step: 'course_outcomes', dirty: true });
            },

            revert() {
                this.outcomes = this.outcomes
                    .filter(o => o.id)
                    .map(o => ({ ...o, description: o._original, _dirty: false }));
                this.$wire.dispatch('syllabus-step-dirty', { step: 'course_outcomes', dirty: false });
            },

            async removeUnsaved(co, index) {
                if (co.description?.trim()) {
                    const ok = await this._confirm({
                        title: 'Remove unsaved CO?',
                        message: 'This outcome has not been saved yet. Remove it?',
                        confirmLabel: 'Remove',
                        confirmClass: 'bg-rose-600 hover:bg-rose-700 text-white',
                    });
                    if (!ok) return;
                }
                this.outcomes.splice(index, 1);
                this.resequenceCodes();
                this.$wire.dispatch('syllabus-step-dirty', { step: 'course_outcomes', dirty: this.hasPending() });
            },

            async deleteCo(co) {
                if (!co.id) return;
                const ok = await this._confirm({
                    title: 'Delete ' + co.co_code + '?',
                    message: 'This course outcome will be permanently removed and codes will be re-sequenced.',
                    confirmLabel: 'Delete',
                    confirmClass: 'bg-rose-600 hover:bg-rose-700 text-white',
                });
                if (!ok) return;
                this.isSaving = true;
                try {
                    await this.$wire.call('deleteSingle', co.id);
                } finally {
                    this.isSaving = false;
                }
            },

            async saveAll() {
                if (!this.hasPending()) {
                    window.dispatchEvent(new CustomEvent('lw-toast', {
                        detail: { type: 'info', message: 'No changes to save.' }
                    }));
                    return;
                }
                const blank = this.outcomes.find(o => !o.description?.trim());
                if (blank) {
                    window.dispatchEvent(new CustomEvent('lw-toast', {
                        detail: { type: 'warning', message: 'All course outcomes must have a description.' }
                    }));
                    return;
                }
                this.isSaving = true;
                try {
                    await this.$wire.call('saveAll',
                        this.outcomes.map(o => ({ id: o.id, description: o.description, isNew: !o.id }))
                    );
                } catch {
                    this.isSaving = false;
                }
            },

            onSaved(fresh) {
                this.outcomes = fresh.map((o, i) => ({
                    ...o,
                    _dirty:    false,
                    _original: o.description,
                    _key:      o.id ?? ('new-' + i),
                }));
                this.isSaving = false;
                this.$wire.dispatch('syllabus-step-dirty', { step: 'course_outcomes', dirty: false });
            },

            _confirm(detail) {
                return new Promise(resolve => {
                    window.dispatchEvent(new CustomEvent('confirm-dialog:co', {
                        detail: { ...detail, _resolve: resolve }
                    }));
                });
            },
        };
    }
    </script>

</div>
