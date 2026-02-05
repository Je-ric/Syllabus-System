<div>
    <h3 class="text-xl font-semibold mb-4">Map Course Outcomes to Program Outcomes</h3>
    <p class="text-gray-600 text-sm mb-6">Select which Program Outcomes each Course Outcome supports.</p>

    @if (count($courseOutcomes) === 0)
        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed">
            <p class="text-gray-500">Please define Course Outcomes in the previous step first.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Course Outcome</th>
                        @foreach ($course->program->outcomes as $po)
                            <th class="px-2 py-3 text-center font-semibold text-sm">
                                {{ $po->po_code }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courseOutcomes as $index => $co)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-start gap-2">
                                    <span class="font-semibold text-blue-600">{{ $co['co_code'] }}</span>
                                    <span class="text-sm text-gray-700">{{ Str::limit($co['description'], 60) }}</span>
                                </div>
                            </td>
                            @foreach ($course->program->outcomes as $po)
                                <td class="px-2 py-3 text-center">
                                    @php
                                        $coId = $co['id'] ?? null;
                                        $mappingKey = $coId ? $coId : 'new_' . $index;
                                    @endphp
                                    <input type="checkbox"
                                        wire:model.debounce.500ms="coPoMappings.{{ $mappingKey }}.{{ $po->id }}"
                                        class="h-4 w-4 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PO Reference --}}
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 shadow-sm">
            <h4 class="font-semibold text-sm text-blue-700 mb-3">Program Outcomes Reference</h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach ($course->program->outcomes as $po)
                    @php
                        $iedLevel = $course->programOutcomes->firstWhere('id', $po->id)?->pivot?->ied ?? '-';
                    @endphp

                    <div
                        class="bg-white border border-gray-200 rounded p-3 flex flex-col shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-blue-600 text-sm">{{ $po->po_code }}</span>
                            <span class="text-[10px] text-gray-500 px-2 py-0.5 bg-gray-100 rounded">
                                {{ $iedLevel }}
                            </span>
                        </div>
                        <p class="text-gray-700 text-sm">{{ $po->po_text }}</p>
                    </div>
                @endforeach
            </div>
        </div>

    @endif
</div>
