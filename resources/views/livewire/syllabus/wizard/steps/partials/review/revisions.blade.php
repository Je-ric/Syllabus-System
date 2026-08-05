{{--
    REVISION HISTORY ACCORDION
    Form is in a modal — triggered by "Add Revision" or edit icon.
    Saved list stays in the accordion as a clean read-only list.
--}}
<div
    x-data="{ open: true }"
    class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden"
    style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

    {{-- ── Header ── --}}
    <button type="button" x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left
               hover:bg-[#f8fafc] transition-colors focus:outline-none">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#e2e8f0] text-[#475569]">
                <i class="bx bx-history text-base leading-none"></i>
            </span>
            <div>
                <p class="text-sm font-bold text-[#0f172a]">Revision History</p>
                <p class="text-xs text-[#94a3b8] mt-0.5">
                    @if (count($revisions) > 0)
                        {{ count($revisions) }} {{ Str::plural('entry', count($revisions)) }} saved
                    @else
                        No entries yet
                    @endif
                </p>
            </div>
        </div>
        <i class="bx text-[#94a3b8] text-lg transition-transform duration-200"
           x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
    </button>

    {{-- ── Body ── --}}
    <div x-show="open" x-collapse>
        <div
            x-data="{
                editingId:    null,
                revisionNo:   '',
                date:         '{{ now()->format('Y-m-d') }}',
                semester:     '',
                highlights:   '',
                contributors: '',
                saving:       false,
                deletingId:   null,

                openNew() {
                    this.editingId    = null;
                    this.revisionNo   = '';
                    this.date         = new Date().toISOString().slice(0, 10);
                    this.semester     = '';
                    this.highlights   = '';
                    this.contributors = '';
                    this.saving       = false;
                    document.getElementById('revision-modal').showModal();
                },

                openEdit(rev) {
                    this.editingId    = rev.id;
                    this.revisionNo   = rev.revision_no;
                    this.date         = rev.revision_date;
                    this.semester     = rev.implementation_semester ?? '';
                    this.highlights   = rev.highlights ?? '';
                    this.contributors = rev.contributors ?? '';
                    this.saving       = false;
                    document.getElementById('revision-modal').showModal();
                },

                async submit() {
                    const revNo = parseInt(this.revisionNo, 10);
                    if (!this.date || !this.semester.trim() || isNaN(revNo) || revNo < 0) return;
                    this.saving = true;
                    await $wire.saveRevision(
                        this.editingId,
                        revNo,
                        this.date,
                        this.semester,
                        this.highlights,
                        this.contributors
                    );
                    this.saving = false;
                },

                async deleteRevision(id) {
                    this.deletingId = id;
                    await $wire.removeRevision(id);
                }
            }"
            x-on:revision-saved.window="saving = false; document.getElementById('revision-modal')?.close()"
            x-on:revision-deleted.window="deletingId = null"
            x-on:revision-delete-failed.window="deletingId = null"
            class="border-t border-[#e2e8f0] p-5">

            {{-- ── Revision add/edit modal ── --}}
            <x-modal.dialog id="revision-modal" maxWidth="max-w-md" variant="edit">
                <x-modal.header modalId="revision-modal" variant="edit">
                    <span x-text="editingId ? 'Edit Revision' : 'Add Revision'"></span>
                </x-modal.header>

                <x-modal.body class="space-y-3.5">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-form.label for="modal-date" :isRequired="true">Date</x-form.label>
                            <x-form.input id="modal-date" type="date" x-model="date" class="text-xs" />
                        </div>
                        <div>
                            <x-form.label for="modal-rev-no" :isRequired="true">Rev. No.</x-form.label>
                            <x-form.input id="modal-rev-no" type="number" min="0" step="1" placeholder="0" x-model="revisionNo" class="text-xs" />
                            <p class="text-xs text-slate-400 mt-1 leading-snug">Whole number ≥ 0.</p>
                        </div>
                    </div>

                    <div>
                        <x-form.label for="modal-semester" :isRequired="true">Implementation Semester</x-form.label>
                        <x-form.input id="modal-semester" type="text" x-model="semester" placeholder="e.g. 1st Sem 2025–2026" class="text-xs" />
                        <p x-show="!semester.trim() && saving" x-cloak class="text-xs text-rose-500 mt-1">This field is required.</p>
                    </div>

                    <div>
                        <x-form.label for="modal-highlights">Highlights</x-form.label>
                        <x-form.textarea id="modal-highlights" x-model="highlights" rows="2" placeholder="Brief summary of changes…" class="text-xs resize-none" />
                    </div>

                    <div>
                        <x-form.label for="modal-contributors">Contributors</x-form.label>
                        <x-form.textarea id="modal-contributors" x-model="contributors" rows="2" placeholder="Names of contributors…" class="text-xs resize-none" />
                    </div>
                </x-modal.body>

                <x-modal.footer>
                    <x-modal.close-button modalId="revision-modal" text="Cancel" x-bind:disabled="saving" />
                    <x-ui.button type="button" variant="save"
                        x-show="!editingId"
                        x-on:click="submit()"
                        x-bind:disabled="saving"
                        submitting="saving" loadingText="Saving…">
                        <i class="bx bx-plus leading-none"></i> Add Revision
                    </x-ui.button>
                    <x-ui.button type="button" variant="warning"
                        x-show="editingId" x-cloak
                        x-on:click="submit()"
                        x-bind:disabled="saving"
                        submitting="saving" loadingText="Saving…">
                        <i class="bx bx-save leading-none"></i> Update
                    </x-ui.button>
                </x-modal.footer>
            </x-modal.dialog>

            {{-- ── List header + Add button ── --}}
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Saved Revisions</p>
                <div class="flex items-center gap-2">
                    @if (count($revisions) > 1)
                        <button type="button"
                            wire:loading.attr="disabled"
                            wire:target="resequenceRevisions"
                            x-on:click="if (confirm('This will renumber all revisions 0, 1, 2, … in their current order. Continue?')) $wire.resequenceRevisions()"
                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold text-slate-500
                                   border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-50 transition-colors">
                            <span class="inline-flex items-center justify-center gap-1">
                                <span wire:loading.remove wire:target="resequenceRevisions" class="inline-flex items-center gap-1">
                                    <i class="bx bx-sort-a-z leading-none"></i> Resequence
                                </span>
                                <span wire:loading wire:target="resequenceRevisions" class="inline-flex items-center gap-1">
                                    <svg class="animate-spin h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Renumbering…
                                </span>
                            </span>
                        </button>
                    @endif
                    <x-ui.button type="button" variant="sm-success" x-on:click="openNew()">
                        <i class="bx bx-plus text-sm"></i> Add Revision
                    </x-ui.button>
                </div>
            </div>

            {{-- ── Saved revisions list ── --}}
            @if (count($revisions) > 0)
                <div class="space-y-2">
                    @foreach ($revisions as $rev)
                        <div
                            wire:key="saved-rev-{{ $rev['id'] }}"
                            x-bind:class="{
                                'border-rose-100 bg-rose-50/50 opacity-50 pointer-events-none': deletingId === {{ $rev['id'] }},
                                'border-slate-200 bg-white': deletingId !== {{ $rev['id'] }}
                            }"
                            class="rounded-xl border px-3.5 py-3 transition-all duration-150">

                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-slate-700 shrink-0">
                                            Rev.&nbsp;#{{ $rev['revision_no'] }}
                                        </span>
                                        <span class="text-xs text-slate-400">{{ $rev['revision_date'] }}</span>
                                        @if ($rev['implementation_semester'])
                                            <x-feedback-status.status-indicator variant="emerald" :dot="true">
                                                {{ $rev['implementation_semester'] }}
                                            </x-feedback-status.status-indicator>
                                        @endif
                                    </div>
                                    @if ($rev['highlights'])
                                        <p class="text-xs text-slate-500 mt-1.5 leading-relaxed line-clamp-2">
                                            {{ $rev['highlights'] }}
                                        </p>
                                    @endif
                                    @if ($rev['contributors'])
                                        <p class="text-[10px] text-slate-400 mt-1">
                                            <i class="bx bx-user text-xs"></i> {{ $rev['contributors'] }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-0.5 shrink-0 ml-1">
                                    <button type="button"
                                        x-on:click="openEdit(@js($rev))"
                                        x-bind:disabled="deletingId !== null"
                                        title="Edit"
                                        class="p-1.5 rounded-lg text-[#9d9ea4] hover:text-[#36363b] hover:bg-[#F5F5F6] disabled:opacity-40 transition-colors">
                                        <i class="bx bx-edit-alt text-sm leading-none"></i>
                                    </button>
                                    <button type="button"
                                        x-on:click="deleteRevision({{ $rev['id'] }})"
                                        x-bind:disabled="deletingId !== null"
                                        title="Delete"
                                        class="p-1.5 rounded-lg disabled:opacity-40 transition-colors">
                                        <i x-show="deletingId !== {{ $rev['id'] }}"
                                           class="bx bx-trash text-sm leading-none text-slate-400 hover:text-rose-600"></i>
                                        <svg x-show="deletingId === {{ $rev['id'] }}" x-cloak
                                             class="animate-spin h-3.5 w-3.5 text-rose-400" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (count($revisions) > 1)
                    <p class="text-[10px] text-slate-400 mt-2 leading-relaxed">
                        <i class="bx bx-info-circle text-xs"></i>
                        To insert a revision between existing ones, set its number manually, then click <strong>Resequence</strong>.
                    </p>
                @endif

            @else
                <x-feedback-status.empty-state
                    icon="bx-history"
                    title="No revisions yet"
                    message="Click Add Revision to record your first entry." />
            @endif

        </div>
    </div>
</div>