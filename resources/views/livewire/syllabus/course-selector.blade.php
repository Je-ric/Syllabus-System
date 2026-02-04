<div>
    @if (!$programId)
        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed">
            <p class="text-gray-500">Select a program to view courses</p>
        </div>
    @else
        <div class="mb-4">
            <h2 class="text-lg font-semibold">Available Courses</h2>
            <p class="text-gray-600 text-sm">Click on a course to fill out its syllabus</p>
        </div>

        <div wire:loading class="text-center py-8 text-gray-500">
            Loading courses...
        </div>

        @if (count($courses) > 0)
            <div class="grid grid-cols-1 gap-3" wire:loading.remove>
                @foreach ($courses as $course)
                    <a href="{{ route('syllabus.form', $course->id) }}"
                        class="block p-4 border rounded-lg hover:bg-blue-50 hover:border-blue-500 transition group">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 group-hover:text-blue-600">
                                    {{ $course->course_code }}
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    {{ $course->course_title }}
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    {{ $course->credit_units }} units
                                    @if ($course->year_level)
                                        • Year {{ $course->year_level }}
                                    @endif
                                    @if ($course->semester)
                                        • Sem {{ $course->semester }}
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4 flex items-center gap-2">
                                @if ($course->has_lec_lab)
                                    <span class="px-2 py-1 text-xs bg-purple-100 text-purple-700 rounded">LEC+LAB</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded">LEC</span>
                                @endif
                                <span class="text-gray-400 group-hover:text-blue-600">
                                    <i class="bx bx-chevron-right"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed" wire:loading.remove>
                <p class="text-gray-500">No courses found for selected program</p>
            </div>
        @endif
    @endif
</div>
