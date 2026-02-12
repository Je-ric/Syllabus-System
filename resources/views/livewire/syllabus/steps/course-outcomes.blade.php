<div>
    <div class="mb-4">
        <h3 class="text-xl font-semibold text-slate-900">Course Outcomes</h3>
        <p class="text-sm text-slate-600">
            Define what students should be able to do after completing this course.
        </p>
    </div>

    <div class="space-y-4">
        @foreach($courseOutcomes as $index => $outcome)
            @php
                $rowKey = $outcome['temp_key'] ?? ($outcome['id'] ? 'co_' . $outcome['id'] : 'new_' . $index);
            @endphp
            <div
                class="border border-emerald-200 rounded-lg p-4 bg-emerald-50/40"
                wire:key="outcome-{{ $rowKey }}"
            >
                <div class="flex items-start gap-4">
                    {{-- CO Code --}}
                    <div class="w-24 shrink-0">
                        <span class="w-16 text-center font-semibold text-emerald-700">
                            {{ $outcome['co_code'] }}
                        </span>

                    </div>

                    {{-- Description --}}
                    <div class="flex-1">
                        <textarea
                            x-data="{
                                desc: @js($outcome['description'] ?? ''),
                                lastSent: @js($outcome['description'] ?? ''),
                                syncNow() {
                                    if (this.desc === this.lastSent) return;
                                    this.lastSent = this.desc;
                                    $wire.syncCourseOutcomeDescription('{{ $rowKey }}', this.desc);
                                }
                            }"
                            x-model="desc"
                            data-co-rowkey="{{ $rowKey }}"
                            x-on:input.debounce.900ms="syncNow()"
                            x-on:blur="syncNow()"
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
                        x-on:click="
                            const y = window.scrollY;
                            $wire.removeCourseOutcomeByRowKey('{{ $rowKey }}').then(() => window.scrollTo(0, y));
                        "
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
            x-on:click="
                const y = window.scrollY;
                $wire.addCourseOutcome().then(() => window.scrollTo(0, y));
            "
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
        @if($coAddError)
            <div class="text-xs text-red-600">
                {{ $coAddError }}
            </div>
        @endif
    </div>
</div>
