{{--
    REVISION HISTORY ACCORDION
    ─────────────────────────────────────────────────────────────────────────
    Performance strategy — ZERO round-trips while typing:
      • wire:model.lazy   → syncs only on blur / select-change, not every keystroke
      • Alpine x-model    → local echo of the value for instant display updates
      • dirty flag        → set by @input on Alpine side; cleared on save
      • Auto-save         → closing the accordion while dirty calls saveRevisions()
    ─────────────────────────────────────────────────────────────────────────
--}}
<div
    x-data="{ open: true, dirty: false }"
    x-on:revision-field-changed.window="dirty = true"
    class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

    {{-- ── Header ── --}}
    <button type="button"
        x-on:click="
            if (open && dirty) {
                $wire.saveRevisions();
                dirty = false;
            }
            open = !open
        "
        class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-slate-50 transition-colors">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-600">
                <i class="bx bx-history text-lg"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-800">Revision History</p>
                <p class="text-xs text-slate-500">
                    {{ count($revisions) }} {{ \Illuminate\Support\Str::plural('entry', count($revisions)) }}
                    <span x-show="dirty" x-cloak class="text-amber-500 font-medium ml-1">· unsaved changes</span>
                </p>
            </div>
        </div>
        <i class="bx text-slate-400 text-xl transition-transform duration-200"
           x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
    </button>

    {{-- ── Body ── --}}
    <div x-show="open" x-collapse>
        <div class="border-t border-slate-100 p-5">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- ════════════ LEFT: Editable entries ════════════ --}}
                <div class="space-y-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Revision Entries</p>

                    @foreach ($revisions as $rIdx => $revision)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-3"
                             wire:key="rev-{{ $rIdx }}">

                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-600">
                                    Rev. #{{ $revision['revision_no'] }}
                                </span>
                                @if (count($revisions) > 1)
                                    <button type="button"
                                        wire:click="removeRevision({{ $rIdx }})"
                                        wire:confirm="Remove this revision?"
                                        class="text-rose-400 hover:text-rose-600 transition-colors">
                                        <i class="bx bx-trash text-sm"></i>
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                {{-- Date: wire:model.lazy fires on blur only --}}
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
                                    <input type="date"
                                        wire:model.lazy="revisions.{{ $rIdx }}.revision_date"
                                        x-on:change="$dispatch('revision-field-changed')"
                                        class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                               px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                               focus:ring-emerald-300 focus:outline-none" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Rev. No.</label>
                                    <input type="number"
                                        wire:model="revisions.{{ $rIdx }}.revision_no"
                                        readonly
                                        class="w-full text-xs rounded-lg border border-slate-200 bg-slate-100
                                               px-2.5 py-1.5 text-slate-500 cursor-not-allowed" />
                                </div>
                            </div>

                            {{-- Implementation Semester: lazy (blur only) --}}
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">
                                    Implementation Semester *
                                </label>
                                <input type="text"
                                    wire:model.lazy="revisions.{{ $rIdx }}.implementation_semester"
                                    x-on:input="$dispatch('revision-field-changed')"
                                    placeholder="e.g., 1st Sem 2025-2026"
                                    class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                           px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                           focus:ring-emerald-300 focus:outline-none
                                           placeholder:text-slate-300" />
                            </div>

                            {{-- Highlights: lazy --}}
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Highlights</label>
                                <textarea
                                    wire:model.lazy="revisions.{{ $rIdx }}.highlights"
                                    x-on:input="$dispatch('revision-field-changed')"
                                    rows="2"
                                    placeholder="Brief summary of changes…"
                                    class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                           px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                           focus:ring-emerald-300 focus:outline-none resize-none
                                           placeholder:text-slate-300"></textarea>
                            </div>

                            {{-- Contributors: lazy --}}
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Contributors</label>
                                <textarea
                                    wire:model.lazy="revisions.{{ $rIdx }}.contributors"
                                    x-on:input="$dispatch('revision-field-changed')"
                                    rows="2"
                                    placeholder="Names of contributors…"
                                    class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                           px-2.5 py-1.5 focus:border-emerald-400 focus:ring-1
                                           focus:ring-emerald-300 focus:outline-none resize-none
                                           placeholder:text-slate-300"></textarea>
                            </div>
                        </div>
                    @endforeach

                    {{-- Add row --}}
                    <button type="button"
                        wire:click="addRevision"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                               rounded-xl border-2 border-dashed border-slate-300 text-sm text-slate-500
                               hover:border-slate-400 hover:text-slate-700 transition-colors">
                        <i class="bx bx-plus"></i> Add Revision Entry
                    </button>
                </div>

                {{-- ════════════ RIGHT: Saved list + save button ════════════ --}}
                <div class="space-y-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Saved Revisions</p>

                    @php $saved = array_filter($revisions, fn ($r) => !empty($r['id'])); @endphp

                    @if (count($saved) > 0)
                        <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                            @foreach ($revisions as $rev)
                                @if (!empty($rev['id']))
                                    <div class="rounded-xl border border-slate-200 bg-white p-3">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <p class="text-xs font-semibold text-slate-700">
                                                    Rev. #{{ $rev['revision_no'] }}
                                                    <span class="font-normal text-slate-400 ml-1">
                                                        {{ $rev['revision_date'] }}
                                                    </span>
                                                </p>
                                                @if ($rev['implementation_semester'])
                                                    <p class="text-xs text-emerald-700 mt-0.5">
                                                        {{ $rev['implementation_semester'] }}
                                                    </p>
                                                @endif
                                                @if ($rev['highlights'])
                                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                                        {{ $rev['highlights'] }}
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="shrink-0 inline-flex items-center px-1.5 py-0.5
                                                         rounded text-[10px] font-semibold
                                                         bg-emerald-100 text-emerald-700">
                                                saved
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center">
                            <i class="bx bx-history text-2xl text-slate-300"></i>
                            <p class="text-xs text-slate-400 mt-1">No saved revisions yet.</p>
                            <p class="text-xs text-slate-300 mt-0.5">
                                Fill in the entries and click Save.
                            </p>
                        </div>
                    @endif

                    <button type="button"
                        wire:click="saveRevisions"
                        x-on:click="dirty = false"
                        wire:loading.attr="disabled"
                        wire:target="saveRevisions"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                               rounded-xl bg-slate-700 text-white text-sm font-semibold
                               hover:bg-slate-800 disabled:opacity-60 transition-colors">
                        <span wire:loading.remove wire:target="saveRevisions"
                              class="flex items-center gap-2">
                            <i class="bx bx-save"></i> Save Revisions
                        </span>
                        <span wire:loading wire:target="saveRevisions"
                              class="flex items-center gap-2">
                            <i class="bx bx-loader-alt bx-spin"></i> Saving…
                        </span>
                    </button>
                </div>

            </div>{{-- /grid --}}
        </div>
    </div>
</div>
