<div>
    {{--
        COURSE OUTCOMES STEP — Inline / Draft-first (PEO-style)
        ─────────────────────────────────────────────────────────────────────────────
        UX model identical to PEOs:
          • rows are always visible and directly editable
          • each row tracks _dirty / _original for change indicators
          • unsaved new rows have id = null
          • "Save All" persists everything in one Livewire call
          • "Revert" restores all rows to server state
          • individual delete on persisted rows calls Livewire immediately (no batch)
        ─────────────────────────────────────────────────────────────────────────────
    --}}
    
    @include('livewire.programs.partials.confirm-modal', ['confirmNs' => 'co'])
    
    <div x-data="coManager(@js(collect($outcomes)->values()->all()), @js($syllabusId))"
         x-on:co-all-saved.window="onSaved($event.detail.outcomes)"
         x-on:co-save-failed.window="isSaving = false"
         class="space-y-5">
    
        <x-wizard.step-header
            title="Course Outcomes"
            icon="book-open"
            description="Add outcomes below. Changes are staged until you click Save All." />
    
        {{-- Pending changes bar --}}
        <template x-if="hasPending()">
            <div class="flex flex-wrap items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-200 bg-amber-50 text-[13px]">
                <i class="bx bx-error-circle text-amber-500 shrink-0"></i>
                <span class="text-amber-700 font-semibold">Unsaved changes:</span>
                <template x-if="pendingSummary().added > 0">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-semibold">
                        <i class="bx bx-plus text-xs"></i>
                        <span x-text="pendingSummary().added + ' new'"></span>
                    </span>
                </template>
                <template x-if="pendingSummary().modified > 0">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[11px] font-semibold">
                        <i class="bx bx-edit-alt text-xs"></i>
                        <span x-text="pendingSummary().modified + ' modified'"></span>
                    </span>
                </template>
                <span class="ml-auto text-[11px] text-amber-600">Click <strong>Save All</strong> to apply.</span>
            </div>
        </template>
    
        {{-- CO rows --}}
        <div class="space-y-2">
            <template x-for="(co, index) in outcomes" :key="co._key">
                <div class="rounded-xl border transition-all duration-200"
                    :class="{
                        'border-emerald-300 bg-emerald-50/40 shadow-emerald-100': !co.id,
                        'border-amber-300 bg-amber-50/30 shadow-amber-100':       co.id && co._dirty,
                        'border-slate-200 bg-white shadow-slate-100':             co.id && !co._dirty
                    }"
                    style="box-shadow:0 1px 8px rgba(0,0,0,.05);">
    
                    <div class="flex items-start gap-3 px-4 py-3 border-l-4 rounded-xl"
                        :class="{
                            'border-emerald-400': !co.id,
                            'border-amber-400':   co.id && co._dirty,
                            'border-emerald-700': co.id && !co._dirty,
                        }">
    
                        {{-- Code badge --}}
                        <div class="shrink-0 flex flex-col items-center gap-1 mt-0.5">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-[11px] font-bold transition-colors"
                                :class="{
                                    'bg-emerald-100 text-emerald-700 ring-2 ring-emerald-400': !co.id,
                                    'bg-amber-100 text-amber-700 ring-2 ring-amber-400':       co.id && co._dirty,
                                    'bg-emerald-50 text-emerald-800 ring-2 ring-emerald-300':  co.id && !co._dirty
                                }"
                                x-text="co.co_code">
                            </span>
                        </div>
    
                        {{-- Textarea --}}
                        <div class="flex-1 min-w-0">
                            <textarea
                                x-model="co.description"
                                x-on:input="markDirty(co)"
                                rows="3"
                                placeholder="Describe what students will be able to do after this outcome…"
                                x-bind:disabled="isSaving"
                                class="w-full rounded-lg border px-3 py-2 text-[13px] text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 transition-all resize-none leading-relaxed disabled:opacity-50"
                                :class="{
                                    'border-amber-300 bg-amber-50/50 focus:border-amber-400 focus:ring-amber-100':       co.id && co._dirty,
                                    'border-emerald-300 bg-emerald-50/50 focus:border-emerald-400 focus:ring-emerald-100': !co.id,
                                    'border-slate-200 bg-white focus:border-emerald-400 focus:ring-emerald-100':           co.id && !co._dirty
                                }"></textarea>
    
                            <template x-if="!co.id">
                                <p class="mt-1 flex items-center gap-1 text-[11px] text-emerald-600 font-medium">
                                    <i class="bx bx-plus-circle text-sm shrink-0"></i>
                                    New — click <strong class="mx-0.5">Save All</strong> to persist.
                                </p>
                            </template>
                            <template x-if="co.id && co._dirty">
                                <p class="mt-1 flex items-center gap-1 text-[11px] text-amber-600 font-medium">
                                    <i class="bx bx-edit-alt text-sm shrink-0"></i>
                                    Modified — not saved yet.
                                </p>
                            </template>
                        </div>
    
                        {{-- Delete saved row --}}
                        <button x-show="co.id" type="button"
                            x-on:click="deleteCo(co)"
                            x-bind:disabled="isSaving"
                            class="shrink-0 mt-0.5 p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors disabled:opacity-40"
                            title="Delete CO">
                            <i class="bx bx-trash text-base"></i>
                        </button>
    
                        {{-- Remove unsaved row --}}
                        <button x-show="!co.id" x-cloak type="button"
                            x-on:click="outcomes.splice(index, 1); resequenceCodes()"
                            x-bind:disabled="isSaving"
                            class="shrink-0 mt-0.5 p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors disabled:opacity-40"
                            title="Remove">
                            <i class="bx bx-x text-lg"></i>
                        </button>
                    </div>
                </div>
            </template>
    
            {{-- Empty state --}}
            <template x-if="outcomes.length === 0">
                <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 py-12 text-center">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-100 mb-3">
                        <i class="bx bx-book-open text-3xl text-slate-300"></i>
                    </span>
                    <p class="text-[13px] font-semibold text-slate-500">No Course Outcomes yet</p>
                    <p class="text-[12px] text-slate-400 mt-0.5">Click <strong>Add Course Outcome</strong> below to get started.</p>
                </div>
            </template>
        </div>
    
        {{-- Action row --}}
        <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
            <x-button variant="add-dashed" type="button" x-on:click="addCo()"
                x-bind:disabled="isSaving" class="flex-1">
                <i class="bx bx-plus text-base"></i> Add Course Outcome
            </x-button>
    
            <template x-if="hasPending()">
                <x-button variant="cancel" type="button" x-on:click="revert()">
                    <i class="bx bx-undo text-base leading-none"></i> Revert
                </x-button>
            </template>
    
            <x-button variant="add-button" type="button" x-on:click="saveAll()"
                x-bind:disabled="isSaving" class="whitespace-nowrap relative">
                <template x-if="hasPending()">
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-amber-400 ring-2 ring-white animate-pulse"></span>
                </template>
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
    
        {{-- Program Outcomes Reference --}}
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-2">Reference</p>
            @include('livewire.syllabus.steps.outcome-partials.po-reference')
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
    
            markDirty(co) {
                if (co.id) co._dirty = (co.description !== co._original);
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
                // Re-number ALL rows (saved + unsaved) so display codes stay sequential
                this.outcomes.forEach((o, i) => {
                    o.co_code = 'CO' + (i + 1);
                });
            },
    
            addCo() {
                if (this.outcomes.some(o => !o.description?.trim())) {
                    window.dispatchEvent(new CustomEvent('lw-toast', {
                        detail: { type: 'warning', message: 'Fill in the blank CO before adding another.' }
                    }));
                    return;
                }
                this._keyCounter++;
                const idx = this.outcomes.length;
                this.outcomes.push({
                    id: null,
                    co_code: 'CO' + (idx + 1),
                    description: '',
                    _dirty: false,
                    _original: '',
                    _key: 'new-' + this._keyCounter,
                });
            },
    
            revert() {
                this.outcomes = this.outcomes
                    .filter(o => o.id)
                    .map(o => ({ ...o, description: o._original, _dirty: false }));
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
                // Validate: no blank rows
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