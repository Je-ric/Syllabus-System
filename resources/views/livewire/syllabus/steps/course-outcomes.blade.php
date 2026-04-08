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
            this.addingNew = false;
            this.$nextTick(() => {
                const el = document.getElementById('edit-ta-' + id);
                if (el) { el.focus(); el.setSelectionRange(el.value.length, el.value.length); }
            });
        },
        cancelEdit() { this.editingId = null; this.editDraft = ''; },
        async saveEdit(id) {
            const text = this.editDraft.trim();
            if (!text) return;
            this.savingId = id;
            await $wire.updateOutcome(id, text);
        },
        openAdd() {
            this.addingNew = true;
            this.editingId = null;
            this.$nextTick(() => {
                const el = document.getElementById('new-co-ta');
                if (el) el.focus();
            });
        },
        cancelAdd() { this.addingNew = false; this.newDraft = ''; },
        async saveNew() {
            const text = this.newDraft.trim();
            if (!text) return;
            this.savingId = 'new';
            await $wire.createOutcome(text);
        },
        async confirmDelete(id, coCode) {
            if (!confirm('Delete ' + coCode + '? This cannot be undone.')) return;
            this.deletingId = id;
            await $wire.deleteOutcome(id);
        }
    }"
    x-on:co-saved.window="editingId = null; editDraft = ''; addingNew = false; newDraft = ''; savingId = null;"
    x-on:co-deleted.window="deletingId = null"
    x-on:co-delete-failed.window="deletingId = null"
    class="space-y-5">

    {{-- Step header --}}
    <x-wizard.step-header
        title="Course Outcomes"
        icon="book-open"
        description="Define what students should be able to do after completing this course. Each outcome is saved individually." />

    {{-- CO cards --}}
    <div class="space-y-2">

        @forelse ($outcomes as $co)
            <div wire:key="co-{{ $co['id'] }}"
                x-bind:class="{
                    'ring-2 ring-[#16a34a] ring-offset-1': editingId === {{ $co['id'] }},
                    'opacity-50 pointer-events-none':      deletingId === {{ $co['id'] }}
                }"
                class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden transition-all duration-150"
                style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

                {{-- READ mode --}}
                <div x-show="editingId !== {{ $co['id'] }}"
                     class="flex items-start gap-4 p-4 border-l-[3px] border-[#16a34a]">

                    <span class="shrink-0 mt-0.5 inline-flex items-center justify-center
                                 w-10 h-10 rounded-xl bg-[#16a34a] text-white
                                 text-[13px] font-bold">
                        {{ $co['co_code'] }}
                    </span>

                    <p class="flex-1 text-[13px] text-[#0f172a] leading-relaxed pt-1 min-w-0">
                        {{ $co['description'] }}
                    </p>

                    <div class="shrink-0 flex items-center gap-1 ml-2">
                        <button type="button"
                            x-on:click="startEdit({{ $co['id'] }}, {{ Js::from($co['description']) }})"
                            x-bind:disabled="deletingId !== null || savingId !== null"
                            title="Edit {{ $co['co_code'] }}"
                            class="p-2 rounded-lg text-[#94a3b8] hover:text-[#16a34a] hover:bg-[#f0fdf4]
                                   disabled:opacity-40 transition-colors">
                            <i class="bx bx-edit-alt text-base leading-none"></i>
                        </button>
                        <button type="button"
                            x-on:click="confirmDelete({{ $co['id'] }}, '{{ $co['co_code'] }}')"
                            x-bind:disabled="deletingId !== null || savingId !== null || editingId !== null"
                            title="Delete {{ $co['co_code'] }}"
                            class="p-2 rounded-lg transition-colors disabled:opacity-40">
                            <i x-show="deletingId !== {{ $co['id'] }}"
                               class="bx bx-trash text-base leading-none text-[#94a3b8] hover:text-rose-600"></i>
                            <svg x-show="deletingId === {{ $co['id'] }}" x-cloak
                                 class="animate-spin h-4 w-4 text-rose-400" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- EDIT mode --}}
                <div x-show="editingId === {{ $co['id'] }}" x-cloak
                     class="p-4 bg-[#f0fdf4]/40 border-t border-[#bbf7d0] space-y-3">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#166534]">
                        Editing {{ $co['co_code'] }}
                    </p>
                    <textarea
                        id="edit-ta-{{ $co['id'] }}"
                        x-model="editDraft"
                        rows="3"
                        placeholder="Describe what students will be able to do…"
                        class="w-full resize-none rounded-xl border border-[#bbf7d0] bg-white
                               px-3 py-2.5 text-[13px] text-[#0f172a]
                               placeholder:text-[#94a3b8] focus:border-[#16a34a] focus:outline-none transition-colors"
                        style="box-shadow:none"
                        onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                        onblur="this.style.boxShadow='none'"></textarea>
                    <div class="flex items-center gap-2 justify-end">
                        <x-button type="button" variant="cancel"
                            x-on:click="cancelEdit()"
                            x-bind:disabled="savingId === {{ $co['id'] }}">
                            <i class="bx bx-x"></i> Cancel
                        </x-button>
                        <x-button type="button" variant="add-button"
                            x-on:click="saveEdit({{ $co['id'] }})"
                            x-bind:disabled="!editDraft.trim() || savingId === {{ $co['id'] }}">
                            <span x-show="savingId !== {{ $co['id'] }}" class="inline-flex items-center gap-1.5">
                                <i class="bx bx-save"></i> Save
                            </span>
                            <span x-show="savingId === {{ $co['id'] }}" x-cloak class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Saving…
                            </span>
                        </x-button>
                    </div>
                </div>
            </div>

        @empty
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
        @endforelse

        {{-- Add form --}}
        <div x-show="addingNew" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="rounded-xl border-2 border-[#bbf7d0] bg-[#f0fdf4]/40 overflow-hidden">

            <div class="px-4 pt-4 pb-1 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#dcfce7] text-[#16a34a]">
                    <i class="bx bx-plus text-base"></i>
                </span>
                <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#166534]">
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
                    class="w-full resize-none rounded-xl border border-[#bbf7d0] bg-white
                           px-3 py-2.5 text-[13px] text-[#0f172a]
                           placeholder:text-[#94a3b8] focus:border-[#16a34a] focus:outline-none transition-colors"
                    style="box-shadow:none"
                    onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                    onblur="this.style.boxShadow='none'"></textarea>

                <div class="flex items-center gap-2 justify-between">
                    <p class="text-[13px] text-[#94a3b8]">
                        <i class="bx bx-info-circle"></i>
                        Press <kbd class="px-1 py-0.5 rounded border border-[#e2e8f0] bg-white text-[11px]">Esc</kbd> to cancel
                    </p>
                    <div class="flex items-center gap-2">
                        <x-button type="button" variant="cancel"
                            x-on:click="cancelAdd()"
                            x-bind:disabled="savingId === 'new'">
                            <i class="bx bx-x"></i> Cancel
                        </x-button>
                        <x-button type="button" variant="add-button"
                            x-on:click="saveNew()"
                            x-bind:disabled="!newDraft.trim() || savingId === 'new'">
                            <span x-show="savingId !== 'new'" class="inline-flex items-center gap-1.5">
                                <i class="bx bx-save"></i> Save Outcome
                            </span>
                            <span x-show="savingId === 'new'" x-cloak class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Saving…
                            </span>
                        </x-button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add button --}}
        <x-button type="button" variant="add-dashed"
            x-show="!addingNew"
            x-on:click="openAdd()"
            x-bind:disabled="deletingId !== null || savingId !== null"
            class="w-full">
            <i class="bx bx-plus text-lg"></i>
            Add Course Outcome
        </x-button>

    </div>

    {{-- Program Outcomes Reference --}}
    @include('livewire.syllabus.steps.outcome-partials.po-reference')

</div>
