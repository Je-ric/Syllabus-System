{{--
    REVIEW & APPROVAL ACCORDION
    ─────────────────────────────────────────────────────────────────────────
    • approved_by  → dean only (required when set)
    • concurred_by → dean only, nullable, must differ from approved_by
    • Reviewed By  → faculty only; already-added names removed from options
    • Add/Remove reviewer uses Alpine saving/removing flags — wire:loading
      cannot track $parent.* calls, so Alpine owns the loading state.
    • Alpine local mirrors (localApprovedBy / localConcurredBy) give instant
      badge updates without a round-trip; "Set" button persists to DB.
    • Concurred dean select filters out whoever is selected as Approved.
    ─────────────────────────────────────────────────────────────────────────
--}}
<div
    x-data="{
        open: false,
        localApprovedBy:  {{ $approvedBy  ?? 'null' }},
        localConcurredBy: {{ $concurredBy ?? 'null' }},
        addingReviewer:   false,
        removingId:       null,
        selectedFaculty:  null,

        deanMap: {
            @foreach ($deanUsers as $u)
                {{ $u->id }}: @js($u->name),
            @endforeach
        },
        getName(id) {
            return id && this.deanMap[id] ? this.deanMap[id] : null;
        },

        async addReviewer() {
            if (!this.selectedFaculty) return;
            this.addingReviewer = true;
            await $wire.$parent.addReviewer(this.selectedFaculty);
            this.addingReviewer = false;
            this.selectedFaculty = null;
        },

        async removeReviewer(id) {
            this.removingId = id;
            await $wire.$parent.removeReviewer(id);
            this.removingId = null;
        }
    }"
    class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

    {{-- Header --}}
    <button type="button" x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-slate-50 transition-colors">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600">
                <i class="bx bx-user-check text-lg"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-800">Review &amp; Approval</p>
                <p class="text-xs text-slate-500">
                    Signatures, concurrence &amp; additional reviewers
                    @if (count($reviewers) > 0)
                        · <span class="text-blue-600 font-medium">
                            {{ count($reviewers) }} {{ Str::plural('reviewer', count($reviewers)) }}
                        </span>
                    @endif
                </p>
            </div>
        </div>
        <i class="bx text-slate-400 text-xl transition-transform duration-200"
           x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
    </button>

    {{-- Body --}}
    <div x-show="open" x-collapse>
        <div class="border-t border-slate-100 p-5 space-y-6">

            {{-- Prepared By (read-only) --}}
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-3 flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-full
                             bg-blue-200 text-blue-700 shrink-0 text-xs font-bold">
                    {{ strtoupper(substr($syllabus->preparer->name ?? 'U', 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="text-[10px] text-blue-500 font-semibold uppercase tracking-wide">Prepared By</p>
                    <p class="text-sm font-semibold text-slate-800 truncate">
                        {{ $syllabus->preparer->name ?? 'N/A' }}
                    </p>
                    <p class="text-xs text-slate-500 truncate">{{ $syllabus->preparer->email ?? '' }}</p>
                </div>
            </div>

            {{-- Approved + Concurred (two columns) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- Approved By (Dean) --}}
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        Approved By <span class="text-slate-400 font-normal normal-case">(Dean)</span>
                    </p>
                    <div x-show="getName(localApprovedBy)"
                         class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-emerald-200 text-emerald-700 shrink-0 text-xs font-bold"
                                  x-text="getName(localApprovedBy)?.charAt(0)?.toUpperCase() ?? ''"></span>
                            <p class="text-xs font-semibold text-slate-800 truncate" x-text="getName(localApprovedBy)"></p>
                        </div>
                        <button type="button" wire:click="clearApproved" x-on:click="localApprovedBy = null"
                            class="shrink-0 text-rose-400 hover:text-rose-600 transition-colors">
                            <i class="bx bx-x text-base leading-none"></i>
                        </button>
                    </div>
                    <div x-show="!getName(localApprovedBy)"
                         class="rounded-xl border border-dashed border-slate-200 p-3 text-center">
                        <p class="text-xs text-slate-400">No dean assigned yet.</p>
                    </div>
                    <div class="flex gap-2">
                        <select wire:model="approvedBy"
                            x-on:change="localApprovedBy = $event.target.value ? parseInt($event.target.value) : null"
                            class="flex-1 text-xs rounded-xl border border-slate-300 bg-white px-2.5 py-2
                                   focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none">
                            <option value="">Select Dean…</option>
                            @foreach ($deanUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" wire:click="saveApproved"
                            wire:loading.attr="disabled" wire:target="saveApproved"
                            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl
                                   bg-emerald-600 text-white text-xs font-semibold
                                   hover:bg-emerald-700 disabled:opacity-60 transition-colors">
                            <span wire:loading.remove wire:target="saveApproved" class="inline-flex items-center gap-1.5">
                                <i class="bx bx-check leading-none"></i> Set
                            </span>
                            <span wire:loading wire:target="saveApproved" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Saving…
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Concurred By (Dean, nullable) --}}
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        Concurred By <span class="text-slate-400 font-normal normal-case">(Dean · optional)</span>
                    </p>
                    <div x-show="getName(localConcurredBy)"
                         class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-emerald-200 text-emerald-700 shrink-0 text-xs font-bold"
                                  x-text="getName(localConcurredBy)?.charAt(0)?.toUpperCase() ?? ''"></span>
                            <p class="text-xs font-semibold text-slate-800 truncate" x-text="getName(localConcurredBy)"></p>
                        </div>
                        <button type="button" wire:click="clearConcurred" x-on:click="localConcurredBy = null"
                            class="shrink-0 text-rose-400 hover:text-rose-600 transition-colors">
                            <i class="bx bx-x text-base leading-none"></i>
                        </button>
                    </div>
                    <div x-show="!getName(localConcurredBy)"
                         class="rounded-xl border border-dashed border-slate-200 p-3 text-center">
                        <p class="text-xs text-slate-400">No concurrence assigned.</p>
                    </div>
                    <div class="flex gap-2">
                        <select wire:model="concurredBy"
                            x-on:change="localConcurredBy = $event.target.value ? parseInt($event.target.value) : null"
                            class="flex-1 text-xs rounded-xl border border-slate-300 bg-white px-2.5 py-2
                                   focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none">
                            <option value="">Select Dean…</option>
                            @foreach ($deanUsers as $user)
                                <option value="{{ $user->id }}"
                                    x-bind:disabled="localApprovedBy == {{ $user->id }}"
                                    x-bind:class="localApprovedBy == {{ $user->id }} ? 'text-slate-300' : ''">
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" wire:click="saveConcurred"
                            wire:loading.attr="disabled" wire:target="saveConcurred"
                            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl
                                   bg-emerald-600 text-white text-xs font-semibold
                                   hover:bg-emerald-700 disabled:opacity-60 transition-colors">
                            <span wire:loading.remove wire:target="saveConcurred" class="inline-flex items-center gap-1.5">
                                <i class="bx bx-check leading-none"></i> Set
                            </span>
                            <span wire:loading wire:target="saveConcurred" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Saving…
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Reviewed By (additional faculty) --}}
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">
                    Reviewed By
                    <span class="text-slate-400 font-normal normal-case">(Additional — Faculty)</span>
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                    {{--
                        LEFT: add form
                        wire:loading CANNOT track $parent.addReviewer because the method
                        lives on SyllabusWizard, not ReviewStep. Alpine `addingReviewer`
                        is the correct loading indicator here.
                    --}}
                    <div class="space-y-2">
                        <div class="flex gap-2">
                            <select x-model="selectedFaculty"
                                class="flex-1 text-xs rounded-xl border border-slate-300 bg-white px-2.5 py-2
                                       focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none">
                                <option value="">Select faculty reviewer…</option>
                                @foreach ($facultyUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button type="button"
                                x-on:click="addReviewer()"
                                x-bind:disabled="addingReviewer || !selectedFaculty"
                                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl
                                       bg-emerald-600 text-white text-xs font-semibold
                                       hover:bg-emerald-700 disabled:opacity-60 transition-colors">
                                <template x-if="!addingReviewer">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="bx bx-plus leading-none"></i> Add
                                    </span>
                                </template>
                                <template x-if="addingReviewer">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        Adding…
                                    </span>
                                </template>
                            </button>
                        </div>
                        <p class="text-xs text-slate-400">
                            Each reviewer will appear in the printed syllabus signature section.
                        </p>
                    </div>

                    {{-- RIGHT: reviewer list --}}
                    <div class="space-y-2">
                        @if (count($reviewers) > 0)
                            @foreach ($reviewers as $reviewer)
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl
                                            border border-slate-200 transition-opacity"
                                     x-bind:class="removingId === {{ $reviewer['id'] }} ? 'opacity-40 pointer-events-none' : ''"
                                     wire:key="reviewer-{{ $reviewer['id'] }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="flex items-center justify-center w-7 h-7 rounded-full
                                                     bg-slate-100 text-slate-600 shrink-0 text-xs font-bold">
                                            {{ strtoupper(substr($reviewer['user_name'], 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-xs font-medium text-slate-800 truncate">
                                                {{ $reviewer['user_name'] }}
                                            </p>
                                            <p class="text-xs text-slate-400 truncate">
                                                {{ $reviewer['user_email'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 ml-2 shrink-0">
                                        <x-feedback-status.status-indicator
                                            :status="$reviewer['status'] === 'approved' ? 'success' : $reviewer['status']"
                                            :label="$reviewer['status'] === 'approved' ? 'Approved' : ucfirst($reviewer['status'])" />

                                        {{--
                                            wire:loading cannot track $parent.removeReviewer.
                                            removingId shows a spinner on the exact row being deleted.
                                        --}}
                                        <button type="button"
                                            x-on:click="removeReviewer({{ $reviewer['id'] }})"
                                            x-bind:disabled="removingId === {{ $reviewer['id'] }}"
                                            class="inline-flex items-center justify-center w-6 h-6
                                                   text-rose-400 hover:text-rose-600
                                                   disabled:opacity-50 transition-colors">
                                            <template x-if="removingId !== {{ $reviewer['id'] }}">
                                                <i class="bx bx-trash text-sm leading-none"></i>
                                            </template>
                                            <template x-if="removingId === {{ $reviewer['id'] }}">
                                                <svg class="animate-spin h-3.5 w-3.5 shrink-0 text-rose-400"
                                                     viewBox="0 0 24 24" fill="none">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"/>
                                                    <path class="opacity-75" fill="currentColor"
                                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                </svg>
                                            </template>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="rounded-xl border border-dashed border-slate-200 p-5 text-center">
                                <i class="bx bx-group text-2xl text-slate-300"></i>
                                <p class="text-xs text-slate-400 mt-1">No additional reviewers yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
