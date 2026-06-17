{{--
    COURSE OUTCOMES STEP — Batch / Draft-first
    ─────────────────────────────────────────────────────────────────────────────
    UX model: all edits are local until "Save All" is clicked.
      • drafts[]     — full working copy (adds, edits, deletes all live here)
      • deletedIds[] — IDs of persisted COs marked for removal
      • editingIdx   — which draft index is in edit mode (null = all read)
      • saving       — bool, true while Livewire saveAll() is in flight
      • isDirty      — computed: any addition, edit, or deletion pending

    Livewire owns:
      $outcomes  — authoritative list, only updated after successful saveAll()
      $programOutcomes — read-only reference

    Livewire dispatches:
      co-all-saved   → Alpine resets to clean state (re-syncs from $outcomes)
      co-save-failed → Alpine resets saving flag, preserves drafts
    ─────────────────────────────────────────────────────────────────────────────
--}}

<div
    x-data="{
        drafts:     {{ Js::from(collect($outcomes)->map(fn($o) => [...$o, 'isNew' => false, 'isDirty' => false])->values()) }},
        deletedIds: [],
        editingIdx: null,
        editBuf:    '',
        saving:     false,

        get isDirty() {
            return this.deletedIds.length > 0
                || this.drafts.some(d => d.isNew || d.isDirty);
        },
        get pendingSummary() {
            const adds = this.drafts.filter(d => d.isNew).length;
            const edits = this.drafts.filter(d => !d.isNew && d.isDirty).length;
            const dels = this.deletedIds.length;
            const parts = [];
            if (adds)  parts.push(adds  + (adds  === 1 ? ' new'  : ' new'));
            if (edits) parts.push(edits + (edits === 1 ? ' edit' : ' edits'));
            if (dels)  parts.push(dels  + (dels  === 1 ? ' deletion' : ' deletions'));
            return parts.join(', ') + ' pending';
        },

        startEdit(idx) {
            this.editingIdx = idx;
            this.editBuf    = this.drafts[idx].description;
            this.$nextTick(() => {
                const el = document.getElementById('edit-ta-' + idx);
                if (el) { el.focus(); el.setSelectionRange(el.value.length, el.value.length); }
            });
        },
        commitEdit(idx) {
            const text = this.editBuf.trim();
            if (!text) return;
            const orig = {{ Js::from(collect($outcomes)->pluck('description', 'id')) }};
            this.drafts[idx].description = text;
            if (!this.drafts[idx].isNew) {
                this.drafts[idx].isDirty = text !== (orig[this.drafts[idx].id] ?? text);
            }
            this.editingIdx = null;
            this.editBuf    = '';
        },
        cancelEdit() {
            this.editingIdx = null;
            this.editBuf    = '';
        },

        addDraft() {
            this.drafts.push({ id: null, co_code: 'CO' + (this.drafts.length + 1), description: '', isNew: true, isDirty: false });
            const newIdx = this.drafts.length - 1;
            this.editingIdx = newIdx;
            this.editBuf    = '';
            this.$nextTick(() => {
                const el = document.getElementById('edit-ta-' + newIdx);
                if (el) el.focus();
            });
        },
        removeDraft(idx) {
            const draft = this.drafts[idx];
            if (!draft.isNew) {
                if (!confirm('Remove ' + draft.co_code + '? Changes are not saved yet — this will mark it for deletion.')) return;
                this.deletedIds.push(draft.id);
            }
            this.drafts.splice(idx, 1);
            // Recompute CO codes for display
            this.drafts.forEach((d, i) => { if (d.isNew) d.co_code = 'CO' + (i + 1); });
            if (this.editingIdx === idx) { this.editingIdx = null; this.editBuf = ''; }
            else if (this.editingIdx > idx) this.editingIdx--;
        },

        async saveAll() {
            // Commit any open edit first
            if (this.editingIdx !== null) this.commitEdit(this.editingIdx);
            if (!this.isDirty) return;
            this.saving = true;
            await $wire.saveAll(
                this.drafts.map(d => ({ id: d.id, description: d.description, isNew: d.isNew })),
                this.deletedIds
            );
        },

        discardAll() {
            if (!confirm('Discard all unsaved changes?')) return;
            this.drafts     = {{ Js::from(collect($outcomes)->map(fn($o) => [...$o, 'isNew' => false, 'isDirty' => false])->values()) }};
            this.deletedIds = [];
            this.editingIdx = null;
            this.editBuf    = '';
        },

        syncFromServer(outcomes) {
            this.drafts     = outcomes.map(o => ({ ...o, isNew: false, isDirty: false }));
            this.deletedIds = [];
            this.editingIdx = null;
            this.editBuf    = '';
            this.saving     = false;
        }
    }"
    x-on:co-all-saved.window="syncFromServer($event.detail.outcomes)"
    x-on:co-save-failed.window="saving = false"
    class="space-y-5">

    {{-- Step header --}}
    <x-wizard.step-header
        title="Course Outcomes"
        icon="book-open"
        description="Draft all outcomes, then save everything at once when you're ready." />

    {{-- Pending changes banner --}}
    <div x-show="isDirty" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="flex items-center justify-between gap-4 px-4 py-3 rounded-xl
                border border-amber-200 bg-amber-50">
        <div class="flex items-center gap-2 min-w-0">
            <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-lg bg-amber-100 text-amber-600">
                <i class="bx bx-edit text-base leading-none"></i>
            </span>
            <p class="text-[13px] font-medium text-amber-800 truncate" x-text="pendingSummary"></p>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <button type="button"
                x-on:click="discardAll()"
                x-bind:disabled="saving"
                class="text-[12px] font-medium text-amber-700 hover:text-amber-900
                       disabled:opacity-40 transition-colors underline underline-offset-2">
                Discard
            </button>
            <button type="button"
                x-on:click="saveAll()"
                x-bind:disabled="saving"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                       bg-[#16a34a] text-white text-[12px] font-semibold
                       hover:bg-[#15803d] disabled:opacity-50 transition-colors">
                <span x-show="!saving" class="inline-flex items-center gap-1.5">
                    <i class="bx bx-save text-sm leading-none"></i> Save All
                </span>
                <span x-show="saving" x-cloak class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Saving…
                </span>
            </button>
        </div>
    </div>

    {{-- CO draft cards --}}
    <div class="space-y-2">

        <template x-if="drafts.length === 0">
            <div class="flex flex-col items-center gap-3 py-14 text-center
                        rounded-xl border-2 border-dashed border-[#e2e8f0] bg-[#f8fafc]">
                <span class="flex items-center justify-center w-14 h-14 rounded-xl bg-[#dcfce7] text-[#16a34a]">
                    <i class="bx bx-book-open text-2xl"></i>
                </span>
                <div>
                    <p class="text-[13px] font-semibold text-[#0f172a]">No Course Outcomes yet</p>
                    <p class="text-[13px] text-[#94a3b8] mt-1">
                        Click <strong class="text-[#16a34a]">Add Course Outcome</strong> below to get started.
                    </p>
                </div>
            </div>
        </template>

        <template x-for="(draft, idx) in drafts" :key="idx">
            <div
                x-bind:class="{
                    'ring-2 ring-[#16a34a] ring-offset-1': editingIdx === idx,
                    'opacity-60':                          saving,
                    'border-amber-200 bg-amber-50/30':     draft.isDirty && editingIdx !== idx,
                    'border-[#bbf7d0] bg-[#f0fdf4]/30':   draft.isNew && editingIdx !== idx,
                    'border-[#e2e8f0] bg-white':           !draft.isDirty && !draft.isNew && editingIdx !== idx,
                }"
                class="rounded-xl border overflow-hidden transition-all duration-150"
                style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

                {{-- READ mode --}}
                <div x-show="editingIdx !== idx"
                     class="flex items-start gap-4 p-4"
                     x-bind:class="{
                         'border-l-[3px] border-[#16a34a]': !draft.isDirty && !draft.isNew,
                         'border-l-[3px] border-amber-400': draft.isDirty,
                         'border-l-[3px] border-emerald-300': draft.isNew,
                     }">

                    <span class="shrink-0 mt-0.5 inline-flex items-center justify-center
                                 w-10 h-10 rounded-xl text-white text-[13px] font-bold"
                          x-bind:class="{
                              'bg-[#16a34a]':   !draft.isDirty && !draft.isNew,
                              'bg-amber-400':    draft.isDirty,
                              'bg-emerald-300':  draft.isNew,
                          }"
                          x-text="draft.co_code">
                    </span>

                    <div class="flex-1 min-w-0 pt-1">
                        <p class="text-[13px] text-[#0f172a] leading-relaxed"
                           x-show="draft.description"
                           x-text="draft.description"></p>
                        <p class="text-[13px] text-[#94a3b8] italic"
                           x-show="!draft.description">
                            No description yet — click edit to add one.
                        </p>
                    </div>

                    {{-- State badge --}}
                    <span x-show="draft.isNew"
                          class="shrink-0 mt-1 text-[10px] font-bold uppercase tracking-[0.1em]
                                 px-1.5 py-0.5 rounded-md bg-emerald-100 text-emerald-700">
                        New
                    </span>
                    <span x-show="draft.isDirty && !draft.isNew"
                          class="shrink-0 mt-1 text-[10px] font-bold uppercase tracking-[0.1em]
                                 px-1.5 py-0.5 rounded-md bg-amber-100 text-amber-700">
                        Edited
                    </span>

                    <div class="shrink-0 flex items-center gap-1 ml-1">
                        <button type="button"
                            x-on:click="startEdit(idx)"
                            x-bind:disabled="saving"
                            title="Edit"
                            class="p-2 rounded-lg text-[#94a3b8] hover:text-[#16a34a] hover:bg-[#f0fdf4]
                                   disabled:opacity-40 transition-colors">
                            <i class="bx bx-edit-alt text-base leading-none"></i>
                        </button>
                        <button type="button"
                            x-on:click="removeDraft(idx)"
                            x-bind:disabled="saving"
                            title="Remove"
                            class="p-2 rounded-lg text-[#94a3b8] hover:text-rose-600 hover:bg-rose-50
                                   disabled:opacity-40 transition-colors">
                            <i class="bx bx-trash text-base leading-none"></i>
                        </button>
                    </div>
                </div>

                {{-- EDIT mode --}}
                <div x-show="editingIdx === idx" x-cloak
                     class="p-4 bg-[#f0fdf4]/40 space-y-3">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#166534]"
                       x-text="'Editing ' + draft.co_code"></p>
                    <textarea
                        x-bind:id="'edit-ta-' + idx"
                        x-model="editBuf"
                        rows="3"
                        placeholder="Describe what students will be able to do…"
                        x-on:keydown.escape="cancelEdit()"
                        class="w-full resize-none rounded-xl border border-[#bbf7d0] bg-white
                               px-3 py-2.5 text-[13px] text-[#0f172a]
                               placeholder:text-[#94a3b8] focus:border-[#16a34a] focus:outline-none transition-colors"
                        style="box-shadow:none"
                        onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                        onblur="this.style.boxShadow='none'"></textarea>
                    <div class="flex items-center gap-2 justify-between">
                        <p class="text-[13px] text-[#94a3b8]">
                            <i class="bx bx-info-circle"></i>
                            <kbd class="px-1 py-0.5 rounded border border-[#e2e8f0] bg-white text-[11px]">Esc</kbd> to cancel
                        </p>
                        <div class="flex items-center gap-2">
                            <x-button type="button" variant="cancel"
                                x-on:click="cancelEdit()">
                                <i class="bx bx-x"></i> Cancel
                            </x-button>
                            <x-button type="button" variant="add-button"
                                x-on:click="commitEdit(idx)"
                                x-bind:disabled="!editBuf.trim()">
                                <i class="bx bx-check"></i> Done
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Add button --}}
        <x-button type="button" variant="add-dashed"
            x-on:click="addDraft()"
            x-bind:disabled="saving"
            class="w-full">
            <i class="bx bx-plus text-lg"></i>
            Add Course Outcome
        </x-button>

    </div>

    {{-- Program Outcomes Reference (Offcanvas) --}}
    <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-2">
            Reference
        </p>
        @include('livewire.syllabus.steps.outcome-partials.po-reference')
    </div>

</div>
