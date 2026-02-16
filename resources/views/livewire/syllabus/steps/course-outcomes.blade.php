<div class="space-y-4 text-slate-800">
    <div class="mb-4">
        <h3 class="text-xl font-semibold text-slate-900">Course Outcomes</h3>
        <p class="text-sm text-slate-600">
            Define what students should be able to do after completing this course.
        </p>
        <div class="mt-2 text-xs rounded-md border border-amber-200 bg-amber-50 text-amber-800 px-3 py-2">
            Course Outcomes are manual-save only. Click <strong>Save Draft</strong> to apply changes.
        </div>
        <div class="mt-2 flex items-center gap-2 text-xs text-slate-600">
            <span @class([
                    'inline-flex items-center rounded bg-orange-100 text-orange-700 px-2 py-1',
                    'hidden' => !($stepDirty['course_outcomes'] ?? false),
                ])>
                Unsaved CO changes
            </span>
            <span @class([
                    'inline-flex items-center rounded bg-emerald-100 text-emerald-700 px-2 py-1',
                    'hidden' => ($stepDirty['course_outcomes'] ?? false),
                ])
                >
                COs saved
            </span>
        </div>
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
</div>
