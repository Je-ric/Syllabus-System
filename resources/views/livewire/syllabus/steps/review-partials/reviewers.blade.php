{{--
    REVIEW & APPROVAL ACCORDION
    ─────────────────────────────────────────────────────────────────────────
    Performance strategy:
      • Approved By / Concurred By: wire:model on <select> is fine (single event)
        but we ONLY send to server on explicit "Set" button click (saveApproved /
        saveConcurred). No auto-sync on every change.
      • Alpine x-data holds local copies of approvedBy / concurredBy so the
        "current user" display updates instantly without a round-trip.
      • Additional Reviewers: $parent.addReviewer / $parent.removeReviewer
        (SyllabusWizard) — these are single-event actions, no typing involved.
    ─────────────────────────────────────────────────────────────────────────
    Role filters (enforced in ReviewStep::render()):
      $deanUsers    → users with 'dean' role    → approved_by & concurred_by
      $facultyUsers → users with 'faculty' role → additional reviewers
    ─────────────────────────────────────────────────────────────────────────
--}}
<div
    x-data="{
        open: false,
        {{-- Local mirrors so current-user display updates instantly on select --}}
        localApprovedBy:  {{ $approvedBy  ?? 'null' }},
        localConcurredBy: {{ $concurredBy ?? 'null' }},
        {{-- Map of id → name for instant label display --}}
        deanMap: {
            @foreach ($deanUsers as $u)
                {{ $u->id }}: @js($u->name),
            @endforeach
        },
        facultyMap: {
            @foreach ($facultyUsers as $u)
                {{ $u->id }}: @js($u->name),
            @endforeach
        },
        getName(map, id) {
            return id && map[id] ? map[id] : null;
        }
    }"
    class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

    {{-- ── Header ── --}}
    <button type="button"
        x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-slate-50 transition-colors">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600">
                <i class="bx bx-user-check text-lg"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-800">Review & Approval</p>
                <p class="text-xs text-slate-500">
                    Signatures, concurrence &amp; additional reviewers
                    @if (count($reviewers) > 0)
                        · <span class="text-blue-600 font-medium">{{ count($reviewers) }} reviewer{{ count($reviewers) !== 1 ? 's' : '' }}</span>
                    @endif
                </p>
            </div>
        </div>
        <i class="bx text-slate-400 text-xl transition-transform duration-200"
           x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
    </button>

    {{-- ── Body ── --}}
    <div x-show="open" x-collapse>
        <div class="border-t border-slate-100 p-5 space-y-6">

            {{-- Prepared By (read-only, always shown) --}}
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

            {{-- ══ Approved + Concurred (two columns) ══ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- ── Approved By (Dean) ── --}}
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        Approved By <span class="text-slate-400 font-normal">(Dean)</span>
                    </p>

                    {{-- Current selection badge (instant via Alpine local mirror) --}}
                    <div x-show="getName(deanMap, localApprovedBy)"
                         class="rounded-xl border border-emerald-200 bg-emerald-50 p-3
                                flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full
                                         bg-emerald-200 text-emerald-700 shrink-0 text-xs font-bold"
                                  x-text="getName(deanMap, localApprovedBy)?.charAt(0)?.toUpperCase() ?? ''"></span>
                            <p class="text-xs font-semibold text-slate-800 truncate"
                               x-text="getName(deanMap, localApprovedBy)"></p>
                        </div>
                        <button type="button"
                            wire:click="clearApproved"
                            x-on:click="localApprovedBy = null"
                            class="shrink-0 text-rose-400 hover:text-rose-600 transition-colors">
                            <i class="bx bx-x text-base"></i>
                        </button>
                    </div>
                    <div x-show="!getName(deanMap, localApprovedBy)"
                         class="rounded-xl border border-dashed border-slate-200 p-3 text-center">
                        <p class="text-xs text-slate-400">No dean assigned yet.</p>
                    </div>

                    {{-- Picker — wire:model syncs to $approvedBy on change --}}
                    <div class="flex gap-2">
                        <select
                            wire:model="approvedBy"
                            x-on:change="localApprovedBy = $event.target.value ? parseInt($event.target.value) : null"
                            class="flex-1 text-xs rounded-xl border border-slate-300 bg-white px-2.5 py-2
                                   focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none">
                            <option value="">Select Dean…</option>
                            @foreach ($deanUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button type="button"
                            wire:click="saveApproved"
                            wire:loading.attr="disabled"
                            wire:target="saveApproved"
                            class="shrink-0 flex items-center gap-1 px-3 py-2 rounded-xl
                                   bg-slate-700 text-white text-xs font-semibold
                                   hover:bg-slate-800 disabled:opacity-60 transition-colors">
                            <span wire:loading.remove wire:target="saveApproved">
                                <i class="bx bx-check text-sm"></i> Set
                            </span>
                            <span wire:loading wire:target="saveApproved">
                                <i class="bx bx-loader-alt bx-spin text-sm"></i>
                            </span>
                        </button>
                    </div>
                </div>

                {{-- ── Concurred By (Dean, nullable) ── --}}
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        Concurred By <span class="text-slate-400 font-normal">(Dean)</span>
                    </p>

                    <div x-show="getName(deanMap, localConcurredBy)"
                         class="rounded-xl border border-emerald-200 bg-emerald-50 p-3
                                flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full
                                         bg-emerald-200 text-emerald-700 shrink-0 text-xs font-bold"
                                  x-text="getName(deanMap, localConcurredBy)?.charAt(0)?.toUpperCase() ?? ''"></span>
                            <p class="text-xs font-semibold text-slate-800 truncate"
                               x-text="getName(deanMap, localConcurredBy)"></p>
                        </div>
                        <button type="button"
                            wire:click="clearConcurred"
                            x-on:click="localConcurredBy = null"
                            class="shrink-0 text-rose-400 hover:text-rose-600 transition-colors">
                            <i class="bx bx-x text-base"></i>
                        </button>
                    </div>
                    <div x-show="!getName(deanMap, localConcurredBy)"
                         class="rounded-xl border border-dashed border-slate-200 p-3 text-center">
                        <p class="text-xs text-slate-400">No concurrence assigned yet.</p>
                    </div>

                    <div class="flex gap-2">
                        <select
                            wire:model="concurredBy"
                            x-on:change="localConcurredBy = $event.target.value ? parseInt($event.target.value) : null"
                            class="flex-1 text-xs rounded-xl border border-slate-300 bg-white px-2.5 py-2
                                   focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none">
                            <option value="">Select Dean…</option>
                            @foreach ($deanUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button type="button"
                            wire:click="saveConcurred"
                            wire:loading.attr="disabled"
                            wire:target="saveConcurred"
                            class="shrink-0 flex items-center gap-1 px-3 py-2 rounded-xl
                                   bg-slate-700 text-white text-xs font-semibold
                                   hover:bg-slate-800 disabled:opacity-60 transition-colors">
                            <span wire:loading.remove wire:target="saveConcurred">
                                <i class="bx bx-check text-sm"></i> Set
                            </span>
                            <span wire:loading wire:target="saveConcurred">
                                <i class="bx bx-loader-alt bx-spin text-sm"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ══ Reviewed By (additional, N faculty) ══ --}}
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">
                    Reviewed By
                    <span class="text-slate-400 font-normal">(Additional — Faculty)</span>
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                    {{-- LEFT: Add form --}}
                    <div class="space-y-2">
                        <div class="flex gap-2">
                            <select
                                wire:model="selectedReviewerId"
                                class="flex-1 text-xs rounded-xl border border-slate-300 bg-white px-2.5 py-2
                                       focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none">
                                <option value="">Select faculty reviewer…</option>
                                @foreach ($facultyUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button type="button"
                                wire:click="$parent.addReviewer($wire.selectedReviewerId)"
                                wire:loading.attr="disabled"
                                wire:target="addReviewer"
                                class="shrink-0 flex items-center gap-1 px-3 py-2 rounded-xl
                                       bg-emerald-600 text-white text-xs font-semibold
                                       hover:bg-emerald-700 disabled:opacity-60 transition-colors">
                                <span wire:loading.remove wire:target="addReviewer">
                                    <i class="bx bx-plus text-sm"></i> Add
                                </span>
                                <span wire:loading wire:target="addReviewer">
                                    <i class="bx bx-loader-alt bx-spin text-sm"></i>
                                </span>
                            </button>
                        </div>
                        <p class="text-xs text-slate-400">
                            Multiple faculty reviewers can be added.
                            They appear in the signatures section of the printed syllabus.
                        </p>
                    </div>

                    {{-- RIGHT: Reviewer list --}}
                    <div class="space-y-2">
                        @if (count($reviewers) > 0)
                            @foreach ($reviewers as $reviewer)
                                <div class="flex items-center justify-between p-3 bg-white
                                            rounded-xl border border-slate-200"
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
                                        <button type="button"
                                            wire:click="$parent.removeReviewer({{ $reviewer['id'] }})"
                                            wire:confirm="Remove this reviewer?"
                                            class="text-rose-400 hover:text-rose-600 transition-colors">
                                            <i class="bx bx-trash text-sm"></i>
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

        </div>{{-- /body inner --}}
    </div>
</div>
