{{--
    REVIEW & APPROVAL ACCORDION
    ─────────────────────────────────────────────────────────────────────────
    • approved_by  → dean only (required when set)
    • concurred_by → dean only, nullable, must differ from approved_by
    • Reviewed By  → faculty only; already-added names removed from options
    • Adding a reviewer: $parent.addReviewer → accordion stays open, no reload
    • Alpine local mirrors (localApprovedBy / localConcurredBy) give instant
      badge updates without a round-trip; "Set" button persists to DB.
    • Concurred dean select filters out whoever is selected as Approved.
    ─────────────────────────────────────────────────────────────────────────
--}}
<div
    x-data="{
        open: true,
        localApprovedBy:  {{ $approvedBy  ?? 'null' }},
        localConcurredBy: {{ $concurredBy ?? 'null' }},
        deanMap: {
            @foreach ($deanUsers as $u)
                '{{ $u->id }}': @js($u->name),
            @endforeach
        },
        getName(id) {
            return id && this.deanMap[id] ? this.deanMap[id] : null;
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

            {{-- ══ Approved + Concurred (two columns) ══ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- Approved By (Dean) --}}
                <div class="space-y-2">
                    <x-form.label for="approved-by">
                        Approved By <span class="text-slate-400 font-normal normal-case">(Dean)</span>
                    </x-form.label>

                    {{-- Current badge — instant via Alpine mirror --}}
                    <div x-show="getName(localApprovedBy)"
                         class="rounded-xl border border-emerald-200 bg-emerald-50 p-3
                                flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full
                                         bg-emerald-200 text-emerald-700 shrink-0 text-xs font-bold"
                                  x-text="getName(localApprovedBy)?.charAt(0)?.toUpperCase() ?? ''"></span>
                            <p class="text-xs font-semibold text-slate-800 truncate"
                               x-text="getName(localApprovedBy)"></p>
                        </div>
                        <button type="button"
                            wire:click="clearApproved"
                            x-on:click="localApprovedBy = null"
                            class="shrink-0 text-rose-400 hover:text-rose-600 transition-colors">
                            <i class="bx bx-x text-base"></i>
                        </button>
                    </div>
                    <div x-show="!getName(localApprovedBy)"
                         class="rounded-xl border border-dashed border-slate-200 p-3 text-center">
                        <p class="text-xs text-slate-400">No dean assigned yet.</p>
                    </div>

                    <div class="flex gap-2">
                        <x-form.select id="approved-by" wire:model="approvedBy"
                            x-on:change="localApprovedBy = $event.target.value ? parseInt($event.target.value) : null"
                            class="flex-1 text-xs rounded-xl px-2.5 py-2">
                            <option value="">Select Dean…</option>
                            @foreach ($deanUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </x-form.select>
                        <x-button
                            type="button"
                            wire:click="saveApproved"
                            wire:target="saveApproved"
                            variant="table-confirm"
                            loading="Saving"
                            class="shrink-0 text-xs">
                            <i class="bx bx-check"></i> Set
                        </x-button>
                    </div>
                </div>

                {{-- Concurred By (Dean, nullable, must differ from approved) --}}
                <div class="space-y-2">
                    <x-form.label for="concurred-by">
                        Concurred By
                        <span class="text-slate-400 font-normal normal-case">(Dean · optional)</span>
                    </x-form.label>

                    <div x-show="getName(localConcurredBy)"
                         class="rounded-xl border border-emerald-200 bg-emerald-50 p-3
                                flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full
                                         bg-emerald-200 text-emerald-700 shrink-0 text-xs font-bold"
                                  x-text="getName(localConcurredBy)?.charAt(0)?.toUpperCase() ?? ''"></span>
                            <p class="text-xs font-semibold text-slate-800 truncate"
                               x-text="getName(localConcurredBy)"></p>
                        </div>
                        <button type="button"
                            wire:click="clearConcurred"
                            x-on:click="localConcurredBy = null"
                            class="shrink-0 text-rose-400 hover:text-rose-600 transition-colors">
                            <i class="bx bx-x text-base"></i>
                        </button>
                    </div>
                    <div x-show="!getName(localConcurredBy)"
                         class="rounded-xl border border-dashed border-slate-200 p-3 text-center">
                        <p class="text-xs text-slate-400">No concurrence assigned.</p>
                    </div>

                    <div class="flex gap-2">
                        {{--
                            Filter out whoever is selected as Approved so the two
                            selects can never share the same dean.
                            Alpine hides matching options client-side for instant feedback;
                            saveConcurred() double-checks server-side.
                        --}}
                        <x-form.select id="concurred-by" wire:model="concurredBy"
                            x-on:change="localConcurredBy = $event.target.value ? parseInt($event.target.value) : null"
                            class="flex-1 text-xs rounded-xl px-2.5 py-2">
                            <option value="">Select Dean…</option>
                            @foreach ($deanUsers as $user)
                                <option value="{{ $user->id }}"
                                    x-bind:disabled="localApprovedBy == {{ $user->id }}"
                                    x-bind:class="localApprovedBy == {{ $user->id }} ? 'text-slate-300' : ''">
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </x-form.select>
                        <x-button
                            type="button"
                            wire:click="saveConcurred"
                            wire:target="saveConcurred"
                            variant="table-confirm"
                            loading="Saving"
                            class="shrink-0 text-xs">
                            <i class="bx bx-check"></i> Set
                        </x-button>
                    </div>
                </div>
            </div>

            {{-- ══ Reviewed By (additional faculty) ══ --}}
            <div>
                <div class="mb-3">
                    <x-form.label for="reviewer-id">
                        Reviewed By
                        <span class="text-slate-400 font-normal normal-case">(Additional — Faculty)</span>
                    </x-form.label>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                    {{-- LEFT: add form --}}
                    <div class="space-y-2">
                        <div class="flex gap-2">
                            {{-- facultyUsers already excludes already-added reviewers (from render()) --}}
                            <x-form.select id="reviewer-id" wire:model="selectedReviewerId"
                                class="flex-1 text-xs rounded-xl px-2.5 py-2">
                                <option value="">Select faculty reviewer…</option>
                                @foreach ($facultyUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </x-form.select>
                            <x-button
                                type="button"
                                wire:click="$parent.addReviewer($wire.selectedReviewerId)"
                                wire:target="addReviewer"
                                variant="table-confirm"
                                loading="Adding"
                                class="shrink-0 text-xs">
                                <i class="bx bx-plus"></i> Add
                            </x-button>
                        </div>
                        <p class="text-xs text-slate-400">
                            Each reviewer will appear in the printed syllabus signature section.
                        </p>
                    </div>

                    {{-- RIGHT: reviewer list --}}
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
                            <x-empty-state
                                icon="bx bx-group"
                                title="No additional reviewers yet."
                                description="Use the form on the left to add faculty reviewers who will appear in the printed syllabus signature section." />
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
