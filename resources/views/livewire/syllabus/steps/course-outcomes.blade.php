<div>
    <h3 class="text-xl font-semibold text-green-800 mb-2">
        Define Course Outcomes
    </h3>

    <p class="text-gray-600 text-sm mb-6">
        Define what students should be able to do after completing this course.
    </p>

    <div class="space-y-4">
        @foreach($courseOutcomes as $index => $outcome)
            <div
                class="border border-green-200 rounded-lg p-4 bg-green-50/40"
                wire:key="outcome-{{ $index }}"
            >
                <div class="flex items-start gap-4">
                    {{-- CO Code --}}
                    <div class="w-24 shrink-0">
                        <input
                            type="text"
                            wire:model="courseOutcomes.{{ $index }}.co_code"
                            readonly
                            class="
                                w-full text-center font-semibold text-sm
                                border border-green-300 rounded-md
                                px-3 py-2
                                bg-green-100 text-green-800
                            "
                        >
                    </div>

                    {{-- Description --}}
                    <div class="flex-1">
                        <textarea
                            wire:model.debounce.1000ms="courseOutcomes.{{ $index }}.description"
                            rows="3"
                            placeholder="Describe what students will be able to do after completing this course..."
                            class="
                                w-full
                                border border-green-300 rounded-md
                                px-4 py-2 text-sm
                                text-gray-800
                                focus:outline-none
                                focus:ring-1 focus:ring-green-600
                                focus:border-green-600
                            "
                        ></textarea>
                    </div>

                    {{-- Remove --}}
                    <button
                        type="button"
                        wire:click="removeCourseOutcome({{ $index }})"
                        class="
                            text-red-600 hover:text-red-800
                            p-2 rounded
                            hover:bg-red-50
                            transition
                        "
                        title="Remove outcome"
                    >
                        <i class="bx bx-trash text-lg"></i>
                    </button>
                </div>
            </div>
        @endforeach

        {{-- Add Outcome --}}
        <button
            type="button"
            wire:click="addCourseOutcome"
            class="
                w-full
                border-2 border-dashed border-green-300
                rounded-lg p-4
                text-sm font-medium text-green-700
                hover:border-green-600
                hover:bg-green-50
                transition
                flex items-center justify-center gap-2
            "
        >
            <i class="bx bx-plus"></i>
            Add Course Outcome
        </button>
    </div>
</div>
