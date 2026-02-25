<div>
    <div class="space-y-4 text-slate-800">
        <div class="mb-4">
            <h3 class="text-xl font-semibold text-slate-900">Course Outcomes</h3>
            <p class="text-sm text-slate-600">
                Define what students should be able to do after completing this course.
            </p>
        </div>

        <div class="space-y-3">
            @foreach($courseOutcomes as $index => $outcome)
                <div wire:key="co-row-{{ $outcome['temp_key'] ?? $outcome['id'] ?? $index }}"
                    class="flex items-start gap-3 p-3 border border-slate-200 rounded-2xl bg-white/90 shadow-sm">
                    <span class="w-16 text-center text-xs uppercase tracking-[0.2em] text-slate-500">
                        {{ $outcome['co_code'] ?? ('CO' . ($index + 1)) }}
                    </span>

                    <x-form.textarea rows="3"
                        wire:model.live.debounce.250ms="courseOutcomes.{{ $index }}.description"
                        placeholder="Enter CO description">
                    </x-form.textarea>

                    <button type="button"
                        wire:click="removeCourseOutcome({{ $index }})"
                        class="p-2 text-rose-600 hover:text-rose-800 rounded-full hover:bg-rose-100 transition"
                        title="Remove CO">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
            @endforeach

            <button
                type="button"
                wire:click="addCourseOutcome"
                class="
                    w-full
                    border-2 border-dashed border-emerald-300
                    rounded-2xl p-4
                    text-sm font-semibold text-emerald-700
                    hover:border-emerald-500
                    hover:bg-emerald-50
                    transition
                    flex items-center justify-center gap-2
                "
            >
                <i class='bx bx-plus'></i>
                Add Course Outcome
            </button>

            @if($coAddError)
                <div class="text-xs text-rose-600 mt-1">{{ $coAddError }}</div>
            @endif
        </div>

        <div class="mt-6 border border-green-200 rounded-xl p-4 shadow-sm">
            <h4 class="font-semibold text-sm text-green-800 mb-3">
                Program Outcomes Reference
            </h4>

            @if (count($programOutcomes) === 0)
                <p class="text-sm text-slate-500">No program outcomes found for this course.</p>
            @else
                <div class="grid grid-cols-1 gap-3">
                    @foreach ($programOutcomes as $po)
                        <div class="bg-white border border-green-400 rounded-lg p-3 shadow-sm flex items-start gap-2">
                            <span class="font-semibold text-green-700 text-sm shrink-0">
                                {{ $po['po_code'] }}.
                            </span>
                            <p class="text-slate-700 text-sm flex items-center gap-2">
                                {{ $po['po_text'] }}
                                @if (!empty($po['ied']))
                                    <x-feedback-status.ied-badge :level="$po['ied']" />
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
