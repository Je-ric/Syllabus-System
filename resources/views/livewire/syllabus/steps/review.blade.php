<div>
    <h3 class="text-xl font-semibold mb-6">Review & Submit</h3>

    {{-- Summary Sections --}}
    <div class="space-y-6">
        {{-- Academic Calendar --}}
        <div class="border rounded-lg p-4 bg-gray-50">
            <h4 class="font-semibold text-gray-700 mb-2">Academic Calendar</h4>
            @php
                $calendar = $academicCalendars->firstWhere('id', $academic_calendar_id);
            @endphp
            <p class="text-sm">
                @if($calendar)
                    {{ $calendar->academic_year }} - {{ $calendar->getFormattedSemester() }}
                @else
                    <span class="text-red-600">Not selected</span>
                @endif
            </p>
        </div>

        {{-- Course Components --}}
        <div class="border rounded-lg p-4 bg-gray-50">
            <h4 class="font-semibold text-gray-700 mb-3">Course Components</h4>

            <div class="mb-4">
                <h5 class="text-sm font-semibold text-blue-600 mb-2">Lecture</h5>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div><span class="text-gray-600">Instructor:</span> {{ $lec_instructor_name ?: '—' }}</div>
                    <div><span class="text-gray-600">Email:</span> {{ $lec_instructor_email ?: '—' }}</div>
                    <div><span class="text-gray-600">Schedule:</span> {{ $lec_schedule ?: '—' }}</div>
                    <div><span class="text-gray-600">Office:</span> {{ $lec_office ?: '—' }}</div>
                </div>
            </div>

            @if($course->has_lec_lab)
                <div>
                    <h5 class="text-sm font-semibold text-purple-600 mb-2">Laboratory</h5>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div><span class="text-gray-600">Instructor:</span> {{ $lab_instructor_name ?: '—' }}</div>
                        <div><span class="text-gray-600">Email:</span> {{ $lab_instructor_email ?: '—' }}</div>
                        <div><span class="text-gray-600">Schedule:</span> {{ $lab_schedule ?: '—' }}</div>
                        <div><span class="text-gray-600">Office:</span> {{ $lab_office ?: '—' }}</div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Course Outcomes --}}
        <div class="border rounded-lg p-4 bg-gray-50">
            <h4 class="font-semibold text-gray-700 mb-3">Course Outcomes ({{ count($courseOutcomes) }})</h4>
            @if(count($courseOutcomes) > 0)
                <ul class="space-y-2 text-sm">
                    @foreach($courseOutcomes as $co)
                        <li class="flex items-start gap-2">
                            <span class="font-semibold text-blue-600">{{ $co['co_code'] }}:</span>
                            <span class="text-gray-700">{{ $co['description'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500">No course outcomes defined yet.</p>
            @endif
        </div>

        {{-- CO-PO Mapping Summary --}}
        <div class="border rounded-lg p-4 bg-gray-50">
            <h4 class="font-semibold text-gray-700 mb-3">CO-PO Mapping</h4>
            @if(count($coPoMappings) > 0)
                <div class="text-sm">
                    @php
                        $totalMappings = 0;
                        foreach ($coPoMappings as $coId => $poMappings) {
                            $totalMappings += count(array_filter($poMappings));
                        }
                    @endphp
                    <p>Total mappings: <span class="font-semibold">{{ $totalMappings }}</span></p>
                </div>
            @else
                <p class="text-sm text-gray-500">No CO-PO mappings defined yet.</p>
            @endif
        </div>
    </div>

    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <p class="text-sm text-yellow-800">
            <i class="bx bx-info-circle"></i>
            Once you submit, the syllabus will be sent for review by the department chair. Make sure all information is correct.
        </p>
    </div>
</div>
