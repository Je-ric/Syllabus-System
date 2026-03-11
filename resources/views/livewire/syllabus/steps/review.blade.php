<div>
    <x-wizard.step-header
        title="Review & Submit"
        description="Review all details before submitting for approval." />

    <div class="space-y-4">

        {{-- ── Previews ──────────────────────────────────────────────────── --}}
        <x-wizard.section title="Previews" icon="show" color="slate">
            <div class="flex flex-col sm:flex-row gap-2">
                <x-button href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}"
                    variant="outline" target="_blank" rel="noopener" class="flex-1 justify-center">
                    <i class="bx bx-file-blank"></i> Complete
                </x-button>
                <x-button href="{{ route('syllabus.preview.abridged', ['syllabus' => $syllabus->id]) }}"
                    variant="outline" target="_blank" rel="noopener" class="flex-1 justify-center">
                    <i class="bx bx-file"></i> Abridged
                </x-button>
                <x-button href="{{ route('syllabus.preview.assessment', ['syllabus' => $syllabus->id]) }}"
                    variant="outline" target="_blank" rel="noopener" class="flex-1 justify-center">
                    <i class="bx bx-clipboard"></i> Assessment Plan
                </x-button>
            </div>
        </x-wizard.section>

        {{-- ══════════════════════════════════════════════════════════════════
             ACCORDION 1 — REVISION HISTORY
             Auto-saves when accordion is closed if there are unsaved changes.
             saveRevisions() lives on ReviewStep (no $parent needed).
        ══════════════════════════════════════════════════════════════════════ --}}
        <div x-data="{ open: true, dirty: false }"
             x-on:revision-dirty.window="dirty = true"
             class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            {{-- Header / toggle --}}
            <button type="button"
                x-on:click="
                    if (open && dirty) {
                        $wire.saveRevisions();
                        dirty = false;
                    }
                    open = !open
                "
                class="w-full flex items-center justify-between px-5 py-4
                       text-left hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg
                                 bg-slate-100 text-slate-600">
                        <i class="bx bx-history text-lg"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Revision History</p>
                        <p class="text-xs text-slate-500">
                            {{ count($revisions) }} {{ Str::plural('entry', count($revisions)) }}
                            <span x-show="dirty" x-cloak class="text-amber-500 font-medium ml-1">
                                · unsaved changes
                            </span>
                        </p>
                    </div>
                </div>
                <i class="bx text-slate-400 text-xl transition-transform duration-200"
                   x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
            </button>

            {{-- Body --}}
            <div x-show="open" x-collapse>
                <div class="border-t border-slate-100 p-5">

                    {{-- Two-column: form (left) | list (right) --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        {{-- ── LEFT: Add / Edit form ── --}}
                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                Revision Entries
                            </p>

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
                                        <div>
                                            <x-form.label for="rev-{{ $rIdx }}-date" class="text-xs">
                                                Date
                                            </x-form.label>
                                            <x-form.input type="date"
                                                wire:model="revisions.{{ $rIdx }}.revision_date"
                                                x-on:change="$dispatch('revision-dirty')"
                                                id="rev-{{ $rIdx }}-date"
                                                class="text-xs" />
                                        </div>
                                        <div>
                                            <x-form.label for="rev-{{ $rIdx }}-no" class="text-xs">
                                                Rev. No.
                                            </x-form.label>
                                            <x-form.input type="number"
                                                wire:model="revisions.{{ $rIdx }}.revision_no"
                                                id="rev-{{ $rIdx }}-no"
                                                readonly class="bg-slate-100 text-xs" />
                                        </div>
                                    </div>

                                    <div>
                                        <x-form.label for="rev-{{ $rIdx }}-semester" class="text-xs">
                                            Implementation Semester *
                                        </x-form.label>
                                        <x-form.input type="text"
                                            wire:model="revisions.{{ $rIdx }}.implementation_semester"
                                            x-on:input="$dispatch('revision-dirty')"
                                            id="rev-{{ $rIdx }}-semester"
                                            placeholder="e.g., 1st Sem 2025-2026"
                                            class="text-xs" />
                                    </div>

                                    <div>
                                        <x-form.label for="rev-{{ $rIdx }}-highlights" class="text-xs">
                                            Highlights
                                        </x-form.label>
                                        <x-form.textarea
                                            wire:model="revisions.{{ $rIdx }}.highlights"
                                            x-on:input="$dispatch('revision-dirty')"
                                            id="rev-{{ $rIdx }}-highlights"
                                            rows="2"
                                            placeholder="Brief summary of changes…"
                                            class="text-xs" />
                                    </div>

                                    <div>
                                        <x-form.label for="rev-{{ $rIdx }}-contributors" class="text-xs">
                                            Contributors
                                        </x-form.label>
                                        <x-form.textarea
                                            wire:model="revisions.{{ $rIdx }}.contributors"
                                            x-on:input="$dispatch('revision-dirty')"
                                            id="rev-{{ $rIdx }}-contributors"
                                            rows="2"
                                            placeholder="Names of contributors…"
                                            class="text-xs" />
                                    </div>
                                </div>
                            @endforeach

                            {{-- Add row button --}}
                            <button type="button"
                                wire:click="addRevision"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                                       rounded-xl border-2 border-dashed border-slate-300
                                       text-sm text-slate-500 hover:border-slate-400
                                       hover:text-slate-700 transition-colors">
                                <i class="bx bx-plus"></i> Add Revision Entry
                            </button>
                        </div>

                        {{-- ── RIGHT: Saved list summary ── --}}
                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                Saved Revisions
                            </p>

                            @if ($revisions && count(array_filter($revisions, fn($r) => !empty($r['id']))) > 0)
                                <div class="space-y-2">
                                    @foreach ($revisions as $rev)
                                        @if (!empty($rev['id']))
                                            <div class="rounded-xl border border-slate-200 bg-white p-3">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div>
                                                        <p class="text-xs font-semibold text-slate-700">
                                                            Rev. #{{ $rev['revision_no'] }}
                                                            <span class="font-normal text-slate-500 ml-1">
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
                                                                 rounded text-xs bg-emerald-100 text-emerald-700">
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
                                </div>
                            @endif

                            {{-- Save button --}}
                            <button type="button"
                                wire:click="saveRevisions"
                                x-on:click="dirty = false"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                                       rounded-xl bg-slate-700 text-white text-sm font-semibold
                                       hover:bg-slate-800 transition-colors">
                                <i class="bx bx-save"></i> Save Revisions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             ACCORDION 2 — REVIEW & APPROVAL
             Three sub-sections:
               1. Approved By    — exactly 1 dean
               2. Concurred By   — exactly 1 dean
               3. Reviewed By    — N additional reviewers (syllabus_reviewers table)
             Auto-saves concurred/approved on accordion close.
             addReviewer/removeReviewer still go via $parent on SyllabusWizard.
        ══════════════════════════════════════════════════════════════════════ --}}
        <div x-data="{
                open: false,
                concurredDirty: false,
                approvedDirty: false,
                handleClose() {
                    if (this.concurredDirty) { $wire.saveConcurred(); this.concurredDirty = false; }
                    if (this.approvedDirty)  { $wire.saveApproved();  this.approvedDirty  = false; }
                    this.open = false;
                }
             }"
             class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            {{-- Header / toggle --}}
            <button type="button"
                x-on:click="open ? handleClose() : (open = true)"
                class="w-full flex items-center justify-between px-5 py-4
                       text-left hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg
                                 bg-blue-100 text-blue-600">
                        <i class="bx bx-user-check text-lg"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Review & Approval</p>
                        <p class="text-xs text-slate-500">
                            Signatures, concurrence &amp; additional reviewers
                        </p>
                    </div>
                </div>
                <i class="bx text-slate-400 text-xl transition-transform duration-200"
                   x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
            </button>

            {{-- Body --}}
            <div x-show="open" x-collapse>
                <div class="border-t border-slate-100 p-5 space-y-6">

                    {{-- ── Prepared By (read-only) ── --}}
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full
                                     bg-blue-200 text-blue-700 shrink-0">
                            <i class="bx bx-user text-sm"></i>
                        </span>
                        <div>
                            <p class="text-xs text-blue-500 font-medium uppercase tracking-wide">Prepared By</p>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $syllabus->preparer->name ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $syllabus->preparer->email ?? '' }}</p>
                        </div>
                    </div>

                    {{-- ══ TWO-COLUMN: Approved + Concurred ══ --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        {{-- ── Approved By (Dean) — left ── --}}
                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                Approved By (Dean)
                            </p>

                            {{-- Current value display --}}
                            @if ($approvedUser)
                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3
                                            flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="flex items-center justify-center w-7 h-7 rounded-full
                                                     bg-emerald-200 text-emerald-700 shrink-0 text-xs font-bold">
                                            {{ strtoupper(substr($approvedUser->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold text-slate-800 truncate">
                                                {{ $approvedUser->name }}
                                            </p>
                                            <p class="text-xs text-slate-500 truncate">
                                                {{ $approvedUser->email }}
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button"
                                        wire:click="clearApproved"
                                        class="shrink-0 text-rose-400 hover:text-rose-600 transition-colors">
                                        <i class="bx bx-x text-base"></i>
                                    </button>
                                </div>
                            @else
                                <div class="rounded-xl border border-dashed border-slate-200 p-4 text-center">
                                    <i class="bx bx-user text-xl text-slate-300"></i>
                                    <p class="text-xs text-slate-400 mt-1">No dean assigned yet.</p>
                                </div>
                            @endif

                            {{-- Picker --}}
                            <div class="flex gap-2">
                                <x-form.select
                                    wire:model="approvedBy"
                                    x-on:change="approvedDirty = true"
                                    class="flex-1 text-xs">
                                    <option value="">Select Dean…</option>
                                    @foreach ($allUsers as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </x-form.select>
                                <button type="button"
                                    wire:click="saveApproved"
                                    x-on:click="approvedDirty = false"
                                    class="shrink-0 flex items-center gap-1 px-3 py-2 rounded-xl
                                           bg-slate-700 text-white text-xs font-semibold
                                           hover:bg-slate-800 transition-colors">
                                    <i class="bx bx-check text-sm"></i> Set
                                </button>
                            </div>
                        </div>

                        {{-- ── Concurred By (Chair) — right ── --}}
                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                Concurred By (Dean of Other College)
                            </p>

                            @if ($concurredUser)
                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3
                                            flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="flex items-center justify-center w-7 h-7 rounded-full
                                                     bg-emerald-200 text-emerald-700 shrink-0 text-xs font-bold">
                                            {{ strtoupper(substr($concurredUser->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold text-slate-800 truncate">
                                                {{ $concurredUser->name }}
                                            </p>
                                            <p class="text-xs text-slate-500 truncate">
                                                {{ $concurredUser->email }}
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button"
                                        wire:click="clearConcurred"
                                        class="shrink-0 text-rose-400 hover:text-rose-600 transition-colors">
                                        <i class="bx bx-x text-base"></i>
                                    </button>
                                </div>
                            @else
                                <div class="rounded-xl border border-dashed border-slate-200 p-4 text-center">
                                    <i class="bx bx-user text-xl text-slate-300"></i>
                                    <p class="text-xs text-slate-400 mt-1">No chair assigned yet.</p>
                                </div>
                            @endif

                            <div class="flex gap-2">
                                <x-form.select
                                    wire:model="concurredBy"
                                    x-on:change="concurredDirty = true"
                                    class="flex-1 text-xs">
                                    <option value="">Select Dean</option>
                                    @foreach ($allUsers as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </x-form.select>
                                <button type="button"
                                    wire:click="saveConcurred"
                                    x-on:click="concurredDirty = false"
                                    class="shrink-0 flex items-center gap-1 px-3 py-2 rounded-xl
                                           bg-slate-700 text-white text-xs font-semibold
                                           hover:bg-slate-800 transition-colors">
                                    <i class="bx bx-check text-sm"></i> Set
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ══ Reviewed By (additional, N slots) ══ --}}
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">
                            Reviewed By (Additional)
                        </p>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            {{-- LEFT: Add form --}}
                            <div class="space-y-2">
                                <div class="flex gap-2">
                                    <x-form.select
                                        wire:model="selectedReviewerId"
                                        class="flex-1 text-xs">
                                        <option value="">Select reviewer…</option>
                                        @foreach ($allUsers as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </x-form.select>
                                    <button type="button"
                                        wire:click="$parent.addReviewer($wire.selectedReviewerId)"
                                        class="shrink-0 flex items-center gap-1 px-3 py-2 rounded-xl
                                               bg-emerald-600 text-white text-xs font-semibold
                                               hover:bg-emerald-700 transition-colors">
                                        <i class="bx bx-plus text-sm"></i> Add
                                    </button>
                                </div>
                                <p class="text-xs text-slate-400">
                                    Multiple reviewers can be added. Each will appear in the signatures section of the printed syllabus.
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
                                    <div class="rounded-xl border border-dashed border-slate-200 p-4 text-center">
                                        <i class="bx bx-group text-xl text-slate-300"></i>
                                        <p class="text-xs text-slate-400 mt-1">
                                            No additional reviewers yet.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Saved Versions ────────────────────────────────────────────── --}}
        <x-wizard.section title="Saved Versions" icon="cloud-upload" color="emerald">
            @if (isset($completeVersions) && $completeVersions->count() > 0)
                <x-wizard.info-card color="emerald">
                    <x-wizard.info-row label="Total saved versions"
                        :value="$completeVersions->count()" bold />

                    <div class="mt-3 space-y-3">
                        @foreach ($completeVersions as $version)
                            @php
                                $savedPath  = (string) ($version->pdf_path ?? '');
                                $isExternal = preg_match('#^https?://#i', $savedPath)
                                           || str_starts_with($savedPath, '/');
                                $previewUrl  = $isExternal
                                    ? $savedPath
                                    : route('syllabus.saved.complete.preview', $version);
                                $downloadUrl = $isExternal
                                    ? null
                                    : route('syllabus.saved.complete.download', $version);
                            @endphp
                            <div class="rounded-xl border border-emerald-200 bg-white/70 p-3">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="space-y-0.5">
                                        <p class="text-sm font-semibold text-emerald-800">
                                            Version v{{ $version->version }}
                                        </p>
                                        <p class="text-xs text-slate-600">
                                            {{ $version->academic_year }} | {{ $version->semester }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            Saved {{ $version->created_at?->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ $previewUrl }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                                  bg-emerald-600 text-white text-xs font-semibold shadow-sm
                                                  hover:bg-emerald-700 transition-colors">
                                            <i class="bx bx-link-external text-sm"></i> Open
                                        </a>
                                        @if ($downloadUrl)
                                            <a href="{{ $downloadUrl }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                                      bg-white text-emerald-700 text-xs font-semibold shadow-sm
                                                      ring-1 ring-emerald-200 hover:bg-emerald-50 transition-colors">
                                                <i class="bx bx-download text-sm"></i> Download
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-wizard.info-card>
            @else
                <p class="text-sm text-slate-500">No saved versions yet for this course.</p>
            @endif
        </x-wizard.section>

    </div>

    {{-- ── Save as Done ───────────────────────────────────────────────────── --}}
    <div class="mt-6 flex flex-wrap items-center gap-4"
         x-data="{ saving: false }"
         x-on:wizard-save-done.window="saving = false">

        <button type="button"
            x-on:click="saving = true; $dispatch('wizard-save-as-done')"
            x-bind:disabled="saving"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                   bg-emerald-600 text-white text-sm font-semibold shadow-sm
                   hover:bg-emerald-700 active:bg-emerald-800
                   disabled:opacity-60 disabled:cursor-not-allowed
                   transition-colors duration-150">
            <span x-show="!saving" class="inline-flex items-center gap-2">
                <i class="bx bx-save text-base"></i> Save as Done
            </span>
            <span x-show="saving" x-cloak class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Freezing version…
            </span>
        </button>

        <p x-show="saving" x-cloak class="text-xs text-slate-500 animate-pulse">
            Creating an immutable saved version — this may take a few seconds.
        </p>
    </div>

    <div class="mt-6">
        <x-feedback-status.alert
            type="warning"
            title="Before you submit"
            message="Once you submit, the syllabus will be sent for review by the department chair. Make sure all information is correct." />
    </div>
</div>
