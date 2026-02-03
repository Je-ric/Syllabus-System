<x-modal.dialog id="viewCourseModal_{{ $course->id }}" maxWidth="max-w-6xl" width="w-11/12">
    <x-modal.header>
        <h3 class="text-xl font-semibold text-gray-800">Course Details</h3>
    </x-modal.header>

    <x-modal.body>
        <div id="modalContent" class="space-y-4">
            <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body">
            <h1 class="card-title text-3xl">{{ $course->course_title }}</h1>
            <p class="text-lg font-mono text-primary">{{ $course->course_code }}</p>

            <div class="divider"></div>

            {{-- Course Details Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="font-semibold text-sm text-base-content/70">Program</h3>
                    <p>{{ $course->program->program_name ?? 'N/A' }}</p>
                </div>

                <div>
                    <h3 class="font-semibold text-sm text-base-content/70">Credit Units</h3>
                    <p>{{ $course->credit_units }}</p>
                </div>

                @if($course->year_level)
                <div>
                    <h3 class="font-semibold text-sm text-base-content/70">Year Level</h3>
                    <p>Year {{ $course->year_level }}</p>
                </div>
                @endif

                @if($course->semester)
                <div>
                    <h3 class="font-semibold text-sm text-base-content/70">Semester</h3>
                    <p>Semester {{ $course->semester }}</p>
                </div>
                @endif

                <div>
                    <h3 class="font-semibold text-sm text-base-content/70">Lecture/Laboratory</h3>
                    <p>
                        @if($course->has_lec_lab)
                            <span class="badge badge-success">Yes</span>
                        @else
                            <span class="badge badge-ghost">No</span>
                        @endif
                    </p>
                </div>

                @if($course->prerequisite)
                <div>
                    <h3 class="font-semibold text-sm text-base-content/70">Prerequisite</h3>
                    <p>{{ $course->prerequisite }}</p>
                </div>
                @endif

                @if($course->corequisite)
                <div>
                    <h3 class="font-semibold text-sm text-base-content/70">Corequisite</h3>
                    <p>{{ $course->corequisite }}</p>
                </div>
                @endif

                @if($course->creator)
                <div>
                    <h3 class="font-semibold text-sm text-base-content/70">Created By</h3>
                    <p>{{ $course->creator->name ?? 'N/A' }}</p>
                </div>
                @endif
            </div>

            @if($course->course_description)
            <div class="mt-4">
                <h3 class="font-semibold text-sm text-base-content/70 mb-2">Course Description</h3>
                <p class="text-base-content/90">{{ $course->course_description }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Program Outcomes Mapping --}}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-2xl">Program Outcomes Mapping (IED Levels)</h2>

            @if($course->programOutcomes->isEmpty())
                <div class="alert alert-info">
                    <span>No program outcomes mapped to this course yet.</span>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>PO Code</th>
                                <th>Program Outcome</th>
                                <th>IED Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($course->programOutcomes as $outcome)
                            <tr>
                                <td class="font-mono font-semibold">
                                    {{-- {{ $outcome->po_code }} --}}
                                    PO{{ $loop->iteration }}
                                </td>
                                <td>{{ $outcome->po_text }}</td>
                                <td>
                                    @if($outcome->pivot->ied === 'I')
                                        <span class="badge badge-info">I - Introduced</span>
                                    @elseif($outcome->pivot->ied === 'E')
                                        <span class="badge badge-warning">E - Emphasized</span>
                                    @elseif($outcome->pivot->ied === 'D')
                                        <span class="badge badge-success">D - Demonstrated</span>
                                    @else
                                        <span class="badge badge-ghost">{{ $outcome->pivot->ied }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endif
        </div>
    </div>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="viewCourseModal_{{ $course->id }}" text="Close" variant="close" />
    </x-modal.footer>
</x-modal.dialog>

