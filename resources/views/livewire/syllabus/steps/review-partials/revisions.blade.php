{{--
    REVISION HISTORY ACCORDION
    ─────────────────────────────────────────────────────────────────────────
    Layout: single add/edit form (LEFT) | saved revisions list (RIGHT)
    PERFORMANCE: Form is pure Alpine x-model — ZERO wire round-trips while
    typing. Single $wire.saveRevision(...) call on submit passes all values
    as method arguments. Edit button loads data via window event (no server).
    ─────────────────────────────────────────────────────────────────────────
--}}
<div x-data="{ open: true }"
     x-on:revision-load-form.window="open = true"
     class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

    {{-- Header --}}
    <button type="button" x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-slate-50 transition-colors">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-600">
                <i class="bx bx-history text-lg"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-800">Revision History</p>
                <p class="text-xs text-slate-500">
                    {{ count($revisions) }}
                    {{ \Illuminate\Support\Str::plural('revision', count($revisions)) }} saved
                </p>
            </div>
        </div>
        <i class="bx text-slate-400 text-xl transition-transform duration-200"
           x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
    </button>

    {{-- Body --}}
    {{--
        PERFORMANCE: The form fields are pure Alpine x-model — ZERO wire round-trips
        while typing. Only the submit button calls $wire.saveRevision(...) with all
        values passed as arguments at once. This way Livewire never touches the DOM
        until the user explicitly clicks Add/Update.

        When a revision is edited, the 'revision-load-form' window event carries the
        row data so Alpine can populate its local state instantly (no server round-trip).
    --}}
    <div x-show="open" x-collapse>
        <div class="border-t border-slate-100 p-5">
            <div
                x-data="{
                    editingId:    null,
                    revisionNo:   {{ $nextRevisionNo }},
                    date:         '{{ now()->format('Y-m-d') }}',
                    semester:     '',
                    highlights:   '',
                    contributors: '',
                    saving:       false,

                    loadForm(e) {
                        this.editingId    = e.detail.id;
                        this.revisionNo   = e.detail.revision_no;
                        this.date         = e.detail.revision_date;
                        this.semester     = e.detail.implementation_semester ?? '';
                        this.highlights   = e.detail.highlights ?? '';
                        this.contributors = e.detail.contributors ?? '';
                        // accordion open is handled by the outer x-data listener
                    },

                    reset(nextNo) {
                        this.editingId    = null;
                        this.revisionNo   = nextNo ?? 0;
                        this.date         = new Date().toISOString().slice(0,10);
                        this.semester     = '';
                        this.highlights   = '';
                        this.contributors = '';
                        this.saving       = false;
                    },

                    async submit() {
                        if (!this.date || !this.semester.trim()) return;
                        this.saving = true;
                        await $wire.saveRevision(
                            this.editingId,
                            this.revisionNo,
                            this.date,
                            this.semester,
                            this.highlights,
                            this.contributors
                        );
                        this.saving = false;
                    }
                }"
                x-on:revision-load-form.window="loadForm($event)"
                x-on:revision-form-reset.window="reset($event.detail.nextNo)"
                class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- ══ LEFT: single add / edit form ══ --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                            <span x-show="!editingId">Add New Revision</span>
                            <span x-show="editingId" x-cloak class="text-amber-600 flex items-center gap-1">
                                <i class="bx bx-edit-alt"></i>
                                Editing Rev. #<span x-text="revisionNo"></span>
                            </span>
                        </p>
                        <button type="button" x-show="editingId" x-cloak
                            x-on:click="reset({{ count($revisions) }})"
                            class="text-xs text-slate-400 hover:text-slate-600 underline transition-colors">
                            Cancel
                        </button>
                    </div>

                    <div class="rounded-xl border p-4 space-y-3 transition-colors"
                         x-bind:class="editingId ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-slate-50'">

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Date *</label>
                                <input type="date"
                                    x-model="date"
                                    class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                           px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                           focus:ring-emerald-300 focus:outline-none" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Rev. No.</label>
                                <input type="number"
                                    x-model="revisionNo"
                                    readonly
                                    class="w-full text-xs rounded-lg border border-slate-200 bg-slate-100
                                           px-2.5 py-1.5 text-slate-500 cursor-not-allowed" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">
                                Implementation Semester *
                            </label>
                            <input type="text"
                                x-model="semester"
                                placeholder="e.g., 1st Sem 2025-2026"
                                class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                       px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                       focus:ring-emerald-300 focus:outline-none placeholder:text-slate-300" />
                            <p x-show="!semester.trim() && saving"
                               class="text-xs text-rose-500 mt-1">
                                Implementation Semester is required.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Highlights</label>
                            <textarea x-model="highlights" rows="2"
                                placeholder="Brief summary of changes…"
                                class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                       px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                       focus:ring-emerald-300 focus:outline-none resize-none
                                       placeholder:text-slate-300"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Contributors</label>
                            <textarea x-model="contributors" rows="2"
                                placeholder="Names of contributors…"
                                class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                       px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                       focus:ring-emerald-300 focus:outline-none resize-none
                                       placeholder:text-slate-300"></textarea>
                        </div>

                        <button type="button"
                            x-on:click="submit"
                            x-bind:disabled="saving"
                            x-bind:class="editingId
                                ? 'bg-amber-500 hover:bg-amber-600 text-white'
                                : 'bg-emerald-600 hover:bg-emerald-700 text-white'"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                                   rounded-xl text-sm font-semibold transition-colors
                                   disabled:opacity-60">
                            <template x-if="!saving">
                                <span class="inline-flex items-center gap-2">
                                    <i x-show="editingId" class="bx bx-save leading-none"></i>
                                    <i x-show="!editingId" class="bx bx-plus leading-none"></i>
                                    <span x-text="editingId ? 'Update Revision' : 'Add Revision'"></span>
                                </span>
                            </template>
                            <template x-if="saving">
                                <span class="inline-flex items-center gap-2">
                                    <i class="bx bx-loader-alt bx-spin leading-none"></i>
                                    Saving…
                                </span>
                            </template>
                        </button>
                    </div>
                </div>

                {{-- ══ RIGHT: saved revisions list ══ --}}
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">
                        Saved Revisions
                    </p>

                    @if (count($revisions) > 0)
                        <div class="space-y-2 max-h-[420px] overflow-y-auto pr-1">
                            @foreach ($revisions as $rev)
                                <div class="rounded-xl border p-3 transition-colors"
                                     x-bind:class="editingId === {{ $rev['id'] }} ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-white'"
                                     wire:key="saved-rev-{{ $rev['id'] }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <span class="text-xs font-bold text-slate-700">
                                                    Rev. #{{ $rev['revision_no'] }}
                                                </span>
                                                <span class="text-xs text-slate-400">
                                                    {{ $rev['revision_date'] }}
                                                </span>
                                            </div>
                                            @if ($rev['implementation_semester'])
                                                <p class="text-xs text-emerald-700 font-medium">
                                                    {{ $rev['implementation_semester'] }}
                                                </p>
                                            @endif
                                            @if ($rev['highlights'])
                                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                                    {{ $rev['highlights'] }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            {{-- Edit: pure Alpine — loads all fields instantly, zero server round-trip --}}
                                            <button type="button"
                                                x-on:click="$dispatch('revision-load-form', @js($rev))"
                                                title="Edit"
                                                class="p-1 rounded text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                                                <i class="bx bx-edit-alt text-sm"></i>
                                            </button>
                                            <button type="button"
                                                wire:click="removeRevision({{ $rev['id'] }})"
                                                wire:confirm="Remove this revision?"
                                                title="Delete"
                                                class="p-1 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                                <i class="bx bx-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center">
                            <i class="bx bx-history text-3xl text-slate-300"></i>
                            <p class="text-xs text-slate-400 mt-2">No revisions saved yet.</p>
                            <p class="text-xs text-slate-300 mt-0.5">Use the form to add your first revision.</p>
                        </div>
                    @endif
                </div>

            </div>{{-- /grid --}}
        </div>
    </div>
</div>
