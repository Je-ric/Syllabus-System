<div>
    <div class="space-y-4 text-slate-800">

        {{-- Header --}}
        <div class="mb-5">
            <h3 class="text-xl font-semibold text-slate-900">Course Outcomes</h3>
            <p class="text-sm text-slate-500 mt-0.5">
                Define what students should be able to do after completing this course.
            </p>
        </div>

        {{-- ══ CO list ════════════════════════════════════════════════════════ --}}
        <div class="space-y-2.5">
            @foreach($courseOutcomes as $index => $outcome)
                {{--
                    Each row is purely Alpine-driven for text input.
                    We only sync to Livewire on blur (x-on:blur) to avoid
                    a round-trip on every keystroke — no more lag while typing.
                --}}
                <div wire:key="co-row-{{ $outcome['temp_key'] ?? ($outcome['id'] ?? $index) }}"
                     class="flex items-start gap-3 px-4 py-3 border border-slate-200 rounded-xl
                            bg-white shadow-sm transition-shadow hover:shadow-md"
                     x-data="{ text: @js($outcome['description'] ?? '') }">

                    {{-- CO badge --}}
                    <span class="mt-1.5 inline-flex items-center justify-center min-w-11 h-7
                                 rounded-full bg-emerald-100 text-emerald-800
                                 text-xs font-bold tracking-widest uppercase shrink-0">
                        {{ $outcome['co_code'] ?? ('CO' . ($index + 1)) }}
                    </span>

                    {{--
                        Textarea is Alpine-local (x-model="text") while typing — zero Livewire requests.
                        On blur, we push the value into Livewire's $weekInputs via $wire.set().
                        This is the same pattern used by wire:model.lazy but without the Livewire
                        DOM attribute so Alpine controls the element natively.
                    --}}
                    <textarea
                        x-model="text"
                        x-on:blur="$wire.set('courseOutcomes.{{ $index }}.description', text)"
                        rows="2"
                        placeholder="Enter course outcome description…"
                        class="flex-1 resize-none rounded-lg border border-slate-200 bg-slate-50 px-3 py-2
                               text-sm text-slate-800 placeholder:text-slate-300
                               focus:bg-white focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300
                               focus:outline-none transition-colors"></textarea>

                    {{-- Remove button --}}
                    <button type="button"
                        wire:click="removeCourseOutcome({{ $index }})"
                        class="mt-1.5 p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50
                               rounded-lg transition-colors"
                        title="Remove outcome">
                        <i class="bx bx-trash text-base leading-none"></i>
                    </button>
                </div>
            @endforeach

            {{-- Add CO button --}}
            <button type="button"
                wire:click="addCourseOutcome"
                wire:loading.attr="disabled"
                wire:target="addCourseOutcome,removeCourseOutcome"
                class="w-full flex items-center justify-center gap-2 px-4 py-3
                        border-2 border-dashed border-emerald-300 rounded-xl
                        text-sm font-semibold text-emerald-700
                        hover:border-emerald-400 hover:bg-emerald-50
                        disabled:opacity-60 disabled:cursor-not-allowed
                        transition-colors">
                <span wire:loading.remove wire:target="addCourseOutcome">
                    <i class="bx bx-plus text-base"></i> Add Course Outcome
                </span>
                <span wire:loading wire:target="addCourseOutcome">
                    <i class="bx bx-loader-alt bx-spin"></i> Adding…
                </span>
            </button>

            @if($coAddError)
                <p class="text-xs text-red-600 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> {{ $coAddError }}
                </p>
            @endif
        </div>

        {{-- ══ Program Outcomes reference ══════════════════════════════════════ --}}
        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50/40 p-4">
            <h4 class="text-sm font-semibold text-emerald-800 mb-3 flex items-center gap-1.5">
                <i class="bx bx-list-check text-emerald-600"></i>
                Program Outcomes Reference
            </h4>

            @if (count($programOutcomes) === 0)
                <p class="text-sm text-slate-500 italic">No program outcomes found for this course.</p>
            @else
                <div class="space-y-2">
                    @foreach ($programOutcomes as $po)
                        <div class="flex items-start gap-2.5 bg-white border border-emerald-200
                                    rounded-lg px-3 py-2.5 shadow-sm">
                            <span class="shrink-0 font-bold text-emerald-700 text-sm mt-0.5">
                                {{ $po['po_code'] }}.
                            </span>
                            <p class="text-slate-700 text-sm flex-1">
                                {{ $po['po_text'] }}
                            </p>
                            @if (!empty($po['ied']))
                                <x-feedback-status.ied-badge :level="$po['ied']" />
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>