{{--
    Livewire view: livewire/syllabus/syllabus-review-page.blade.php
    Component:    App\Livewire\Syllabus\SyllabusReviewPage
    Partials:     review-page-partials/
--}}
<div>

    {{-- ══ Page header ══════════════════════════════════════════════════════════ --}}
    <x-layout.page-header
        icon="bx-revision"
        title="Review: {{ $syllabus->course->course_code }} — {{ $syllabus->course->course_title }}"
        desc="{{ $isChair ? 'CQI Chair' : 'CQI Member' }} · {{ $syllabus->academicCalendar?->academic_year }} {{ $syllabus->academicCalendar?->semester }}">

        <a href="{{ route('syllabus.review-queue.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#72809E]
                  hover:text-[#394056] transition-colors">
            <i class="bx bx-arrow-back text-sm"></i> Back to Queue
        </a>

        <x-ui.button
            href="{{ route('syllabus.preview.complete', $syllabus) }}"
            variant="cancel"
            target="_blank">
            <i class="bx bx-show text-sm leading-none"></i> Preview Syllabus
        </x-ui.button>

    </x-layout.page-header>

    <x-layout.panel>
        <div class="grid grid-cols-1 xl:grid-cols-[1fr_320px] gap-5">

            {{-- ══ LEFT: checklist ════════════════════════════════════════════ --}}
            <div class="space-y-5">

                {{-- ── F.003 not submitted warning ─────────────────────────── --}}
                @if (! $isSubmitted)
                    <div class="flex items-start gap-3 rounded-xl border border-amber-200
                                bg-amber-50/70 px-4 py-3.5">
                        <i class="bx bx-error text-amber-500 text-lg shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">F.003 not yet submitted by faculty</p>
                            <p class="text-xs text-amber-700 mt-0.5 leading-relaxed">
                                The faculty member has not submitted the review form yet.
                                You can still fill in your checklist, but a classification
                                (Updating / Revision) is needed before saving responses.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- ── Progress bar ─────────────────────────────────────────── --}}
                <x-layout.card-section title="My Checklist Progress" icon="bx-task">
                    <x-slot name="actions">
                        @if ($allResponded)
                            <x-feedback-status.status-indicator variant="emerald" icon="bx bx-check-circle">
                                Complete
                            </x-feedback-status.status-indicator>
                        @else
                            <span class="text-xs font-semibold text-[#72809E]">{{ $progressPct }}%</span>
                        @endif
                    </x-slot>

                    <div class="h-2 rounded-full bg-[#E3E8EB] overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500
                                    {{ $allResponded ? 'bg-[#00965F]' : 'bg-[#F5B126]' }}"
                             style="width: {{ $progressPct }}%"></div>
                    </div>
                    <p class="text-xs text-[#93A1AF] mt-2">
                        Answer every criterion to mark your review as complete.
                        Classification: <strong class="text-[#394056]">{{ ucfirst($classification) }}</strong>
                    </p>
                </x-layout.card-section>

                {{-- ── Criteria sections ────────────────────────────────────── --}}
                @foreach ($criteria as $sectionKey => $section)
                    <x-layout.card-section
                        :title="$section['label']"
                        icon="bx-list-check"
                        :padded="false">

                        <div class="divide-y divide-[#F1F3F5]">
                            @foreach ($section['criteria'] as $code => $text)
                                @php
                                    $current  = $responses[$code]['response'] ?? '';
                                    $comment  = $responses[$code]['comments'] ?? '';
                                    $answered = ! blank($current);
                                @endphp

                                <div wire:key="criterion-{{ $code }}"
                                     x-data="{
                                         response: @js($current),
                                         comments: @js($comment),
                                         saving:   false,
                                         async save(val) {
                                             this.response = val;
                                             this.saving = true;
                                             await $wire.saveResponse(@js($code), val, this.comments);
                                             this.saving = false;
                                         },
                                         async saveComment() {
                                             if (!this.response) return;
                                             this.saving = true;
                                             await $wire.saveResponse(@js($code), this.response, this.comments);
                                             this.saving = false;
                                         }
                                     }"
                                     class="px-4 py-4 transition-colors duration-150
                                            {{ $answered ? 'bg-white' : 'bg-[#FAFDFB]' }}">

                                    <div class="flex items-start gap-3">

                                        <span class="mt-1 shrink-0 w-2 h-2 rounded-full
                                                     {{ $answered ? 'bg-[#00965F]' : 'bg-[#E3E8EB]' }}"></span>

                                        <div class="flex-1 min-w-0">

                                            <div class="flex items-baseline gap-2 flex-wrap">
                                                <span class="text-[11px] font-bold text-[#72809E] font-mono shrink-0">
                                                    {{ $code }}
                                                </span>
                                                <p class="text-[13px] text-[#394056] leading-relaxed">
                                                    {{ $text }}
                                                </p>
                                            </div>

                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach ([
                                                    'satisfied'      => ['label' => 'Satisfied',     'color' => 'emerald'],
                                                    'not_satisfied'  => ['label' => 'Not Satisfied', 'color' => 'rose'],
                                                    'not_applicable' => ['label' => 'N/A',           'color' => 'slate'],
                                                ] as $val => $cfg)
                                                    <button
                                                        type="button"
                                                        x-on:click="save('{{ $val }}')"
                                                        x-bind:disabled="saving"
                                                        x-bind:class="
                                                            response === '{{ $val }}'
                                                                ? 'bg-{{ $cfg['color'] }}-100 border-{{ $cfg['color'] }}-400 text-{{ $cfg['color'] }}-800 shadow-sm'
                                                                : 'bg-white border-[#E3E8EB] text-[#72809E] hover:border-{{ $cfg['color'] }}-300 hover:text-{{ $cfg['color'] }}-700'
                                                        "
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                               rounded-lg border text-xs font-semibold
                                                               transition-all duration-150 disabled:opacity-60
                                                               disabled:cursor-wait">
                                                        <template x-if="saving && response === '{{ $val }}'">
                                                            <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                        stroke="currentColor" stroke-width="4"/>
                                                                <path class="opacity-75" fill="currentColor"
                                                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                            </svg>
                                                        </template>
                                                        <template x-if="!(saving && response === '{{ $val }}')">
                                                            <i x-show="response === '{{ $val }}'"
                                                               class="bx bx-check text-sm leading-none"></i>
                                                        </template>
                                                        {{ $cfg['label'] }}
                                                    </button>
                                                @endforeach
                                            </div>

                                            <div x-show="response !== ''" x-cloak class="mt-2">
                                                <textarea
                                                    x-model="comments"
                                                    x-on:blur="saveComment()"
                                                    rows="2"
                                                    placeholder="Optional comment…"
                                                    class="w-full text-xs rounded-lg border border-[#E3E8EB]
                                                           px-3 py-2 text-[#394056] placeholder:text-[#C1C8D4]
                                                           focus:outline-none focus:border-[#00C075]
                                                           focus:ring-1 focus:ring-[#00C075]/30
                                                           resize-none transition-colors"></textarea>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </x-layout.card-section>
                @endforeach

            </div>

            {{-- ══ RIGHT: sidebar ═════════════════════════════════════════════ --}}
            <div class="space-y-5">

                @include('livewire.syllabus.review-page.partials.syllabus-info')

                @include('livewire.syllabus.review-page.partials.reviewer-status')

            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            @if ($isChair)
                @include('livewire.syllabus.review-page.partials.chair-decision')
            @endif

            @if ($reviewForm?->approved_by_dean_id)
                @include('livewire.syllabus.review-page.partials.dean-approval')
            @endif
        </div>
        
    </x-layout.panel>

</div>
