<div>
    <div class="mb-4">
        <h3 class="text-xl font-semibold text-slate-900">Review & Submit</h3>
        <p class="text-sm text-slate-600">Review all details before submitting for approval.</p>
    </div>

    <div class="space-y-6">
        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
            <h4 class="font-semibold text-slate-700 mb-2">Academic Calendar</h4>
            @php
                $calendar = $academicCalendars->firstWhere('id', $academic_calendar_id);
            @endphp
            <p class="text-sm">
                @if($calendar)
                    {{ $calendar->academic_year }} - {{ $calendar->getFormattedSemester() }}
                    <span class="text-slate-500">
                        ({{ $calendar->start_date?->format('M d, Y') }} - {{ $calendar->end_date?->format('M d, Y') }})
                    </span>
                @else
                    <span class="text-red-600">Not selected</span>
                @endif
            </p>
        </div>

        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
            <h4 class="font-semibold text-slate-700 mb-3">Course Components</h4>
            @php
                $lec = $syllabus?->components?->firstWhere('type', 'LEC');
                $lab = $syllabus?->components?->firstWhere('type', 'LAB');
            @endphp

            <div class="mb-4">
                <h5 class="text-sm font-semibold text-blue-700 mb-2">Lecture</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                    <div><span class="text-slate-600">Instructor:</span> {{ $lec?->instructor_name ?: '-' }}</div>
                    <div><span class="text-slate-600">Email:</span> {{ $lec?->instructor_email ?: '-' }}</div>
                    <div><span class="text-slate-600">Phone:</span> {{ $lec?->phone ?: '-' }}</div>
                    <div><span class="text-slate-600">Office:</span> {{ $lec?->office ?: '-' }}</div>
                    <div><span class="text-slate-600">Class Hours:</span> {{ $lec?->class_hours ?: '-' }}</div>
                    <div><span class="text-slate-600">Schedule:</span> {{ $lec?->schedule ?: '-' }}</div>
                    <div><span class="text-slate-600">Consultation:</span> {{ $lec?->consultation_hours ?: '-' }}</div>
                    <div><span class="text-slate-600">Performance:</span> {{ $lec?->performance_standard ?: '-' }}</div>
                </div>
            </div>

            @if($course && $course->has_lec_lab)
                <div>
                    <h5 class="text-sm font-semibold text-purple-700 mb-2">Laboratory</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <div><span class="text-slate-600">Instructor:</span> {{ $lab?->instructor_name ?: '-' }}</div>
                        <div><span class="text-slate-600">Email:</span> {{ $lab?->instructor_email ?: '-' }}</div>
                        <div><span class="text-slate-600">Phone:</span> {{ $lab?->phone ?: '-' }}</div>
                        <div><span class="text-slate-600">Office:</span> {{ $lab?->office ?: '-' }}</div>
                        <div><span class="text-slate-600">Class Hours:</span> {{ $lab?->class_hours ?: '-' }}</div>
                        <div><span class="text-slate-600">Schedule:</span> {{ $lab?->schedule ?: '-' }}</div>
                        <div><span class="text-slate-600">Consultation:</span> {{ $lab?->consultation_hours ?: '-' }}</div>
                        <div><span class="text-slate-600">Performance:</span> {{ $lab?->performance_standard ?: '-' }}</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
            <h4 class="font-semibold text-slate-700 mb-3">Course Outcomes ({{ count($courseOutcomes) }})</h4>
            @if(count($courseOutcomes) > 0)
                <ul class="space-y-2 text-sm">
                    @foreach($courseOutcomes as $co)
                        <li class="flex items-start gap-2">
                            <span class="font-semibold text-blue-700">{{ $co['co_code'] }}:</span>
                            <span class="text-slate-700">{{ $co['description'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-slate-500">No course outcomes defined yet.</p>
            @endif
        </div>

        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
            <h4 class="font-semibold text-slate-700 mb-3">CO-PO Mapping</h4>
            @if(count($coPoMappings) > 0)
                <div class="text-sm text-slate-700">
                    @php
                        $totalMappings = 0;
                        foreach ($coPoMappings as $poMappings) {
                            $totalMappings += count(array_filter($poMappings ?? []));
                        }
                    @endphp
                    <p>Total mappings: <span class="font-semibold">{{ $totalMappings }}</span></p>
                </div>
            @else
                <p class="text-sm text-slate-500">No CO-PO mappings defined yet.</p>
            @endif
        </div>

        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
            <h4 class="font-semibold text-slate-700 mb-3">Weekly Coverage</h4>
            @if(isset($syllabusWeeks) && $syllabusWeeks->count() > 0)
                <div class="text-sm text-slate-700">
                    <p>Total weeks: <span class="font-semibold">{{ $syllabusWeeks->count() }}</span></p>
                    <div class="mt-2">
                        @php
                            $examLabels = [
                                'first_term' => '1st Term Exam',
                                'second_term' => '2nd Term Exam',
                                'final_term' => 'Final Term Exam',
                            ];
                        @endphp
                        @foreach($examLabels as $key => $label)
                            @php
                                $weekNo = $examWeeks[$key] ?? null;
                            @endphp
                            <div>
                                {{ $label }}:
                                <span class="font-semibold">
                                    {{ $weekNo ? 'Week ' . $weekNo : 'Not set' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-sm text-slate-500">Weekly coverage not generated yet.</p>
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
