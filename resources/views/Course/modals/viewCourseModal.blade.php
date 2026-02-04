<x-modal.dialog id="viewCourseModal_{{ $course->id }}" maxWidth="max-w-6xl" width="w-11/12">
    <x-modal.header class="bg-gray-50">
        <h3 class="text-xl font-semibold text-gray-800">Course Details</h3>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-6">

            {{-- Basic Course Info --}}
            <div class="border rounded-lg p-6 bg-white">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $course->course_title }}
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $course->course_code }}
                </p>

                <div class="border-t my-4"></div>

                {{-- Details Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 font-medium">Program</p>
                        <p>{{ $course->program->program_name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 font-medium">Credit Units</p>
                        <p>{{ $course->credit_units }}</p>
                    </div>

                    @if ($course->year_level)
                        <div>
                            <p class="text-gray-500 font-medium">Year Level</p>
                            <p>Year {{ $course->year_level }}</p>
                        </div>
                    @endif

                    @if ($course->semester)
                        <div>
                            <p class="text-gray-500 font-medium">Semester</p>
                            <p>Semester {{ $course->semester }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-gray-500 font-medium">Lecture / Laboratory</p>
                        <p class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                            {{ $course->has_lec_lab ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $course->has_lec_lab ? 'Yes' : 'No' }}
                        </p>
                    </div>

                    @if ($course->prerequisite)
                        <div>
                            <p class="text-gray-500 font-medium">Prerequisite</p>
                            <p>{{ $course->prerequisite }}</p>
                        </div>
                    @endif

                    @if ($course->corequisite)
                        <div>
                            <p class="text-gray-500 font-medium">Corequisite</p>
                            <p>{{ $course->corequisite }}</p>
                        </div>
                    @endif

                    @if ($course->creator)
                        <div>
                            <p class="text-gray-500 font-medium">Created By</p>
                            <p>{{ $course->creator->name ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>

                @if ($course->course_description)
                    <div class="mt-5">
                        <p class="text-gray-500 font-medium mb-1">Course Description</p>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            {{ $course->course_description }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Program Outcomes Mapping --}}
            <div class="border rounded-lg bg-white">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Program Outcomes Mapping (IED)
                    </h2>
                </div>

                @if ($course->programOutcomes->isEmpty())
                    <div class="px-6 py-4 text-sm text-gray-600">
                        No program outcomes mapped to this course.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold">PO</th>
                                    <th class="px-4 py-2 text-left font-semibold">Program Outcome</th>
                                    <th class="px-4 py-2 text-center font-semibold">IED</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($course->programOutcomes as $outcome)
                                    <tr class="border-b last:border-b-0">
                                        <td class="px-4 py-2 font-mono font-semibold">
                                            {{ $outcome->po_code }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $outcome->po_text }}
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <x-feedback-status.ied-badge :level="$outcome->pivot->ied" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </x-modal.body>

    <x-modal.footer class="bg-gray-50">
        <x-modal.close-button
            modalId="viewCourseModal_{{ $course->id }}"
            text="Close"
            variant="close"
        />
    </x-modal.footer>
</x-modal.dialog>
