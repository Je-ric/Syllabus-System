{{--
    REVISION HISTORY ACCORDION
    ─────────────────────────────────────────────────────────────────────────
    PERFORMANCE: Pure Alpine x-model — zero wire round-trips while typing.
    Single $wire.saveRevision(...) on submit. Edit loads form via window
    event — pure Alpine, zero server call.

    Loading indicators:
    • Save/Update  — Alpine saving=true while awaiting $wire.saveRevision
    • Delete       — Alpine deletingId=N while awaiting $wire.removeRevision

    BUG NOTE: <template x-if> inside a <button> causes double-rendering and
    text-disappearing issues because Alpine reuses DOM nodes. All button
    content uses x-show on plain <span> elements instead — this is the
    correct, stable approach.

    Revision numbering:
    • User enters any non-negative integer (0-based by convention).
    • "Resequence" button renumbers all saved rows 0, 1, 2, … in order.
    ─────────────────────────────────────────────────────────────────────────
--}}
<div
    x-data="{ open: true }"
    x-on:revision-load-form.window="open = true"
    class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

    {{-- ── Header ── --}}
    <button type="button" x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left
               hover:bg-slate-50/80 transition-colors">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg
                         bg-amber-100 text-amber-600">
                <i class="bx bx-history text-lg leading-none"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-800">Revision History</p>
                <p class="text-xs text-slate-400 mt-0.5">
                    @if (count($revisions) > 0)
                        {{ count($revisions) }} {{ Str::plural('entry', count($revisions)) }} saved
                    @else
                        No entries yet — use the form to add the first one
                    @endif
                </p>
            </div>
        </div>
        <i class="bx text-slate-400 text-xl transition-transform duration-200"
           x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
    </button>

    {{-- ── Body ── --}}
    <div x-show="open" x-collapse>
        <div class="border-t border-amber-100/60 p-5">

            {{--
                x-data owns form state + delete state.
                'revision-saved'        → server save finished → reset form
                'revision-deleted'      → server delete finished → clear deletingId
                'revision-delete-failed'→ server delete failed  → clear deletingId
            --}}
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

                    loadForm(e) {
                        this.editingId    = e.detail.id;
                        this.revisionNo   = e.detail.revision_no;
                        this.date         = e.detail.revision_date;
                        this.semester     = e.detail.implementation_semester ?? '';
                        this.highlights   = e.detail.highlights ?? '';
                        this.contributors = e.detail.contributors ?? '';
                    },

                    reset() {
                        this.editingId    = null;
                        this.revisionNo   = '';
                        this.date         = new Date().toISOString().slice(0, 10);
                        this.semester     = '';
                        this.highlights   = '';
                        this.contributors = '';
                        this.saving       = false;
                    },

                    async submit() {
                        const revNo = parseInt(this.revisionNo, 10);
                        if (!this.date || !this.semester.trim()) return;
                        if (isNaN(revNo) || revNo < 0) return;
                        this.saving = true;
                        await $wire.saveRevision(
                            this.editingId,
                            revNo,
                            this.date,
                            this.semester,
                            this.highlights,
                            this.contributors
                        );
                        // saving is reset by the reset() call on 'revision-saved'
                        // but also clear here in case of validation error toast
                        this.saving = false;
                    },

                    async deleteRevision(id) {
                        this.deletingId = id;
                        await $wire.removeRevision(id);
                    }
                }"
                x-on:revision-load-form.window="loadForm($event)"
                x-on:revision-saved.window="reset()"
                x-on:revision-deleted.window="deletingId = null"
                x-on:revision-delete-failed.window="deletingId = null"
                class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">

                {{-- ════════════════════════════════════
                     LEFT — add / edit form
                     ════════════════════════════════════ --}}
                <div>
                    {{-- Form title + cancel --}}
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <span x-show="!editingId"
                                  class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                New Entry
                            </span>
                            <span x-show="editingId" x-cloak
                                  class="inline-flex items-center gap-1.5 text-[10px] font-bold
                                         uppercase tracking-widest text-amber-600">
                                <i class="bx bx-edit-alt text-xs leading-none"></i>
                                Editing Rev.&nbsp;<span x-text="revisionNo"></span>
                            </span>
                        </div>
                        <button type="button"
                            x-show="editingId" x-cloak
                            x-on:click="reset()"
                            class="text-xs text-slate-400 hover:text-slate-700
                                   underline underline-offset-2 transition-colors">
                            Cancel
                        </button>
                    </div>

                    {{-- Form card --}}
                    <div class="rounded-xl border p-4 space-y-3.5 transition-colors duration-150"
                         x-bind:class="editingId
                            ? 'border-amber-200 bg-amber-50/60'
                            : 'border-slate-200 bg-slate-50/60'">

                        {{-- Date + Rev No row --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-form.label for="draft-date" :isRequired="true">Date</x-form.label>
                                <x-form.input
                                    id="draft-date"
                                    type="date"
                                    x-model="date"
                                    class="text-xs" />
                            </div>
                            <div>
                                <x-form.label for="draft-rev-no">
                                    Rev. No. <span class="text-rose-400">*</span>
                                </x-form.label>
                                <x-form.input
                                    id="draft-rev-no"
                                    type="number"
                                    min="0"
                                    step="1"
                                    placeholder="0"
                                    x-model="revisionNo"
                                    class="text-xs" />
                                <p class="text-[10px] text-slate-400 mt-1 leading-snug">
                                    Whole number ≥ 0. Use Resequence to auto-renumber.
                                </p>
                            </div>
                        </div>

                        {{-- Semester --}}
                        <div>
                            <x-form.label for="draft-semester" :isRequired="true">
                                Implementation Semester
                            </x-form.label>
                            <x-form.input
                                id="draft-semester"
                                type="text"
                                x-model="semester"
                                placeholder="e.g. 1st Sem 2025–2026"
                                class="text-xs" />
                            <p x-show="!semester.trim() && saving" x-cloak
                               class="text-[11px] text-rose-500 mt-1">
                                This field is required.
                            </p>
                        </div>

                        {{-- Highlights --}}
                        <div>
                            <x-form.label for="draft-highlights">Highlights</x-form.label>
                            <x-form.textarea
                                id="draft-highlights"
                                x-model="highlights"
                                rows="2"
                                placeholder="Brief summary of changes…"
                                class="text-xs resize-none" />
                        </div>

                        {{-- Contributors --}}
                        <div>
                            <x-form.label for="draft-contributors">Contributors</x-form.label>
                            <x-form.textarea
                                id="draft-contributors"
                                x-model="contributors"
                                rows="2"
                                placeholder="Names of contributors…"
                                class="text-xs resize-none" />
                        </div>

                        {{--
                            Submit button.
                            IMPORTANT: do NOT use <template x-if> inside a button —
                            it causes double-rendering and the text disappearing bug.
                            Use x-show on plain <span>s instead.
                        --}}
                        <x-button type="button" variant="add-button"
                            x-on:click="submit()"
                            x-bind:disabled="saving"
                            x-bind:class="editingId
                                ? '!bg-amber-500 hover:!bg-amber-600'
                                : ''">
                            {{-- Not saving: icon + label --}}
                            <span x-show="!saving" class="inline-flex items-center gap-2">
                                <i x-show="editingId"  class="bx bx-save leading-none"></i>
                                <i x-show="!editingId" class="bx bx-plus leading-none"></i>
                                <span x-text="editingId ? 'Update Revision' : 'Add Revision'"></span>
                            </span>
                            {{-- Saving: spinner + label --}}
                            <span x-show="saving" x-cloak class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Saving…
                            </span>
                        </x-button>
                    </div>
                </div>

                {{-- ════════════════════════════════════
                     RIGHT — saved revisions list
                     ════════════════════════════════════ --}}
                <div>
                    {{-- List header with Resequence button --}}
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            Saved Revisions
                        </p>
                        @if (count($revisions) > 1)
                            <button type="button"
                                wire:click="resequenceRevisions"
                                wire:loading.attr="disabled"
                                wire:target="resequenceRevisions"
                                wire:confirm="This will renumber all revisions 0, 1, 2, … in their current order. Continue?"
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg
                                       text-[11px] font-semibold text-slate-500
                                       border border-slate-200 bg-white
                                       hover:bg-slate-50 hover:border-slate-300
                                       disabled:opacity-50 transition-colors">
                                <span wire:loading.remove wire:target="resequenceRevisions"
                                      class="inline-flex items-center gap-1">
                                    <i class="bx bx-sort-a-z leading-none"></i> Resequence
                                </span>
                                <span wire:loading wire:target="resequenceRevisions"
                                      class="inline-flex items-center gap-1">
                                    <svg class="animate-spin h-3 w-3 shrink-0"
                                         viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Renumbering…
                                </span>
                            </button>
                        @endif
                    </div>

                    @if (count($revisions) > 0)
                        <div class="space-y-2 max-h-110 overflow-y-auto pr-0.5">
                            @foreach ($revisions as $rev)
                                <div
                                    wire:key="saved-rev-{{ $rev['id'] }}"
                                    x-bind:class="{
                                        'border-amber-200 bg-amber-50/70':
                                            editingId  === {{ $rev['id'] }},
                                        'border-rose-100 bg-rose-50/50 opacity-50 pointer-events-none':
                                            deletingId === {{ $rev['id'] }},
                                        'border-slate-200 bg-white':
                                            editingId  !== {{ $rev['id'] }} &&
                                            deletingId !== {{ $rev['id'] }}
                                    }"
                                    class="rounded-xl border px-3.5 py-3 transition-all duration-150">

                                    <div class="flex items-start justify-between gap-2">

                                        {{-- Content --}}
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs font-bold text-slate-700 shrink-0">
                                                    Rev.&nbsp;#{{ $rev['revision_no'] }}
                                                </span>
                                                <span class="text-[11px] text-slate-400">
                                                    {{ $rev['revision_date'] }}
                                                </span>
                                                @if ($rev['implementation_semester'])
                                                    <x-feedback-status.status-indicator
                                                        variant="emerald" :dot="true">
                                                        {{ $rev['implementation_semester'] }}
                                                    </x-feedback-status.status-indicator>
                                                @endif
                                            </div>
                                            @if ($rev['highlights'])
                                                <p class="text-[11px] text-slate-500 mt-1.5
                                                          leading-relaxed line-clamp-2">
                                                    {{ $rev['highlights'] }}
                                                </p>
                                            @endif
                                            @if ($rev['contributors'])
                                                <p class="text-[10px] text-slate-400 mt-1">
                                                    <i class="bx bx-user text-[11px]"></i>
                                                    {{ $rev['contributors'] }}
                                                </p>
                                            @endif
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-0.5 shrink-0 ml-1">

                                            {{-- Edit — pure Alpine, zero server call --}}
                                            <button type="button"
                                                x-on:click="$dispatch('revision-load-form', @js($rev))"
                                                x-bind:disabled="deletingId !== null || saving"
                                                title="Edit"
                                                class="p-1.5 rounded-lg text-slate-400
                                                       hover:text-amber-600 hover:bg-amber-50
                                                       disabled:opacity-40 transition-colors">
                                                <i class="bx bx-edit-alt text-sm leading-none"></i>
                                            </button>

                                            {{--
                                                Delete — Alpine spinner (same x-show pattern, NOT
                                                <template x-if>, to avoid the double-render bug)
                                            --}}
                                            <button type="button"
                                                x-on:click="deleteRevision({{ $rev['id'] }})"
                                                x-bind:disabled="deletingId !== null || saving"
                                                title="Delete"
                                                class="p-1.5 rounded-lg transition-colors
                                                       disabled:opacity-40">
                                                <i x-show="deletingId !== {{ $rev['id'] }}"
                                                   class="bx bx-trash text-sm leading-none
                                                          text-slate-400 hover:text-rose-600"></i>
                                                <svg x-show="deletingId === {{ $rev['id'] }}"
                                                     x-cloak
                                                     class="animate-spin h-3.5 w-3.5 text-rose-400"
                                                     viewBox="0 0 24 24" fill="none">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"/>
                                                    <path class="opacity-75" fill="currentColor"
                                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Resequence hint --}}
                        @if (count($revisions) > 1)
                            <p class="text-[10px] text-slate-400 mt-2 leading-relaxed">
                                <i class="bx bx-info-circle text-[11px]"></i>
                                To insert a revision between existing ones, set its number manually,
                                then click <strong>Resequence</strong> to re-sort and renumber all rows.
                            </p>
                        @endif

                    @else
                        <x-empty-state
                            icon="bx-history"
                            title="No revisions yet"
                            message="Use the form on the left to record your first revision entry." />
                    @endif
                </div>

            </div>{{-- /grid --}}
        </div>
    </div>
</div>
