{{--
    REVISION HISTORY ACCORDION
    ─────────────────────────────────────────────────────────────────────────
    Layout: single add/edit form (LEFT) | saved revisions list (RIGHT)
    "Add" / "Update" button saves immediately to DB → right list refreshes.
    Edit pencil on a saved row loads it into the left form.
    Revision numbering starts at 0.
    wire:model.lazy on all text inputs → zero round-trips while typing.
    ─────────────────────────────────────────────────────────────────────────
--}}
<div x-data="{ open: true }"
     x-on:revision-edit-started.window="open = true"
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
    <div x-show="open" x-collapse>
        <div class="border-t border-slate-100 p-5">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- ══ LEFT: single add / edit form ══ --}}
                <div>
                    {{-- Form header changes between Add / Edit mode --}}
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                            @if ($editingRevisionId)
                                <span class="text-amber-600">
                                    <i class="bx bx-edit-alt mr-1"></i>Editing Rev. #{{ $draftRevisionNo }}
                                </span>
                            @else
                                Add New Revision
                            @endif
                        </p>
                        @if ($editingRevisionId)
                            <button type="button" wire:click="cancelEdit"
                                class="text-xs text-slate-400 hover:text-slate-600 underline transition-colors">
                                Cancel
                            </button>
                        @endif
                    </div>

                    <div class="rounded-xl border {{ $editingRevisionId ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-slate-50' }} p-4 space-y-3">

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Date *</label>
                                <input type="date"
                                    wire:model.lazy="draftDate"
                                    class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                           px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                           focus:ring-emerald-300 focus:outline-none" />
                                @error('draftDate')
                                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Rev. No.</label>
                                <input type="number"
                                    wire:model="draftRevisionNo"
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
                                wire:model.lazy="draftSemester"
                                placeholder="e.g., 1st Sem 2025-2026"
                                class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                       px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                       focus:ring-emerald-300 focus:outline-none placeholder:text-slate-300" />
                            @error('draftSemester')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Highlights</label>
                            <textarea wire:model.lazy="draftHighlights" rows="2"
                                placeholder="Brief summary of changes…"
                                class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                       px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                       focus:ring-emerald-300 focus:outline-none resize-none
                                       placeholder:text-slate-300"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Contributors</label>
                            <textarea wire:model.lazy="draftContributors" rows="2"
                                placeholder="Names of contributors…"
                                class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                       px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                       focus:ring-emerald-300 focus:outline-none resize-none
                                       placeholder:text-slate-300"></textarea>
                        </div>

                        <button type="button"
                            wire:click="saveRevision"
                            wire:loading.attr="disabled"
                            wire:target="saveRevision"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                                   text-sm font-semibold transition-colors
                                   {{ $editingRevisionId
                                       ? 'bg-amber-500 text-white hover:bg-amber-600'
                                       : 'bg-emerald-600 text-white hover:bg-emerald-700' }}
                                   disabled:opacity-60">
                            <span wire:loading.remove wire:target="saveRevision" class="flex items-center gap-2">
                                @if ($editingRevisionId)
                                    <i class="bx bx-save"></i> Update Revision
                                @else
                                    <i class="bx bx-plus"></i> Add Revision
                                @endif
                            </span>
                            <span wire:loading wire:target="saveRevision" class="flex items-center gap-2">
                                <i class="bx bx-loader-alt bx-spin"></i> Saving…
                            </span>
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
                                <div class="rounded-xl border {{ $editingRevisionId === $rev['id'] ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-white' }} p-3"
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
                                            <button type="button"
                                                wire:click="editRevision({{ $rev['id'] }})"
                                                x-on:click="$dispatch('revision-edit-started')"
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

            </div>
        </div>
    </div>
</div>
