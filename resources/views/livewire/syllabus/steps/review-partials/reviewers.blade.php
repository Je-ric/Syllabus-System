{{--
    REVIEW & APPROVAL ACCORDION
    ─────────────────────────────────────────────────────────────────────────
    Loading indicators:
    • Approved/Concurred Set buttons — wire:loading
    • Add reviewer   — Alpine addingReviewer flag
    • Remove reviewer — Alpine removingId flag
    ─────────────────────────────────────────────────────────────────────────
--}}
<div
    x-data="{
        open: true,
        localApprovedBy:  {{ $approvedBy  ?? 'null' }},
        localConcurredBy: {{ $concurredBy ?? 'null' }},
        deanMap: {
            @foreach ($deanUsers as $u)
                {{ $u['id'] }}: @js($u['name']),
            @endforeach
        },
        getName(id) {
            return id && this.deanMap[id] ? this.deanMap[id] : null;
        },
        addingReviewer:   false,
        removingId:       null,
        clearingApproved: false,
        clearingConcurred: false,
        selectedFaculty:  null,
        selectedRole:     'member',

        async addReviewer() {
            if (!this.selectedFaculty) return;
            this.addingReviewer = true;
            await $wire.$parent.addReviewer(this.selectedFaculty, this.selectedRole);
            this.addingReviewer = false;
            this.selectedFaculty = null;
            this.selectedRole = 'member';
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
    class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

    {{-- ── Header ── --}}
    <button type="button" x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left
               hover:bg-[#f8fafc] transition-colors focus:outline-none">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg" style="background: #f0fdf4; color: var(--clsu-green);">
                <i class="bx bx-user-check text-base leading-none"></i>
            </span>
            <div>
                <p class="text-sm font-bold text-[#0f172a]">Review &amp; Approval</p>
                <p class="text-xs text-[#94a3b8] mt-0.5">
                    Signatures, concurrence &amp; additional reviewers
                    @if (count($reviewers) > 0)
                        &middot;
                        <span class="font-semibold" style="color: var(--clsu-green);">
                            {{ count($reviewers) }} {{ Str::plural('reviewer', count($reviewers)) }}
                        </span>
                    @endif
                </p>
            </div>
        </div>
        <i class="bx text-[#94a3b8] text-lg transition-transform duration-200"
           x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
    </button>

    {{-- ── Body ── --}}
    <div x-show="open" x-collapse>
        <div class="border-t border-[#e2e8f0] divide-y divide-[#e2e8f0]">

            {{-- SECTION 1 — Signatories --}}
            <div class="px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3">Signatories</p>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

                    {{-- Prepared By --}}
                    <div class="space-y-2">
                        <x-form.label>
                            Prepared By
                            <span class="text-slate-300 font-normal normal-case tracking-normal">(Author)</span>
                        </x-form.label>

                        @php
                            $lecComp = $syllabus->components->firstWhere('type', 'LEC');
                            $labComp = $syllabus->components->firstWhere('type', 'LAB');
                        @endphp

                        {{-- LEC instructor (always shown) --}}
                        <div class="flex items-center gap-3 rounded-xl px-4 py-3"
                             style="border: 1px solid #bbf7d0; background: rgba(240,253,244,0.6);">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-xs font-bold shrink-0"
                                  style="background: #f0fdf4; color: var(--clsu-cobra);">
                                {{ strtoupper(substr($lecComp?->instructor_name ?? $syllabus->preparer->name ?? 'U', 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ $lecComp?->instructor_name ?? $syllabus->preparer->name ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-slate-400 truncate">
                                    {{ $lecComp?->instructor_email ?? $syllabus->preparer->email ?? '' }}
                                </p>
                            </div>
                            <x-feedback-status.status-indicator variant="slate" class="ml-auto shrink-0">
                                LEC
                            </x-feedback-status.status-indicator>
                        </div>

                        {{-- LAB instructor (only when course has lab and instructor is set) --}}
                        @if ($labComp && $labComp->instructor_name)
                            <div class="flex items-center gap-3 rounded-xl px-4 py-3"
                                 style="border: 1px solid #bfdbfe; background: rgba(239,246,255,0.6);">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-xs font-bold shrink-0"
                                      style="background: #eff6ff; color: #1d4ed8;">
                                    {{ strtoupper(substr($labComp->instructor_name, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $labComp->instructor_name }}</p>
                                    <p class="text-xs text-slate-400 truncate">{{ $labComp->instructor_email ?? '' }}</p>
                                </div>
                                <x-feedback-status.status-indicator variant="slate" class="ml-auto shrink-0">
                                    LAB
                                </x-feedback-status.status-indicator>
                            </div>
                        @endif
                    </div>

                    {{-- Approved By --}}
                    <div class="space-y-2">
                        <x-form.label>
                            Approved By
                            <span class="text-slate-300 font-normal normal-case tracking-normal">(Dean)</span>
                        </x-form.label>

                        <div x-show="getName(localApprovedBy)"
                            class="flex items-center justify-between gap-2 rounded-xl px-3.5 py-2.5"
                            style="border: 1px solid #bbf7d0; background: rgba(240,253,244,0.7);">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold shrink-0"
                                      style="background: #f0fdf4; color: var(--clsu-cobra);"
                                      x-text="getName(localApprovedBy)?.charAt(0)?.toUpperCase() ?? ''"></span>
                                <p class="text-xs font-semibold text-slate-800 truncate"
                                   x-text="getName(localApprovedBy)"></p>
                            </div>
                            <button type="button" x-on:click="clearApprovedBy()"
                                x-bind:disabled="clearingApproved"
                                class="shrink-0 p-0.5 text-rose-400 hover:text-rose-600 transition-colors">
                                <i x-show="!clearingApproved" class="bx bx-x text-base leading-none"></i>
                                <svg x-show="clearingApproved" x-cloak class="animate-spin h-3.5 w-3.5 text-rose-400"
                                     viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </button>
                        </div>
                        <div x-show="!getName(localApprovedBy)"
                            class="rounded-xl border border-dashed border-slate-200 py-2.5 text-center flex items-center justify-center">
                            <p class="text-xs text-slate-400">No dean assigned yet.</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <x-form.select wire:model="approvedBy"
                                    x-on:change="localApprovedBy = $event.target.value ? parseInt($event.target.value) : null">
                                    <option value="">Select dean…</option>
                                    @foreach ($deanUsers as $user)
                                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                    @endforeach
                                </x-form.select>
                            </div>
                            <x-ui.button type="button" variant="sm-add"
                                wire:click="saveApproved"
                                wire:loading.attr="disabled"
                                wire:target="saveApproved"
                                loading="Saving…">
                                <i class="bx bx-check"></i> Set
                            </x-ui.button>
                        </div>
                    </div>

                    {{-- Concurred By --}}
                    <div class="space-y-2">
                        <x-form.label>
                            Concurred By
                            <span class="text-slate-300 font-normal normal-case tracking-normal">(Dean · optional)</span>
                        </x-form.label>

                        <div x-show="getName(localConcurredBy)"
                            class="flex items-center justify-between gap-2 rounded-xl px-3.5 py-2.5"
                            style="border: 1px solid #bbf7d0; background: rgba(240,253,244,0.7);">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold shrink-0"
                                      style="background: #f0fdf4; color: var(--clsu-cobra);"
                                      x-text="getName(localConcurredBy)?.charAt(0)?.toUpperCase() ?? ''"></span>
                                <p class="text-xs font-semibold text-slate-800 truncate"
                                   x-text="getName(localConcurredBy)"></p>
                            </div>
                            <button type="button" x-on:click="clearConcurredBy()"
                                x-bind:disabled="clearingConcurred"
                                class="shrink-0 p-0.5 text-rose-400 hover:text-rose-600 transition-colors">
                                <i x-show="!clearingConcurred" class="bx bx-x text-base leading-none"></i>
                                <svg x-show="clearingConcurred" x-cloak class="animate-spin h-3.5 w-3.5 text-rose-400"
                                     viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </button>
                        </div>
                        <div x-show="!getName(localConcurredBy)"
                            class="rounded-xl border border-dashed border-slate-200 py-2.5 text-center flex items-center justify-center">
                            <p class="text-xs text-slate-400">No concurrence assigned.</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <x-form.select wire:model="concurredBy"
                                    x-on:change="localConcurredBy = $event.target.value ? parseInt($event.target.value) : null">
                                    <option value="">Select dean…</option>
                                    @foreach ($deanUsers as $user)
                                        <option value="{{ $user['id'] }}"
                                            x-bind:disabled="localApprovedBy == {{ $user['id'] }}"
                                            x-bind:class="localApprovedBy == {{ $user['id'] }} ? 'text-slate-300' : ''">
                                            {{ $user['name'] }}
                                        </option>
                                    @endforeach
                                </x-form.select>
                            </div>
                            <x-ui.button type="button" variant="sm-add"
                                wire:click="saveConcurred"
                                wire:loading.attr="disabled"
                                wire:target="saveConcurred"
                                loading="Saving…">
                                <i class="bx bx-check"></i> Set
                            </x-ui.button>
                        </div>
                    </div>

                </div>
            </div>

            {{-- SECTION 2 — Additional Reviewers --}}
            <div class="px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3">
                    Additional Reviewers <span class="text-[#94a3b8] font-normal normal-case tracking-normal">(Faculty)</span>
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                    {{-- Add form --}}
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <x-form.select x-model="selectedFaculty">
                                    <option value="">Select faculty reviewer…</option>
                                    @foreach ($facultyUsers as $user)
                                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                    @endforeach
                                </x-form.select>
                            </div>
                            <div class="w-32 shrink-0">
                                <x-form.select x-model="selectedRole">
                                    <option value="member">Member</option>
                                    <option value="chair">Chair</option>
                                </x-form.select>
                            </div>
                            <x-ui.button type="button" variant="sm-add"
                                x-on:click="addReviewer()"
                                submitting="addingReviewer" loadingText="Adding…"
                                x-bind:disabled="addingReviewer || !selectedFaculty">
                                <i class="bx bx-plus leading-none"></i> Add
                            </x-ui.button>
                        </div>
                        <p class="text-sm text-[#94a3b8] leading-relaxed">
                            Each reviewer appears in the printed syllabus signature section.
                            <strong>Chair</strong> = CQI Committee Chair (required).
                            <strong>Member</strong> = Committee member (Revision track only).
                        </p>
                    </div>

                    {{-- Reviewer list --}}
                    <div class="space-y-2">
                        @if (count($reviewers) > 0)
                            @foreach ($reviewers as $reviewer)
                                <div wire:key="reviewer-{{ $reviewer['id'] }}"
                                    x-bind:class="removingId === {{ $reviewer['id'] }} ? 'opacity-40 pointer-events-none' : ''"
                                    class="flex items-center justify-between px-3.5 py-2.5
                                           rounded-lg border border-[#e2e8f0] bg-[#f8fafc] transition-opacity duration-150">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                                     bg-[#e2e8f0] text-[#475569] text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($reviewer['user_name'], 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-[#0f172a] truncate">{{ $reviewer['user_name'] }}</p>
                                            <p class="text-xs text-[#94a3b8] truncate">{{ $reviewer['user_email'] }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 ml-2 shrink-0">
                                        <x-feedback-status.status-indicator
                                            :status="$reviewer['status'] === 'approved' ? 'success' : $reviewer['status']"
                                            :label="$reviewer['status'] === 'approved' ? 'Completed' : ucfirst($reviewer['status'])" />

                                        @if (!empty($reviewer['role']))
                                            <span class="text-xs px-1.5 py-0.5 rounded font-semibold
                                                {{ $reviewer['role'] === 'chair' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                                {{ ucfirst($reviewer['role']) }}
                                            </span>
                                        @endif

                                        <button type="button"
                                            x-on:click="removeReviewer({{ $reviewer['id'] }})"
                                            x-bind:disabled="removingId !== null"
                                            class="inline-flex items-center justify-center w-6 h-6 rounded-lg
                                                   text-slate-400 hover:text-rose-600 hover:bg-rose-50
                                                   disabled:opacity-50 transition-colors">
                                            <i x-show="removingId !== {{ $reviewer['id'] }}"
                                               class="bx bx-trash text-sm leading-none"></i>
                                            <svg x-show="removingId === {{ $reviewer['id'] }}" x-cloak
                                                 class="animate-spin h-3.5 w-3.5 text-rose-400"
                                                 viewBox="0 0 24 24" fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <x-feedback-status.empty-state
                                icon="bx-group"
                                title="No additional reviewers"
                                message="Select a faculty member from the dropdown and click Add." />
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>