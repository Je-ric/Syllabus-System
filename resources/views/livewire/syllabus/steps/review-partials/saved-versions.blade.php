{{--
    review-partials/saved-versions.blade.php
    ──────────────────────────────────────────
    Accordion card matching the reviewers/revisions pattern.
    Save as Done runs synchronously via saveAsDone() on SyllabusWizard —
    wire:loading on wire:target="saveAsDone" handles all loading states.
--}}
<div
    x-data="{ open: true }"
    class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

    {{-- ── Header ── --}}
    <button type="button" x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left
               hover:bg-slate-50 transition-colors focus:outline-none">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600">
                <i class="bx bx-cloud-upload text-lg leading-none"></i>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-800">Saved Versions</p>
                <p class="text-xs text-slate-400 mt-0.5">
                    @if (isset($completeVersions) && $completeVersions->count() > 0)
                        {{ $completeVersions->count() }} {{ Str::plural('version', $completeVersions->count()) }} saved
                    @else
                        No versions yet
                    @endif
                </p>
            </div>
        </div>
        <i class="bx text-slate-400 text-xl transition-transform duration-200"
           x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
    </button>

    {{-- ── Body ── --}}
    <div x-show="open" x-collapse>
        <div class="border-t border-slate-100 p-4 space-y-2">

            @if (isset($completeVersions) && $completeVersions->count() > 0)
                @foreach ($completeVersions as $sv)
                    @php
                        // ── Complete ──────────────────────────────────────
                        $completePath     = (string) ($sv->pdf_path ?? '');
                        $completeIsExt    = preg_match('#^https?://#i', $completePath) || str_starts_with($completePath, '/');
                        $completePreview  = $completeIsExt ? $completePath : route('syllabus.saved.complete.preview',  $sv);
                        $completeDownload = $completeIsExt ? null          : route('syllabus.saved.complete.download', $sv);

                        // ── Abridged ──────────────────────────────────────
                        $abridgedPath     = (string) ($sv->abridged_path ?? '');
                        $hasAbridged      = $abridgedPath !== '';
                        $abridgedIsExt    = $hasAbridged && (preg_match('#^https?://#i', $abridgedPath) || str_starts_with($abridgedPath, '/'));
                        $abridgedPreview  = $hasAbridged ? ($abridgedIsExt ? $abridgedPath : route('syllabus.saved.abridged.preview',  $sv)) : null;
                        $abridgedDownload = ($hasAbridged && ! $abridgedIsExt) ? route('syllabus.saved.abridged.download', $sv) : null;

                        // ── Assessment ────────────────────────────────────
                        $assessPath     = (string) ($sv->evaluation_path ?? '');
                        $hasAssess      = $assessPath !== '';
                        $assessIsExt    = $hasAssess && (preg_match('#^https?://#i', $assessPath) || str_starts_with($assessPath, '/'));
                        $assessPreview  = $hasAssess ? ($assessIsExt ? $assessPath : route('syllabus.saved.assessment.preview',  $sv)) : null;
                        $assessDownload = ($hasAssess && ! $assessIsExt) ? route('syllabus.saved.assessment.download', $sv) : null;
                    @endphp

                    {{-- Per-version accordion --}}
                    <div
                        wire:key="sv-{{ $sv->id }}"
                        x-data="{ vopen: {{ $loop->first ? 'true' : 'false' }} }"
                        class="rounded-xl border border-slate-200 overflow-hidden">

                        <button type="button" x-on:click="vopen = !vopen"
                            class="w-full flex items-center justify-between px-4 py-3 text-left
                                   bg-slate-50 hover:bg-slate-100 transition-colors focus:outline-none">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md
                                             bg-emerald-100 text-emerald-700 text-[11px] font-bold shrink-0">
                                    v{{ $sv->version }}
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-slate-800">
                                        Version {{ $sv->version }}
                                        <span class="font-normal text-slate-500 ml-1">
                                            · {{ $sv->academic_year }} {{ $sv->semester }}
                                        </span>
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Saved {{ $sv->created_at?->format('M d, Y  H:i') }}
                                    </p>
                                </div>
                            </div>
                            <i class="bx text-slate-400 text-lg transition-transform duration-200"
                               x-bind:class="vopen ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                        </button>

                        <div x-show="vopen" x-collapse>
                            <div class="p-3 grid grid-cols-1 sm:grid-cols-3 gap-2 bg-white">

                                {{-- Complete --}}
                                <div class="rounded-lg border border-slate-200 bg-slate-50/60 p-2.5">
                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-2">
                                        Complete (OBTL)
                                    </p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <a href="{{ $completePreview }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                                  bg-emerald-600 text-white text-xs font-semibold
                                                  hover:bg-emerald-700 transition-colors">
                                            <i class="bx bx-link-external text-sm"></i> Open
                                        </a>
                                        @if ($completeDownload)
                                            <a href="{{ $completeDownload }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                                      bg-white text-emerald-700 text-xs font-semibold
                                                      ring-1 ring-emerald-200 hover:bg-emerald-50 transition-colors">
                                                <i class="bx bx-download text-sm"></i> Download
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                {{-- Abridged --}}
                                <div class="rounded-lg border border-slate-200 bg-slate-50/60 p-2.5">
                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-2">
                                        Abridged (Student)
                                    </p>
                                    @if ($hasAbridged)
                                        <div class="flex flex-wrap gap-1.5">
                                            <a href="{{ $abridgedPreview }}" target="_blank" rel="noopener"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                                      bg-blue-600 text-white text-xs font-semibold
                                                      hover:bg-blue-700 transition-colors">
                                                <i class="bx bx-link-external text-sm"></i> Open
                                            </a>
                                            @if ($abridgedDownload)
                                                <a href="{{ $abridgedDownload }}"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                                          bg-white text-blue-700 text-xs font-semibold
                                                          ring-1 ring-blue-200 hover:bg-blue-50 transition-colors">
                                                    <i class="bx bx-download text-sm"></i> Download
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-400 italic">Not available for this version.</p>
                                    @endif
                                </div>

                                {{-- Assessment --}}
                                <div class="rounded-lg border border-slate-200 bg-slate-50/60 p-2.5">
                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-2">
                                        Assessment Plan
                                    </p>
                                    @if ($hasAssess)
                                        <div class="flex flex-wrap gap-1.5">
                                            <a href="{{ $assessPreview }}" target="_blank" rel="noopener"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                                      bg-violet-600 text-white text-xs font-semibold
                                                      hover:bg-violet-700 transition-colors">
                                                <i class="bx bx-link-external text-sm"></i> Open
                                            </a>
                                            @if ($assessDownload)
                                                <a href="{{ $assessDownload }}"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                                          bg-white text-violet-700 text-xs font-semibold
                                                          ring-1 ring-violet-200 hover:bg-violet-50 transition-colors">
                                                    <i class="bx bx-download text-sm"></i> Download
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-400 italic">Not available for this version.</p>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach

            @else
                <x-empty-state
                    icon="cloud-upload"
                    title="No Saved Versions"
                    message="Click 'Save as Done' to create an immutable snapshot of this syllabus." />
            @endif

        </div>

        {{-- ── Footer: Save as Done — bottom-right ── --}}
        <div class="border-t border-slate-100 px-5 py-3 flex justify-end">
            <x-button
                type="button"
                variant="add-button"
                wire:click="$parent.saveAsDone"
                wire:loading.attr="disabled"
                wire:target="saveAsDone">
                <span wire:loading.remove wire:target="saveAsDone"
                      class="inline-flex items-center gap-1.5">
                    <i class="bx bx-save text-base leading-none"></i> Save as Done
                </span>
                <span wire:loading wire:target="saveAsDone"
                      class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Saving…
                </span>
            </x-button>
        </div>
    </div>
</div>
