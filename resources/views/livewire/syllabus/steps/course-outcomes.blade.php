{{--
    COURSE OUTCOMES STEP
    ─────────────────────────────────────────────────────────────────────────────
    UX model: individual-card CRUD, not batch save.
      • Each saved CO is a read-only card. Pencil → inline edit form.
      • Trash → confirm → delete spinner on that card only.
      • "Add" card always lives at the bottom, expands inline.
      • Program Outcomes reference is in a collapsible panel.

    Alpine owns all transient UI state:
      editingId  int|null  — which saved card is in edit mode
      addingNew  bool      — whether the add-form is open
      savingId   int|null  — which card is mid-save (spinner on that card only)
      deletingId int|null  — which card is mid-delete

    Livewire owns the $outcomes list and dispatches:
      co-saved         → Alpine resets editingId / addingNew / savingId
      co-deleted       → Alpine resets deletingId
      co-delete-failed → Alpine resets deletingId (on error)
    ─────────────────────────────────────────────────────────────────────────────
--}}

<div
    x-data="{
        editingId:  null,
        editDraft:  '',
        addingNew:  false,
        newDraft:   '',
        savingId:   null,
        deletingId: null,

        startEdit(id, currentText) {
            this.editingId = id;
            this.editDraft = currentText;
            this.addingNew = false;   // close add form if open
            // Focus the textarea after Alpine renders it
            this.$nextTick(() => {
                const el = document.getElementById('edit-ta-' + id);
                if (el) { el.focus(); el.setSelectionRange(el.value.length, el.value.length); }
            });
        },

        cancelEdit() {
            this.editingId = null;
            this.editDraft = '';
        },

        async saveEdit(id) {
            const text = this.editDraft.trim();
            if (!text) return;
            this.savingId = id;
            await $wire.updateOutcome(id, text);
            // co-saved event clears savingId + editingId
        },

        openAdd() {
            this.addingNew = true;
            this.editingId = null;   // close any open edit
            this.$nextTick(() => {
                const el = document.getElementById('new-co-ta');
                if (el) el.focus();
            });
        },

        cancelAdd() {
            this.addingNew = false;
            this.newDraft  = '';
        },

        async saveNew() {
            const text = this.newDraft.trim();
            if (!text) return;
            this.savingId = 'new';
            await $wire.createOutcome(text);
            // co-saved event clears savingId + addingNew + newDraft
        },

        async confirmDelete(id, coCode) {
            if (!confirm('Delete ' + coCode + '? This cannot be undone.')) return;
            this.deletingId = id;
            await $wire.deleteOutcome(id);
            // co-deleted / co-delete-failed clear deletingId
        }
    }"
    x-on:co-saved.window="
        editingId = null; editDraft = '';
        addingNew = false; newDraft = '';
        savingId  = null;
    "
    x-on:co-deleted.window="deletingId = null"
    x-on:co-delete-failed.window="deletingId = null"
    class="space-y-6">

    {{-- ══ Step header ══════════════════════════════════════════════════════════ --}}
    <x-wizard.step-header
        title="Course Outcomes"
        icon="book-open"
        description="Define what students should be able to do after completing this course. Each outcome is saved individually — just click Save on each card." />

    {{-- ══ CO cards ════════════════════════════════════════════════════════════ --}}
    <div class="space-y-3">

            {{-- Instruction strip (only when there are no outcomes yet) --}}
            @if (count($outcomes) === 0 && true)
                {{-- always shown until first CO is saved --}}
            @endif

            {{-- ─── Saved CO cards ────────────────────────────────────────────── --}}
            @forelse ($outcomes as $co)
                <div wire:key="co-{{ $co['id'] }}"
                    x-bind:class="{
                        'ring-2 ring-emerald-400 ring-offset-1': editingId === {{ $co['id'] }},
                        'opacity-50 pointer-events-none':        deletingId === {{ $co['id'] }}
                    }"
                    class="rounded-2xl border border-slate-200 bg-white shadow-sm
                           transition-all duration-150 overflow-hidden">

                    {{-- ── READ mode ─────────────────────────────────────────── --}}
                    <div x-show="editingId !== {{ $co['id'] }}" class="flex items-start gap-4 p-4">

                        {{-- CO badge --}}
                        <span class="shrink-0 mt-0.5 inline-flex items-center justify-center
                                     w-11 h-11 rounded-xl
                                     bg-emerald-100 text-emerald-700
                                     text-sm font-bold ring-1 ring-emerald-200">
                            {{ $co['co_code'] }}
                        </span>

                        {{-- Description --}}
                        <p class="flex-1 text-sm text-slate-700 leading-relaxed pt-1.5 min-w-0">
                            {{ $co['description'] }}
                        </p>

                        {{-- Action buttons --}}
                        <div class="shrink-0 flex items-center gap-1 ml-2">

                            {{-- Edit --}}
                            <button type="button"
                                x-on:click="startEdit({{ $co['id'] }}, {{ Js::from($co['description']) }})"
                                x-bind:disabled="deletingId !== null || savingId !== null"
                                title="Edit {{ $co['co_code'] }}"
                                class="p-2 rounded-xl text-slate-400
                                       hover:text-emerald-700 hover:bg-emerald-50
                                       disabled:opacity-40 transition-colors">
                                <i class="bx bx-edit-alt text-base leading-none"></i>
                            </button>

                            {{-- Delete: shows trash normally, spinner while deleting --}}
                            <button type="button"
                                x-on:click="confirmDelete({{ $co['id'] }}, '{{ $co['co_code'] }}')"
                                x-bind:disabled="deletingId !== null || savingId !== null || editingId !== null"
                                title="Delete {{ $co['co_code'] }}"
                                class="p-2 rounded-xl transition-colors disabled:opacity-40">
                                <i x-show="deletingId !== {{ $co['id'] }}"
                                   class="bx bx-trash text-base leading-none
                                          text-slate-400 hover:text-rose-600"></i>
                                <svg x-show="deletingId === {{ $co['id'] }}" x-cloak
                                     class="animate-spin h-4 w-4 text-rose-400"
                                     viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- ── EDIT mode ─────────────────────────────────────────── --}}
                    <div x-show="editingId === {{ $co['id'] }}" x-cloak
                         class="p-4 bg-emerald-50/40 border-t border-emerald-100 space-y-3">

                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">
                                Editing {{ $co['co_code'] }}
                            </span>
                        </div>

                        <textarea
                            id="edit-ta-{{ $co['id'] }}"
                            x-model="editDraft"
                            rows="3"
                            placeholder="Describe what students will be able to do…"
                            class="w-full resize-none rounded-xl border border-emerald-200 bg-white
                                   px-3 py-2.5 text-sm text-slate-800
                                   placeholder:text-slate-300
                                   focus:border-emerald-400 focus:ring-2 focus:ring-emerald-300/50
                                   focus:outline-none transition-colors"></textarea>

                        <div class="flex items-center gap-2 justify-end">

                            {{-- Cancel --}}
                            <button type="button"
                                x-on:click="cancelEdit()"
                                x-bind:disabled="savingId === {{ $co['id'] }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs
                                       font-semibold rounded-lg border border-slate-200
                                       bg-white text-slate-600
                                       hover:bg-slate-50 disabled:opacity-50
                                       transition-colors">
                                <i class="bx bx-x leading-none"></i> Cancel
                            </button>

                            {{-- Save --}}
                            <button type="button"
                                x-on:click="saveEdit({{ $co['id'] }})"
                                x-bind:disabled="!editDraft.trim() || savingId === {{ $co['id'] }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs
                                       font-semibold rounded-lg
                                       bg-emerald-600 text-white
                                       hover:bg-emerald-700
                                       disabled:opacity-50 disabled:pointer-events-none
                                       transition-colors">

                                {{-- Not saving --}}
                                <span x-show="savingId !== {{ $co['id'] }}"
                                      class="inline-flex items-center gap-1.5">
                                    <i class="bx bx-save leading-none"></i> Save
                                </span>

                                {{-- Saving spinner --}}
                                <span x-show="savingId === {{ $co['id'] }}" x-cloak
                                      class="inline-flex items-center gap-1.5">
                                    <svg class="animate-spin h-3.5 w-3.5 shrink-0"
                                         viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Saving…
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

            @empty
                {{-- ─── Empty state ─────────────────────────────────────────── --}}
                <div class="flex flex-col items-center gap-3 py-14 text-center
                            rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50">
                    <span class="flex items-center justify-center w-14 h-14
                                 rounded-2xl bg-emerald-100 text-emerald-600">
                        <i class="bx bx-book-open text-2xl"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">No Course Outcomes yet</p>
                        <p class="text-xs text-slate-400 mt-1">
                            Click <strong class="text-emerald-700">Add Course Outcome</strong> below to get started.
                        </p>
                    </div>
                </div>
            @endforelse

            {{-- ─── Add form / Add button ──────────────────────────────────── --}}

            {{-- Add form (shown when addingNew = true) --}}
            <div x-show="addingNew" x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="rounded-2xl border-2 border-emerald-300 bg-emerald-50/40
                        shadow-sm overflow-hidden">

                <div class="px-4 pt-4 pb-1 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                 bg-emerald-100 text-emerald-700">
                        <i class="bx bx-plus text-base"></i>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-700">
                        New Course Outcome — CO{{ count($outcomes) + 1 }}
                    </span>
                </div>

                <div class="p-4 space-y-3">
                    <textarea
                        id="new-co-ta"
                        x-model="newDraft"
                        rows="3"
                        placeholder="Describe what students will be able to do after completing this course…"
                        x-on:keydown.escape="cancelAdd()"
                        class="w-full resize-none rounded-xl border border-emerald-200 bg-white
                               px-3 py-2.5 text-sm text-slate-800
                               placeholder:text-slate-300
                               focus:border-emerald-400 focus:ring-2 focus:ring-emerald-300/50
                               focus:outline-none transition-colors"></textarea>

                    <div class="flex items-center gap-2 justify-between">
                        <p class="text-[11px] text-slate-400">
                            <i class="bx bx-info-circle"></i>
                            Press <kbd class="px-1 py-0.5 rounded border border-slate-200 bg-white text-[10px]">Esc</kbd> to cancel
                        </p>
                        <div class="flex items-center gap-2">
                            <button type="button"
                                x-on:click="cancelAdd()"
                                x-bind:disabled="savingId === 'new'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs
                                       font-semibold rounded-lg border border-slate-200
                                       bg-white text-slate-600
                                       hover:bg-slate-50 disabled:opacity-50
                                       transition-colors">
                                <i class="bx bx-x leading-none"></i> Cancel
                            </button>

                            <button type="button"
                                x-on:click="saveNew()"
                                x-bind:disabled="!newDraft.trim() || savingId === 'new'"
                                class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs
                                       font-semibold rounded-lg
                                       bg-emerald-600 text-white
                                       hover:bg-emerald-700
                                       disabled:opacity-50 disabled:pointer-events-none
                                       transition-colors">
                                <span x-show="savingId !== 'new'"
                                      class="inline-flex items-center gap-1.5">
                                    <i class="bx bx-save leading-none"></i> Save Outcome
                                </span>
                                <span x-show="savingId === 'new'" x-cloak
                                      class="inline-flex items-center gap-1.5">
                                    <svg class="animate-spin h-3.5 w-3.5 shrink-0"
                                         viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Saving…
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Add button (shown when addingNew = false) --}}
            <button type="button"
                x-show="!addingNew"
                x-on:click="openAdd()"
                x-bind:disabled="deletingId !== null || savingId !== null"
                class="flex w-full items-center justify-center gap-2 px-4 py-3.5
                       border-2 border-dashed border-emerald-300 rounded-2xl
                       text-sm font-semibold text-emerald-700
                       hover:border-emerald-500 hover:bg-emerald-50
                       disabled:opacity-40 disabled:pointer-events-none
                       transition-colors duration-150">
                <i class="bx bx-plus text-lg leading-none"></i>
                Add Course Outcome
            </button>

    </div>

    {{-- ══ Program Outcomes Reference ══════════════════════════════════════════ --}}
    @include('livewire.syllabus.steps.outcome-partials.po-reference')

</div>
