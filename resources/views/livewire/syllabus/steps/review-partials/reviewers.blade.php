{{--
    REVIEW & APPROVAL ACCORDION
    ─────────────────────────────────────────────────────────────────────────
    Three sub-sections, clearly grouped:
      1. Prepared By   — read-only
      2. Approved By   — dean, wire:model + Set button (local component)
      3. Concurred By  — dean, nullable, must differ from approved
      4. Reviewed By   — faculty; addReviewer/removeReviewer via $parent.*

    Loading indicators:
    • Approved/Concurred Set buttons — wire:loading (methods are on THIS component)
    • Add reviewer   — Alpine addingReviewer flag ($parent call can't use wire:loading)
    • Remove reviewer — Alpine removingId flag  ($parent call can't use wire:loading)
    ─────────────────────────────────────────────────────────────────────────
--}}
<div
    x-data="{
        open: true,

        /* ── Dean badge mirrors (instant Alpine update, no round-trip) ── */
        localApprovedBy:  {{ $approvedBy  ?? 'null' }},
        localConcurredBy: {{ $concurredBy ?? 'null' }},
        deanMap: {
            @foreach ($deanUsers as $u)
                {{ $u->id }}: @js($u->name),
            @endforeach
        },
        getName(id) {
            return id && this.deanMap[id] ? this.deanMap[id] : null;
        },

        /* ── Faculty reviewer state ── */
        addingReviewer:  false,
        removingId:      null,
        clearingApproved: false,
        clearingConcurred: false,
        selectedFaculty: null,

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
        },

        async clearApprovedBy() {
            if (this.clearingApproved) return;
            this.clearingApproved = true;
            await $wire.clearApproved();
            this.localApprovedBy = null;
            this.clearingApproved = false;
        },

        async clearConcurredBy() {
            if (this.clearingConcurred) return;
            this.clearingConcurred = true;
            await $wire.clearConcurred();
            this.localConcurredBy = null;
            this.clearingConcurred = false;
        }
    }"
    class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

    {{-- ── Header ── --}}
    <button type="button" x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left
               hover:bg-slate-50 transition-colors">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg
                         bg-blue-100 text-blue-500">
                <i class="bx bx-user-check text-lg leading-none"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-800">Review &amp; Approval</p>
                <p class="text-xs text-slate-400 mt-0.5">
                    Signatures, concurrence &amp; additional reviewers
                    @if (count($reviewers) > 0)
                        ·
                        <span class="text-blue-600 font-semibold">
                            {{ count($reviewers) }} {{ Str::plural('reviewer', count($reviewers)) }}
                        </span>
                    @endif
                </p>
            </div>
        </div>
        <i class="bx text-slate-400 text-xl transition-transform duration-200"
           x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
    </button>

    {{-- ── Body ── --}}
    <div x-show="open" x-collapse>
        <div class="border-t border-slate-100 divide-y divide-slate-100">

            {{-- ══════════════════════════════════════════════════════
                 SECTION 1 — Signatories (Prepared, Approved, Concurred)
                 ══════════════════════════════════════════════════════ --}}
            <div class="px-5 py-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3">
                    Signatories
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

                    {{-- Prepared By --}}
                    <div class="space-y-2">
                        <x-form.label>
                            Prepared By
                            <span class="text-slate-300 font-normal normal-case tracking-normal">
                                (Author)
                            </span>
                        </x-form.label>
                        <div class="flex items-center gap-3 rounded-xl border border-blue-100
                                    bg-blue-50/60 px-4 py-3 h-21">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full
                                         bg-blue-200 text-blue-700 text-xs font-bold shrink-0">
                                {{ strtoupper(substr($syllabus->preparer->name ?? 'U', 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ $syllabus->preparer->name ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-slate-400 truncate">
                                    {{ $syllabus->preparer->email ?? '' }}
                                </p>
                            </div>
                            <x-feedback-status.status-indicator variant="blue" class="ml-auto shrink-0">
                                Author
                            </x-feedback-status.status-indicator>
                        </div>
                    </div>

                    {{-- Approved By --}}
                    <div class="space-y-2">
                        <x-form.label>
                            Approved By
                            <span class="text-slate-300 font-normal normal-case tracking-normal">(Dean)</span>
                        </x-form.label>

                        {{-- Current badge --}}
                            <div x-show="getName(localApprovedBy)"
                                class="flex items-center justify-between gap-2 rounded-xl
                                     border border-emerald-200 bg-emerald-50/70 px-3.5 py-2.5 h-10.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                             bg-emerald-200 text-emerald-700 text-xs font-bold shrink-0"
                                      x-text="getName(localApprovedBy)?.charAt(0)?.toUpperCase() ?? ''">
                                </span>
                                <p class="text-xs font-semibold text-slate-800 truncate"
                                   x-text="getName(localApprovedBy)"></p>
                            </div>
                            <button type="button"
                                x-on:click="clearApprovedBy()"
                                x-bind:disabled="clearingApproved"
                                class="shrink-0 p-0.5 text-rose-400 hover:text-rose-600 transition-colors">
                                <template x-if="!clearingApproved">
                                    <i class="bx bx-x text-base leading-none"></i>
                                </template>
                                <template x-if="clearingApproved">
                                    <svg class="animate-spin h-3.5 w-3.5 text-rose-400"
                                         viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </template>
                            </button>
                        </div>
                            <div x-show="!getName(localApprovedBy)"
                                class="rounded-xl border border-dashed border-slate-200 py-2.5 text-center h-10.5 flex items-center justify-center">
                            <p class="text-xs text-slate-400">No dean assigned yet.</p>
                        </div>

                        <div class="flex gap-2">
                            <x-form.select
                                wire:model="approvedBy"
                                x-on:change="localApprovedBy = $event.target.value
                                    ? parseInt($event.target.value) : null"
                                class="flex-1 text-xs py-2">
                                <option value="">Select dean…</option>
                                @foreach ($deanUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </x-form.select>
                            <x-button type="button" variant="add-button"
                                wire:click="saveApproved"
                                wire:loading.attr="disabled"
                                wire:target="saveApproved"
                                loading="Saving…">
                                <i class="bx bx-check"></i> Set
                            </x-button>
                        </div>
                    </div>

                    {{-- Concurred By --}}
                    <div class="space-y-2">
                        <x-form.label>
                            Concurred By
                            <span class="text-slate-300 font-normal normal-case tracking-normal">
                                (Dean · optional)
                            </span>
                        </x-form.label>

                            <div x-show="getName(localConcurredBy)"
                                class="flex items-center justify-between gap-2 rounded-xl
                                     border border-emerald-200 bg-emerald-50/70 px-3.5 py-2.5 h-10.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                             bg-emerald-200 text-emerald-700 text-xs font-bold shrink-0"
                                      x-text="getName(localConcurredBy)?.charAt(0)?.toUpperCase() ?? ''">
                                </span>
                                <p class="text-xs font-semibold text-slate-800 truncate"
                                   x-text="getName(localConcurredBy)"></p>
                            </div>
                            <button type="button"
                                x-on:click="clearConcurredBy()"
                                x-bind:disabled="clearingConcurred"
                                class="shrink-0 p-0.5 text-rose-400 hover:text-rose-600 transition-colors">
                                <template x-if="!clearingConcurred">
                                    <i class="bx bx-x text-base leading-none"></i>
                                </template>
                                <template x-if="clearingConcurred">
                                    <svg class="animate-spin h-3.5 w-3.5 text-rose-400"
                                         viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </template>
                            </button>
                        </div>
                            <div x-show="!getName(localConcurredBy)"
                                class="rounded-xl border border-dashed border-slate-200 py-2.5 text-center h-10.5 flex items-center justify-center">
                            <p class="text-xs text-slate-400">No concurrence assigned.</p>
                        </div>

                        <div class="flex gap-2">
                            <x-form.select
                                wire:model="concurredBy"
                                x-on:change="localConcurredBy = $event.target.value
                                    ? parseInt($event.target.value) : null"
                                class="flex-1 text-xs py-2">
                                <option value="">Select dean…</option>
                                @foreach ($deanUsers as $user)
                                    <option value="{{ $user->id }}"
                                        x-bind:disabled="localApprovedBy == {{ $user->id }}"
                                        x-bind:class="localApprovedBy == {{ $user->id }}
                                            ? 'text-slate-300' : ''">
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </x-form.select>
                            <x-button type="button" variant="add-button"
                                wire:click="saveConcurred"
                                wire:loading.attr="disabled"
                                wire:target="saveConcurred"
                                loading="Saving…">
                                <i class="bx bx-check"></i> Set
                            </x-button>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════
                 SECTION 3 — Reviewed By (additional faculty)
                 ══════════════════════════════════════════════════════ --}}
            <div class="px-5 py-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3">
                    Additional Reviewers
                    <span class="text-slate-300 font-normal normal-case tracking-normal ml-1">
                        (Faculty)
                    </span>
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                    {{-- Add form --}}
                    {{--
                        wire:loading CANNOT track $parent.addReviewer (method lives
                        on SyllabusWizard, not ReviewStep). Alpine addingReviewer is
                        the correct loading indicator here.
                    --}}
                    <div class="space-y-2">
                        <div class="flex gap-2">
                            <x-form.select
                                x-model="selectedFaculty"
                                class="flex-1 text-xs py-2">
                                <option value="">Select faculty reviewer…</option>
                                @foreach ($facultyUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </x-form.select>
                            <x-button type="button" variant="add-button"
                                x-on:click="addReviewer()"
                                x-bind:disabled="addingReviewer || !selectedFaculty">
                                <span x-show="!addingReviewer" class="inline-flex items-center gap-1.5">
                                    <i class="bx bx-plus"></i> Add
                                </span>
                                <span x-show="addingReviewer" x-cloak class="inline-flex items-center gap-1.5">
                                    <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Adding…
                                </span>
                            </x-button>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Each reviewer appears in the printed syllabus signature section.
                        </p>
                    </div>

                    {{-- Reviewer list --}}
                    <div class="space-y-2">
                        @if (count($reviewers) > 0)
                            @foreach ($reviewers as $reviewer)
                                <div
                                    wire:key="reviewer-{{ $reviewer['id'] }}"
                                    x-bind:class="removingId === {{ $reviewer['id'] }}
                                        ? 'opacity-40 pointer-events-none'
                                        : ''"
                                    class="flex items-center justify-between px-3.5 py-2.5
                                           rounded-xl border border-slate-200 bg-white
                                           transition-opacity duration-150">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="inline-flex items-center justify-center
                                                     w-7 h-7 rounded-full bg-slate-100
                                                     text-slate-600 text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($reviewer['user_name'], 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-xs font-medium text-slate-800 truncate">
                                                {{ $reviewer['user_name'] }}
                                            </p>
                                            <p class="text-[11px] text-slate-400 truncate">
                                                {{ $reviewer['user_email'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 ml-2 shrink-0">
                                        <x-feedback-status.status-indicator
                                            :status="$reviewer['status'] === 'approved' ? 'success' : $reviewer['status']"
                                            :label="$reviewer['status'] === 'approved'
                                                ? 'Approved' : ucfirst($reviewer['status'])" />

                                        {{-- Remove: Alpine spinner since $parent can't use wire:loading --}}
                                        <button type="button"
                                            x-on:click="removeReviewer({{ $reviewer['id'] }})"
                                            x-bind:disabled="removingId !== null"
                                            class="inline-flex items-center justify-center
                                                   w-6 h-6 rounded-lg text-slate-400
                                                   hover:text-rose-600 hover:bg-rose-50
                                                   disabled:opacity-50 transition-colors">
                                            <template x-if="removingId !== {{ $reviewer['id'] }}">
                                                <i class="bx bx-trash text-sm leading-none"></i>
                                            </template>
                                            <template x-if="removingId === {{ $reviewer['id'] }}">
                                                <svg class="animate-spin h-3.5 w-3.5 text-rose-400"
                                                     viewBox="0 0 24 24" fill="none">
                                                    <circle class="opacity-25" cx="12" cy="12"
                                                            r="10" stroke="currentColor" stroke-width="4"/>
                                                    <path class="opacity-75" fill="currentColor"
                                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                </svg>
                                            </template>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <x-empty-state
                                icon="bx-group"
                                title="No additional reviewers"
                                message="Select a faculty member from the dropdown and click Add." />
                        @endif
                    </div>

                </div>
            </div>

        </div>{{-- /divide-y --}}
    </div>
</div>
