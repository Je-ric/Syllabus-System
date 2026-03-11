<x-wizard.section title="Saved Versions" icon="cloud-upload" color="emerald">
    @if (isset($completeVersions) && $completeVersions->count() > 0)
        <x-wizard.info-card color="emerald">
            <x-wizard.info-row label="Total saved versions" :value="$completeVersions->count()" bold />

            <div class="mt-3 space-y-3">
                @foreach ($completeVersions as $version)
                    @php
                        $savedPath = (string) ($version->pdf_path ?? '');
                        $isExternal = preg_match('#^https?://#i', $savedPath) || str_starts_with($savedPath, '/');
                        $previewUrl = $isExternal ? $savedPath : route('syllabus.saved.complete.preview', $version);
                        $downloadUrl = $isExternal ? null : route('syllabus.saved.complete.download', $version);
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
