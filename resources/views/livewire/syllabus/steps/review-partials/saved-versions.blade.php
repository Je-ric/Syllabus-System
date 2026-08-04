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
            <span class="flex items-center justify-center w-8 h-8 rounded-lg" style="background: #dcfce7; color: var(--clsu-green);">
                <i class="bx bx-cloud-upload text-base leading-none"></i>
            </span>
            <div>
                <p class="text-sm font-bold text-[#0f172a]">Saved Versions</p>
                <p class="text-xs text-[#94a3b8] mt-0.5">
                    @if (!empty($completeVersions))
                        {{ count($completeVersions) }} {{ Str::plural('version', count($completeVersions)) }} saved
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

            @if (!empty($completeVersions))
                @foreach ($completeVersions as $sv)
                    @php
                        $completePath     = (string) ($sv['pdf_path'] ?? '');
                        $completeIsExt    = preg_match('#^https?://#i', $completePath) || str_starts_with($completePath, '/');
                        $completePreview  = $completeIsExt ? $completePath : route('syllabus.saved.complete.preview',  $sv['id']);
                        $completeDownload = $completeIsExt ? null          : route('syllabus.saved.complete.download', $sv['id']);

                        $abridgedPath     = (string) ($sv['abridged_path'] ?? '');
                        $hasAbridged      = $abridgedPath !== '';
                        $abridgedIsExt    = $hasAbridged && (preg_match('#^https?://#i', $abridgedPath) || str_starts_with($abridgedPath, '/'));
                        $abridgedPreview  = $hasAbridged ? ($abridgedIsExt ? $abridgedPath : route('syllabus.saved.abridged.preview',  $sv['id'])) : null;
                        $abridgedDownload = ($hasAbridged && ! $abridgedIsExt) ? route('syllabus.saved.abridged.download', $sv['id']) : null;

                        $assessPath     = (string) ($sv['evaluation_path'] ?? '');
                        $hasAssess      = $assessPath !== '';
                        $assessIsExt    = $hasAssess && (preg_match('#^https?://#i', $assessPath) || str_starts_with($assessPath, '/'));
                        $assessPreview  = $hasAssess ? ($assessIsExt ? $assessPath : route('syllabus.saved.assessment.preview',  $sv['id'])) : null;
                        $assessDownload = ($hasAssess && ! $assessIsExt) ? route('syllabus.saved.assessment.download', $sv['id']) : null;

                        $rfPath     = (string) ($sv['review_form_path'] ?? '');
                        $hasRf      = $rfPath !== '';
                        $rfPreview  = $hasRf ? route('syllabus.saved.review-form.preview', $sv['id']) : null;

                        $savedAt = $sv['created_at']
                            ? \Illuminate\Support\Carbon::parse($sv['created_at'])->format('M d, Y  H:i')
                            : '';
                    @endphp

                    <div
                        wire:key="sv-{{ $sv['id'] }}"
                        x-data="{ vopen: {{ $loop->first ? 'true' : 'false' }} }"
                        class="rounded-xl border border-[#e2e8f0] overflow-hidden">

                        {{-- Version row header --}}
                        <button type="button" x-on:click="vopen = !vopen"
                            class="w-full flex items-center justify-between px-4 py-3 text-left
                                   bg-[#f8fafc] hover:bg-[#f0fdf4] transition-colors focus:outline-none border-b border-[#e2e8f0]">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md
                                             text-white text-xs font-bold shrink-0" style="background: var(--clsu-green);">
                                    v{{ $sv['version'] }}
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-[#0f172a]">
                                        Version {{ $sv['version'] }}
                                        <span class="font-normal text-[#475569] ml-1">
                                            &middot; {{ $sv['academic_year'] }} {{ $sv['semester'] }}
                                        </span>
                                    </p>
                                    <p class="text-xs text-[#94a3b8] mt-0.5">
                                        Saved {{ $savedAt }}
                                    </p>
                                </div>
                            </div>
                            <i class="bx text-[#94a3b8] text-base transition-transform duration-200"
                               x-bind:class="vopen ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                        </button>

                        <div x-show="vopen" x-collapse>
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 bg-white">

                                {{-- Complete --}}
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#475569] mb-2">
                                        Complete (OBTL)
                                    </p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <x-ui.button href="{{ $completePreview }}" variant="primary" target="_blank" rel="noopener">
                                            <i class="bx bx-link-external text-sm"></i> Open
                                        </x-ui.button>
                                        @if ($completeDownload)
                                            <x-ui.button href="{{ $completeDownload }}" variant="outline">
                                                <i class="bx bx-download text-sm"></i> Download
                                            </x-ui.button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Abridged --}}
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#475569] mb-2">
                                        Abridged (Student)
                                    </p>
                                    @if ($hasAbridged)
                                        <div class="flex flex-wrap gap-1.5">
                                            <x-ui.button href="{{ $abridgedPreview }}" variant="secondary" target="_blank" rel="noopener">
                                                <i class="bx bx-link-external text-sm"></i> Open
                                            </x-ui.button>
                                            @if ($abridgedDownload)
                                                <x-ui.button href="{{ $abridgedDownload }}" variant="cancel">
                                                    <i class="bx bx-download text-sm"></i> Download
                                                </x-ui.button>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-[#94a3b8] italic">Not available.</p>
                                    @endif
                                </div>

                                {{-- Assessment --}}
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#475569] mb-2">
                                        Assessment Plan
                                    </p>
                                    @if ($hasAssess)
                                        <div class="flex flex-wrap gap-1.5">
                                            <x-ui.button href="{{ $assessPreview }}" variant="secondary" target="_blank" rel="noopener">
                                                <i class="bx bx-link-external text-sm"></i> Open
                                            </x-ui.button>
                                            @if ($assessDownload)
                                                <x-ui.button href="{{ $assessDownload }}" variant="cancel">
                                                    <i class="bx bx-download text-sm"></i> Download
                                                </x-ui.button>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-[#94a3b8] italic">Not available.</p>
                                    @endif
                                </div>

                                {{-- F.003 Review Form --}}
                                @if ($hasRf)
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#475569] mb-2">
                                        F.003 Review Form
                                    </p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <x-ui.button href="{{ $rfPreview }}" variant="secondary" target="_blank" rel="noopener">
                                            <i class="bx bx-link-external text-sm"></i> Open
                                        </x-ui.button>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach

            @else
                <x-feedback-status.empty-state
                    icon="cloud-upload"
                    title="No Saved Versions"
                    message="Click 'Save as Done' to create an immutable snapshot of this syllabus." />
            @endif

        </div>
    </div>
</div>
