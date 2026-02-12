<div>
    <div class="mb-4">
        <h3 class="text-xl font-semibold text-slate-900">CO-PO Mapping</h3>
        <p class="text-sm text-slate-600">Select which Program Outcomes each Course Outcome supports.</p>
    </div>

    @if (count($courseOutcomes) === 0)
        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed">
            <p class="text-gray-500">Please define Course Outcomes in the previous step first.</p>
        </div>
    @else
        <div class="overflow-x-auto border border-slate-200 rounded-xl">
            <table class="w-full border-collapse text-sm">
                <thead class="bg-slate-100 border-b sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700 w-64">Course Outcome</th>
                        @foreach ($course->program->outcomes as $po)
                            <th class="px-2 py-3 text-center font-semibold text-slate-700 text-xs">
                                {{ $po->po_code }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courseOutcomes as $index => $co)
                        @php
                            $coId = $co['id'] ?? null;
                            $rowKey = $co['temp_key'] ?? ($coId ? 'co_' . $coId : 'new_' . $index);
                            $mapKey = $coId ? $coId : ($co['temp_key'] ?? 'new_' . $index);
                        @endphp
                        <tr class="border-b hover:bg-slate-50" wire:key="co-row-{{ $rowKey }}">
                            <td class="px-4 py-3">
                                <div class="flex items-start gap-2">
                                    <span class="font-semibold text-green-700">{{ $co['co_code'] }}</span>
                                    <span class="text-xs text-slate-600">{{ Str::limit($co['description'], 60) }}</span>
                                </div>
                            </td>
                            @foreach ($course->program->outcomes as $po)
                                <td class="px-2 py-3 text-center"
                                    wire:key="co-po-{{ $rowKey }}-{{ $po->id }}">
                                    <input type="checkbox"
                                        wire:model.debounce.500ms="coPoMappings.{{ $mapKey }}.{{ $po->id }}"
                                        class="h-4 w-4 cursor-pointer rounded border-slate-300 text-green-600 focus:ring-green-500" />
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PO Reference --}}
        <div class="mt-6 border border-green-200 rounded-xl p-4 shadow-sm">
            <h4 class="font-semibold text-sm text-green-800 mb-3">
                Program Outcomes Reference
            </h4>

            <div class="grid grid-cols-1 gap-3">
                @foreach ($course->program->outcomes as $po)
                    @php
                        $iedLevel = $course->programOutcomes->firstWhere('id', $po->id)?->pivot?->ied ?? '-';
                    @endphp

                    <div
                        class="bg-white border border-green-400 rounded-lg p-3 shadow-sm hover:shadow-md transition flex items-start justify-between gap-4">
                        <div class="flex gap-2">
                            <span class="font-semibold text-green-700 text-sm shrink-0">
                                {{ $po->po_code }}.
                            </span>
                            <p class="text-slate-700 text-sm">
                                {{ $po->po_text }}
                            </p>
                        </div>
                        <div class="shrink-0">
                            <x-feedback-status.ied-badge :level="$iedLevel" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


    @endif
</div>
