<div>
    <x-wizard.step-header
        title="Review & Submit"
        description="Review all details before submitting for approval." />

    <div class="space-y-6">

        {{-- ── Previews ──────────────────────────────────────────────────── --}}
        <x-wizard.section title="Previews" icon="show" color="slate">
            <div class="flex flex-col sm:flex-row gap-2">
                <x-button
                    href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}"
                    variant="outline"
                    target="_blank"
                    rel="noopener"
                    class="flex-1 justify-center">
                <i class="bx bx-file-blank"></i> Complete
                </x-button>

                <x-button
                    href="{{ route('syllabus.preview.abridged', ['syllabus' => $syllabus->id]) }}"
                    variant="outline"
                    target="_blank"
                    rel="noopener"
                    class="flex-1 justify-center">
                    <i class="bx bx-file"></i> Abridged
                </x-button>

                <x-button
                    href="{{ route('syllabus.preview.assessment', ['syllabus' => $syllabus->id]) }}"
                    variant="outline"
                    target="_blank"
                    rel="noopener"
                    class="flex-1 justify-center">
                    <i class="bx bx-clipboard"></i> Assessment Plan
                </x-button>
            </div>
        </x-wizard.section>

        <x-wizard.section title="Revision History" icon="history" color="slate">
            <div class="space-y-3">
                @foreach ($revisions as $rIdx => $revision)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4" wire:key="rev-{{ $rIdx }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <x-form.label for="rev-{{ $rIdx }}-no">Revision No.</x-form.label>
                                <input type="number"
                                    wire:model="revisions.{{ $rIdx }}.revision_no"
                                    id="rev-{{ $rIdx }}-no"
                                    readonly
                                    class="w-full text-sm rounded-lg border border-slate-300 bg-slate-100 px-3 py-2" />
                            </div>

                            <div>
                                <x-form.label for="rev-{{ $rIdx }}-date">Revision Date</x-form.label>
                                <input type="date"
                                    wire:model="revisions.{{ $rIdx }}.revision_date"
                                    id="rev-{{ $rIdx }}-date"
                                    class="w-full text-sm rounded-lg border border-slate-300 bg-white px-3 py-2
                                           focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none" />
                            </div>

                            <div class="md:col-span-2">
                                <x-form.label for="rev-{{ $rIdx }}-semester">Implementation Semester *</x-form.label>
                                <input type="text"
                                    wire:model="revisions.{{ $rIdx }}.implementation_semester"
                                    id="rev-{{ $rIdx }}-semester"
                                    placeholder="e.g., 1st Sem 2025-2026"
                                    class="w-full text-sm rounded-lg border border-slate-300 bg-white px-3 py-2
                                           focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none
                                           placeholder:text-slate-300" />
                            </div>

                            <div class="md:col-span-2">
                                <x-form.label for="rev-{{ $rIdx }}-highlights">Highlights</x-form.label>
                                <textarea
                                    wire:model="revisions.{{ $rIdx }}.highlights"
                                    id="rev-{{ $rIdx }}-highlights"
                                    rows="2"
                                    placeholder="Brief summary of changes..."
                                    class="w-full text-sm rounded-lg border border-slate-300 bg-white px-3 py-2
                                           focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none
                                           placeholder:text-slate-300"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <x-form.label for="rev-{{ $rIdx }}-contributors">Contributors</x-form.label>
                                <textarea
                                    wire:model="revisions.{{ $rIdx }}.contributors"
                                    id="rev-{{ $rIdx }}-contributors"
                                    rows="2"
                                    placeholder="Names of contributors..."
                                    class="w-full text-sm rounded-lg border border-slate-300 bg-white px-3 py-2
                                           focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none
                                           placeholder:text-slate-300"></textarea>
                            </div>
                        </div>

                        @if (count($revisions) > 1)
                            <div class="mt-3 flex justify-end">
                                <button type="button"
                                    wire:click="removeRevision({{ $rIdx }})"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium
                                           text-rose-600 hover:text-rose-700 hover:bg-rose-50
                                           rounded-lg transition-colors">
                                    <i class="bx bx-trash text-sm"></i> Remove
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="flex gap-2">
                    <x-button variant="sm-primary" wire:click="addRevision">
                        <i class="bx bx-plus text-sm"></i> Add Revision
                    </x-button>

                    <x-button variant="sm-secondary" wire:click="saveRevisions">
                        <i class="bx bx-save text-sm"></i> Save Revisions
                    </x-button>
                </div>
            </div>
        </x-wizard.section>

        <x-wizard.section title="Review & Approval" icon="user-check" color="slate">
            <div class="space-y-4">
                {{-- Prepared By (Auto-filled) --}}
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <x-form.label>
                        <i class="bx bx-user text-blue-600"></i> Prepared By
                    </x-form.label>
                    <p class="text-sm font-medium text-slate-800 mt-1">
                        {{ $syllabus->preparer->name ?? 'N/A' }}
                    </p>
                    <p class="text-xs text-slate-600">
                        {{ $syllabus->preparer->email ?? '' }}
                    </p>
                </div>

                {{-- Concurred By (Chair - Manual) --}}
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <x-form.label for="concurred">Concurred By (Department Chair)</x-form.label>
                    <select
                        wire:model="syllabus.concurred_by"
                        id="concurred"
                        class="w-full text-sm rounded-lg border border-slate-300 bg-white px-3 py-2
                               focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none">
                        <option value="">Select Chair...</option>
                        @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Approved By (Dean - Manual) --}}
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <x-form.label for="approved">Approved By (Dean)</x-form.label>
                    <select
                        wire:model="syllabus.approved_by"
                        id="approved"
                        class="w-full text-sm rounded-lg border border-slate-300 bg-white px-3 py-2
                               focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none">
                        <option value="">Select Dean...</option>
                        @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Additional Reviewers --}}
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <x-form.label>
                            <i class="bx bx-group text-slate-500"></i> Additional Reviewers
                        </x-form.label>
                    </div>

                    {{-- Add Reviewer Form --}}
                    <div class="flex gap-2 mb-3">
                        <select
                            wire:model="selectedReviewerId"
                            class="flex-1 text-sm rounded-lg border border-slate-300 bg-white px-3 py-2
                                   focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none">
                            <option value="">Select reviewer...</option>
                            @foreach ($allUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <x-button variant="sm-primary" wire:click="addReviewer">
                            <i class="bx bx-plus text-sm"></i> Add
                        </x-button>
                    </div>

                    {{-- Reviewers List --}}
                    @if (count($reviewers) > 0)
                        <div class="space-y-2">
                            @foreach ($reviewers as $reviewer)
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-slate-200"
                                     wire:key="reviewer-{{ $reviewer['id'] }}">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-800">
                                            {{ $reviewer['user_name'] }}
                                        </p>
                                        <p class="text-xs text-slate-600">
                                            {{ $reviewer['user_email'] }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <select
                                            wire:change="updateReviewerStatus({{ $reviewer['id'] }}, $event.target.value)"
                                            class="text-xs rounded-lg border border-slate-300 px-2 py-1">
                                            <option value="pending" {{ $reviewer['status'] === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $reviewer['status'] === 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ $reviewer['status'] === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>

                                        <button type="button"
                                            wire:click="removeReviewer({{ $reviewer['id'] }})"
                                            class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50
                                                   rounded-md transition-colors"
                                            title="Remove">
                                            <i class="bx bx-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 text-center py-2">No additional reviewers added yet.</p>
                    @endif
                </div>
            </div>
        </x-wizard.section>

        {{-- ── Saved versions ────────────────────────────────────────────── --}}
        <x-wizard.section title="Saved Versions" icon="cloud-upload" color="emerald">
            @if (isset($completeVersions) && $completeVersions->count() > 0)
                <x-wizard.info-card color="emerald">
                    <x-wizard.info-row
                        label="Total saved versions"
                        :value="$completeVersions->count()"
                        bold />

                    <div class="mt-3 space-y-3">
                        @foreach ($completeVersions as $version)
                            @php
                                $savedPath  = (string) ($version->pdf_path ?? '');
                                $isExternal = preg_match('#^https?://#i', $savedPath) || str_starts_with($savedPath, '/');

                                $previewUrl  = $isExternal
                                    ? $savedPath
                                    : route('syllabus.saved.complete.preview', $version);
                                $downloadUrl = $isExternal
                                    ? null
                                    : route('syllabus.saved.complete.download', $version);
                            @endphp

                            <div class="rounded-xl border border-emerald-200 bg-white/70 p-3">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="space-y-1">
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
                                        <a href="{{ $previewUrl }}"
                                           target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-2 px-3 py-2 rounded-lg
                                                  bg-emerald-600 text-white text-xs font-semibold shadow-sm
                                                  hover:bg-emerald-700 transition-colors">
                                            <i class="bx bx-link-external text-base"></i>
                                            Open
                                        </a>

                                        @if ($downloadUrl)
                                            <a href="{{ $downloadUrl }}"
                                               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg
                                                      bg-white text-emerald-700 text-xs font-semibold shadow-sm ring-1 ring-emerald-200
                                                      hover:bg-emerald-50 transition-colors">
                                                <i class="bx bx-download text-base"></i>
                                                Download HTML
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

    {{-- ────────────────────────────────────────────────────────────────────────
         Save as Done button
         ─────────────────────────────────────────────────────────────────────────
         WHY Alpine instead of wire:loading here:
         saveAsDone() lives on SyllabusWizard (the PARENT Livewire component).
         wire:loading only watches the current component's requests — it cannot
         track a request fired on the parent. So we use Alpine local state:

           1. Click → x-on:click sets saving=true (spinner appears immediately)
                    → $dispatch('wizard-save-as-done') fires a browser event
           2. SyllabusWizard hears the event via #[On('wizard-save-as-done')]
              and runs saveAsDone() (freezes an immutable version snapshot, saves, toasts, reloads step)
           3. 'syllabus-step-changed' event causes this component to re-render,
              which resets saving=false via the @wizard-save-done.window listener.

         Do NOT add a saveAsDone() method to ReviewStep — it will be called
         instead of the wizard's, and will spin forever (method not found or
         version freezing fails silently in the child context).
    --}}
    <div class="mt-6 flex flex-wrap items-center gap-4"
         x-data="{ saving: false }"
         x-on:wizard-save-done.window="saving = false">

        <button
            type="button"
            x-on:click="saving = true; $dispatch('wizard-save-as-done')"
            x-bind:disabled="saving"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                   bg-emerald-600 text-white text-sm font-semibold shadow-sm
                   hover:bg-emerald-700 active:bg-emerald-800
                   disabled:opacity-60 disabled:cursor-not-allowed
                   transition-colors duration-150">

            {{-- Idle label --}}
            <span x-show="!saving" class="inline-flex items-center gap-2">
                <i class="bx bx-save text-base"></i>
                Save as Done
            </span>

            {{-- Spinner while saving --}}
            <span x-show="saving" x-cloak class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Freezing version…
            </span>
        </button>

        <p x-show="saving" x-cloak class="text-xs text-slate-500 animate-pulse">
            Creating an immutable saved version — this may take a few seconds.
        </p>
    </div>

    {{-- ── Warning notice ────────────────────────────────────────────────── --}}
    <div class="mt-6">
        <x-feedback-status.alert
            type="warning"
            title="Before you submit"
            message="Once you submit, the syllabus will be sent for review by the department chair. Make sure all information is correct." />
    </div>
</div>
