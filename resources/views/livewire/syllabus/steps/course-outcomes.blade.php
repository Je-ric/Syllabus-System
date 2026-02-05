<div>
    <h3 class="text-xl font-semibold mb-4">Define Course Outcomes</h3>
    <p class="text-gray-600 text-sm mb-6">Define what students should be able to do after completing this course.</p>

    <div class="space-y-4">
        @foreach($courseOutcomes as $index => $outcome)
            <div class="border rounded-lg p-4 bg-gray-50" wire:key="outcome-{{ $index }}">
                <div class="flex items-start gap-4">
                    <div class="w-24 shrink-0">
                        <input type="text"
                               wire:model="courseOutcomes.{{ $index }}.co_code"
                               class="w-full border rounded px-3 py-2 text-center font-semibold"
                               readonly>
                    </div>

                    <div class="flex-1">
                        <textarea wire:model.debounce.1000ms="courseOutcomes.{{ $index }}.description"
                                  rows="3"
                                  placeholder="Describe what students will be able to do..."
                                  class="w-full border rounded-lg px-4 py-2"></textarea>
                    </div>

                    <button type="button"
                            wire:click="removeCourseOutcome({{ $index }})"
                            class="text-red-600 hover:text-red-800 p-2">
                        <i class="bx bx-trash text-xl"></i>
                    </button>
                </div>
            </div>
        @endforeach

        <button type="button"
                wire:click="addCourseOutcome"
                class="w-full border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-500 hover:bg-blue-50 transition text-gray-600 hover:text-blue-600 font-medium">
            <i class="bx bx-plus"></i> Add Course Outcome
        </button>
    </div>
</div>
