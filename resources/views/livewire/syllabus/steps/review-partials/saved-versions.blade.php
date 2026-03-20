{{--
    review-partials/saved-versions.blade.php
    ──────────────────────────────────────────
    Variables expected (from ReviewStep):
      $completeVersions — Collection of CompleteSyllabus records ordered by version desc
                          Each record has: version, academic_year, semester, pdf_path,
                          abridged_path (nullable), created_at

    Routes used:
      syllabus.saved.complete.preview   (CompleteSyllabus)
      syllabus.saved.complete.download  (CompleteSyllabus)
      syllabus.saved.abridged.preview   (CompleteSyllabus)   ← NEW
      syllabus.saved.abridged.download  (CompleteSyllabus)   ← NEW

    NOTE: abridged_path and checksum_abridged columns require a migration:
      $table->string('abridged_path')->nullable()->after('pdf_path');
      $table->string('checksum_abridged', 64)->nullable()->after('checksum');
--}}

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
                        // ── Complete ──────────────────────────────────────
                        $completePath = (string) ($version->pdf_path ?? '');
                        $completeIsExt = preg_match('#^https?://#i', $completePath)
                                      || str_starts_with($completePath, '/');
                        $completePreviewUrl  = $completeIsExt
                            ? $completePath
                            : route('syllabus.saved.complete.preview', $version);
                        $completeDownloadUrl = $completeIsExt
                            ? null
                            : route('syllabus.saved.complete.download', $version);

                        // ── Abridged ─────────────────────────────────────
                        $abridgedPath = (string) ($version->abridged_path ?? '');
                        $hasAbridged  = $abridgedPath !== '';
                        $abridgedIsExt = $hasAbridged && (
                            preg_match('#^https?://#i', $abridgedPath)
                            || str_starts_with($abridgedPath, '/')
                        );
                        $abridgedPreviewUrl  = $hasAbridged
                            ? ($abridgedIsExt ? $abridgedPath : route('syllabus.saved.abridged.preview',  $version))
                            : null;
                        $abridgedDownloadUrl = ($hasAbridged && ! $abridgedIsExt)
                            ? route('syllabus.saved.abridged.download', $version)
                            : null;

                        // ── Assessment ───────────────────────────────────
                        $assessmentPath = (string) ($version->evaluation_path ?? '');
                        $hasAssessment  = $assessmentPath !== '';
                        $assessmentIsExt = $hasAssessment && (
                            preg_match('#^https?://#i', $assessmentPath)
                            || str_starts_with($assessmentPath, '/')
                        );
                        $assessmentPreviewUrl  = $hasAssessment
                            ? ($assessmentIsExt ? $assessmentPath : route('syllabus.saved.assessment.preview', $version))
                            : null;
                        $assessmentDownloadUrl = ($hasAssessment && ! $assessmentIsExt)
                            ? route('syllabus.saved.assessment.download', $version)
                            : null;
                    @endphp

                    <div class="rounded-xl border border-emerald-200 bg-white/70 p-3">
                        {{-- ── Version header ──────────────────────────── --}}
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div>
                                <p class="text-sm font-semibold text-emerald-800">
                                    Version v{{ $version->version }}
                                </p>
                                <p class="text-xs text-slate-600">
                                    {{ $version->academic_year }} &middot; {{ $version->semester }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Saved {{ $version->created_at?->format('M d, Y  H:i') }}
                                </p>
                            </div>
                        </div>

                        {{-- ── Two columns: Complete | Abridged | Assessment ── --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">

                            {{-- Complete --}}
                            <div class="rounded-lg border border-slate-200 bg-slate-50/60 p-2.5">
                                <p class="text-[10px] font-semibold uppercase tracking-widest
                                          text-slate-500 mb-2">
                                    Complete (OBTL)
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    <a href="{{ $completePreviewUrl }}"
                                       target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                              bg-emerald-600 text-white text-xs font-semibold
                                              hover:bg-emerald-700 transition-colors">
                                        <i class="bx bx-link-external text-sm"></i> Open
                                    </a>
                                    @if ($completeDownloadUrl)
                                        <a href="{{ $completeDownloadUrl }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                                  bg-white text-emerald-700 text-xs font-semibold
                                                  ring-1 ring-emerald-200
                                                  hover:bg-emerald-50 transition-colors">
                                            <i class="bx bx-download text-sm"></i> Download
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Abridged --}}
                            <div class="rounded-lg border border-slate-200 bg-slate-50/60 p-2.5">
                                <p class="text-[10px] font-semibold uppercase tracking-widest
                                          text-slate-500 mb-2">
                                    Abridged (Student)
                                </p>
                                @if ($hasAbridged)
                                    <div class="flex flex-wrap gap-1.5">
                                        <a href="{{ $abridgedPreviewUrl }}"
                                           target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                                  bg-blue-600 text-white text-xs font-semibold
                                                  hover:bg-blue-700 transition-colors">
                                            <i class="bx bx-link-external text-sm"></i> Open
                                        </a>
                                        @if ($abridgedDownloadUrl)
                                            <a href="{{ $abridgedDownloadUrl }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                                      bg-white text-blue-700 text-xs font-semibold
                                                      ring-1 ring-blue-200
                                                      hover:bg-blue-50 transition-colors">
                                                <i class="bx bx-download text-sm"></i> Download
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 italic">
                                        Not available for this version.
                                        Save a new version to generate one.
                                    </p>
                                @endif
                            </div>

                            {{-- Assessment Plan --}}
                            <div class="rounded-lg border border-slate-200 bg-slate-50/60 p-2.5">
                                <p class="text-[10px] font-semibold uppercase tracking-widest
                                          text-slate-500 mb-2">
                                    Assessment Plan
                                </p>
                                @if ($hasAssessment)
                                    <div class="flex flex-wrap gap-1.5">
                                        <a href="{{ $assessmentPreviewUrl }}"
                                           target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                                  bg-violet-600 text-white text-xs font-semibold
                                                  hover:bg-violet-700 transition-colors">
                                            <i class="bx bx-link-external text-sm"></i> Open
                                        </a>
                                        @if ($assessmentDownloadUrl)
                                            <a href="{{ $assessmentDownloadUrl }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                                      bg-white text-violet-700 text-xs font-semibold
                                                      ring-1 ring-violet-200
                                                      hover:bg-violet-50 transition-colors">
                                                <i class="bx bx-download text-sm"></i> Download
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 italic">
                                        Not available for this version.
                                        Save a new version to generate one.
                                    </p>
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </x-wizard.info-card>
    @else
        <x-empty-state
            icon="cloud-upload"
            title="No Saved Versions"
            message="Click 'Save as Done' to create an immutable snapshot of this syllabus." />
    @endif
</x-wizard.section>