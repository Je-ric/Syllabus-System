<div>
    <div class="mb-4">
        <h3 class="text-xl font-semibold text-slate-900">CO-PO Mapping</h3>
        <p class="text-sm text-slate-600">Select which Program Outcomes each Course Outcome supports.</p>
    </div>

    @if (!$course)
        <div class="text-sm text-slate-500 bg-slate-50 border border-slate-200 rounded-lg p-4">
            Open this step to load CO-PO mapping data.
        </div>
    @elseif (count($courseOutcomes) === 0)
        <x-empty-state
            icon="bx-list-check"
            title="No course outcomes yet"
            message="Please define Course Outcomes in the previous step first."
            class="py-10"
        />
    @else
        <x-table.container>
            <x-table.table>
                <x-table.head sticky>
                    <tr>
                        <x-table.th>Course Outcome</x-table.th>
                        @foreach ($course->program->outcomes as $po)
                            <x-table.th align="center" class="normal-case tracking-normal text-xs px-2">
                                {{ $po->po_code }}
                            </x-table.th>
                        @endforeach
                    </tr>
                </x-table.head>
                <x-table.body>
                    @foreach ($courseOutcomes as $index => $co)
                        @php
                            $coId = $co['id'] ?? null;
                            $rowKey = $co['temp_key'] ?? ($coId ? 'co_' . $coId : 'new_' . $index);
                            $mapKey = $coId ? $coId : ($co['temp_key'] ?? 'new_' . $index);
                        @endphp
                        <x-table.row hover wire:key="co-row-{{ $rowKey }}">
                            <x-table.td>
                                <div class="flex items-start gap-2">
                                    <span class="font-semibold text-green-700">{{ $co['co_code'] }}</span>
                                    <span class="text-xs text-slate-600">{{ Str::limit($co['description'], 60) }}</span>
                                </div>
                            </x-table.td>
                            @foreach ($course->program->outcomes as $po)
                                <x-table.td align="center" class="px-2"
                                    wire:key="co-po-{{ $rowKey }}-{{ $po->id }}">
                                    <x-form.checkbox
                                        wire:model.debounce.500ms="coPoMappings.{{ $mapKey }}.{{ $po->id }}"
                                        :checked="(bool) data_get($coPoMappings, $mapKey . '.' . $po->id, false)"
                                        aria-label="Map {{ $co['co_code'] }} to {{ $po->po_code }}"
                                        class="justify-center"
                                    />
                                </x-table.td>
                            @endforeach
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table.table>
        </x-table.container>

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
