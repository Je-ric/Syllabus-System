<div>
    <div class="mb-4">
        <h3 class="text-xl font-semibold text-slate-900">Course Outcomes</h3>
        <p class="text-sm text-slate-600">
            Define what students should be able to do after completing this course.
        </p>
        <div class="mt-2 text-xs rounded-md border border-amber-200 bg-amber-50 text-amber-800 px-3 py-2">
            Course Outcomes are not auto-saved while typing. Click <strong>Save Draft</strong> first, then proceed to the next step.
        </div>
        <div class="mt-2 flex items-center gap-2 text-xs">
            <span @class([
                    'inline-flex items-center rounded bg-orange-100 text-orange-700 px-2 py-1',
                    'hidden' => !($stepDirty['course_outcomes'] ?? false),
                ])>
                Unsaved CO changes
            </span>
            <span wire:loading wire:target="saveCurrentStep,navigateToStep,submitForReview"
                class="inline-flex items-center rounded bg-blue-100 text-blue-700 px-2 py-1">
                Saving COs...
            </span>
            <span @class([
                    'inline-flex items-center rounded bg-emerald-100 text-emerald-700 px-2 py-1',
                    'hidden' => ($stepDirty['course_outcomes'] ?? false),
                ])
                wire:loading.remove wire:target="saveCurrentStep,navigateToStep,submitForReview">
                COs saved
            </span>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($courseOutcomes as $index => $outcome)
            <div wire:key="co-row-{{ $outcome['temp_key'] ?? $outcome['id'] ?? $index }}" class="border border-emerald-200 rounded-lg p-4 bg-emerald-50/40">
                <div class="flex items-start gap-4">
                    <div class="w-24 shrink-0">
                        <span class="w-16 text-center font-semibold text-emerald-700">
                            {{ $outcome['co_code'] ?? ('CO' . ($index + 1)) }}
                        </span>
                    </div>

                    <div class="flex-1">
                        <textarea
                            rows="3"
                            placeholder="Describe what students will be able to do after completing this course..."
                            wire:model.live.debounce.300ms="courseOutcomes.{{ $index }}.description"
                            class="w-full border border-green-300 rounded-md px-4 py-2 text-sm text-gray-800 focus:outline-none focus:ring-1 focus:ring-green-600 focus:border-green-600"
                        ></textarea>
                    </div>

                    <button
                        type="button"
                        wire:click="removeCourseOutcome({{ $index }})"
                        class="text-red-600 hover:text-red-800 p-2 rounded hover:bg-red-50 transition"
                        title="Remove outcome"
                    >
                        <i class="bx bx-trash text-lg"></i>
                    </button>
                </div>
            </div>
        @endforeach

        <button
            type="button"
            wire:click="addCourseOutcome"
            class="w-full border-2 border-dashed border-green-300 rounded-lg p-4 text-sm font-medium text-green-700 hover:border-green-600 hover:bg-green-50 transition flex items-center justify-center gap-2"
        >
            <i class="bx bx-plus"></i>
            Add Course Outcome
        </button>

        @if($coAddError)
            <div class="text-xs text-red-600">
                {{ $coAddError }}
            </div>
        @endif
    </div>
</div>
