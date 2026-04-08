{{--
    review-partials/saved-versions.blade.php
--}}
<div
    x-data="{ open: false }"
    class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden"
    style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

    {{-- ── Header ── --}}
    <button type="button" x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left
               hover:bg-[#f8fafc] transition-colors focus:outline-none">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#dcfce7] text-[#16a34a]">
                <i class="bx bx-cloud-upload text-base leading-none"></i>
            </span>
            <div>
                <p class="text-[13px] font-bold text-[#0f172a]">Saved Versions</p>
                <p class="text-[11px] text-[#94a3b8] mt-0.5">
                    @if (isset($completeVersions) && $completeVersions->count() > 0)
                        {{ $completeVersions->count() }} {{ Str::plural('version', $completeVersions->count()) }} saved
                    @else
                        No versions yet
                    @endif
                </p>
            </div>
        </div>
        <i class="bx text-[#94a3b8] text-lg transition-transform duration-200"
           x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
    </button>

    {{-- ── Body ── --}}
    <div x-show="open" x-collapse>
        <div class="border-t border-[#e2e8f0] p-4 space-y-2">

            @if (isset($completeVersions) && $completeVersions->count() > 0)
                @foreach ($completeVersions as $sv)
                    @php
                        $completePath     = (string) ($sv->pdf_path ?? '');
                        $completeIsExt    = preg_match('#^https?://#i', $completePath) || str_starts_with($completePath, '/');
                        $completePreview  = $completeIsExt ? $completePath : route('syllabus.saved.complete.preview',  $sv);
                        $completeDownload = $completeIsExt ? null          : route('syllabus.saved.complete.download', $sv);

                        $abridgedPath     = (string) ($sv->abridged_path ?? '');
                        $hasAbridged      = $abridgedPath !== '';
                        $abridgedIsExt    = $hasAbridged && (preg_match('#^https?://#i', $abridgedPath) || str_starts_with($abridgedPath, '/'));
                        $abridgedPreview  = $hasAbridged ? ($abridgedIsExt ? $abridgedPath : route('syllabus.saved.abridged.preview',  $sv)) : null;
                        $abridgedDownload = ($hasAbridged && ! $abridgedIsExt) ? route('syllabus.saved.abridged.download', $sv) : null;

                        $assessPath     = (string) ($sv->evaluation_path ?? '');
                        $hasAssess      = $assessPath !== '';
                        $assessIsExt    = $hasAssess && (preg_match('#^https?://#i', $assessPath) || str_starts_with($assessPath, '/'));
                        $assessPreview  = $hasAssess ? ($assessIsExt ? $assessPath : route('syllabus.saved.assessment.preview',  $sv)) : null;
                        $assessDownload = ($hasAssess && ! $assessIsExt) ? route('syllabus.saved.assessment.download', $sv) : null;
                    @endphp

                    <div
                        wire:key="sv-{{ $sv->id }}"
                        x-data="{ vopen: {{ $loop->first ? 'true' : 'false' }} }"
                        class="rounded-xl border border-[#e2e8f0] overflow-hidden">

                        {{-- Version row header --}}
                        <button type="button" x-on:click="vopen = !vopen"
                            class="w-full flex items-center justify-between px-4 py-3 text-left
                                   bg-[#f8fafc] hover:bg-[#f0fdf4] transition-colors focus:outline-none border-b border-[#e2e8f0]">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md
                                             bg-[#16a34a] text-white text-[11px] font-bold shrink-0">
                                    v{{ $sv->version }}
                                </span>
                                <div>
                                    <p class="text-[13px] font-semibold text-[#0f172a]">
                                        Version {{ $sv->version }}
                                        <span class="font-normal text-[#475569] ml-1">
                                            &middot; {{ $sv->academic_year }} {{ $sv->semester }}
                                        </span>
                                    </p>
                                    <p class="text-[11px] text-[#94a3b8] mt-0.5">
                                        Saved {{ $sv->created_at?->format('M d, Y  H:i') }}
                                    </p>
                                </div>
                            </div>
                            <i class="bx text-[#94a3b8] text-base transition-transform duration-200"
                               x-bind:class="vopen ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                        </button>

                        <div x-show="vopen" x-collapse>
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-3 gap-3 bg-white">

                                {{-- Complete --}}
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-2">
                                        Complete (OBTL)
                                    </p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <a href="{{ $completePreview }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg
                                                  bg-[#16a34a] text-white text-[13px] font-semibold
                                                  hover:bg-[#15803d] transition-colors">
                                            <i class="bx bx-link-external text-sm"></i> Open
                                        </a>
                                        @if ($completeDownload)
                                            <a href="{{ $completeDownload }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg
                                                      bg-white text-[#166534] text-[13px] font-semibold
                                                      border border-[#bbf7d0] hover:bg-[#f0fdf4] transition-colors">
                                                <i class="bx bx-download text-sm"></i> Download
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                {{-- Abridged --}}
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-2">
                                        Abridged (Student)
                                    </p>
                                    @if ($hasAbridged)
                                        <div class="flex flex-wrap gap-1.5">
                                            <a href="{{ $abridgedPreview }}" target="_blank" rel="noopener"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg
                                                      bg-[#1e40af] text-white text-[13px] font-semibold
                                                      hover:bg-[#1d4ed8] transition-colors">
                                                <i class="bx bx-link-external text-sm"></i> Open
                                            </a>
                                            @if ($abridgedDownload)
                                                <a href="{{ $abridgedDownload }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg
                                                          bg-white text-[#1e40af] text-[13px] font-semibold
                                                          border border-[#bfdbfe] hover:bg-[#eff6ff] transition-colors">
                                                    <i class="bx bx-download text-sm"></i> Download
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-[13px] text-[#94a3b8] italic">Not available.</p>
                                    @endif
                                </div>

                                {{-- Assessment --}}
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-2">
                                        Assessment Plan
                                    </p>
                                    @if ($hasAssess)
                                        <div class="flex flex-wrap gap-1.5">
                                            <a href="{{ $assessPreview }}" target="_blank" rel="noopener"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg
                                                      bg-[#475569] text-white text-[13px] font-semibold
                                                      hover:bg-[#334155] transition-colors">
                                                <i class="bx bx-link-external text-sm"></i> Open
                                            </a>
                                            @if ($assessDownload)
                                                <a href="{{ $assessDownload }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg
                                                          bg-white text-[#475569] text-[13px] font-semibold
                                                          border border-[#e2e8f0] hover:bg-[#f8fafc] transition-colors">
                                                    <i class="bx bx-download text-sm"></i> Download
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-[13px] text-[#94a3b8] italic">Not available.</p>
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

        {{-- Footer: Save as Done --}}
        <div class="border-t border-[#e2e8f0] px-5 py-3 flex justify-end">
            <x-button
                type="button"
                variant="add-button"
                wire:click="$parent.saveAsDone"
                wire:loading.attr="disabled"
                wire:target="saveAsDone">
                <span wire:loading.remove wire:target="saveAsDone" class="inline-flex items-center gap-1.5">
                    <i class="bx bx-save text-base leading-none"></i> Save as Done
                </span>
                <span wire:loading wire:target="saveAsDone" class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Saving…
                </span>
            </x-button>
        </div>
    </div>
</div>
