<x-layout.card-section title="Faculty Dashboard" icon="bx-user-check" class="mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard.stat-card
            label="My Draft Syllabi"
            value="{{ $data['draft_syllabi_count'] ?? 0 }}"
            icon="bx-edit"
            color="amber" />

        <x-dashboard.stat-card
            label="Under Review"
            value="{{ $data['under_review_count'] ?? 0 }}"
            icon="bx-time"
            color="blue" />

        <x-dashboard.stat-card
            label="For Revision"
            value="{{ $data['for_revision_count'] ?? 0 }}"
            icon="bx-notepad"
            color="rose" />

        <x-dashboard.stat-card
            label="Approved"
            value="{{ $data['approved_count'] ?? 0 }}"
            icon="bx-check-circle"
            color="emerald" />
    </div>
</x-layout.card-section>

<x-layout.card-section title="Semester Timeline" icon="bx-calendar-event" class="mb-6">
    @if ($data['active_semester'])
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg p-4 border border-emerald-200">
                <h4 class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Current Week</h4>
                <p class="text-2xl font-bold text-emerald-800 mt-1">Week {{ $data['active_semester']['current_week'] ?? '-' }}</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <h4 class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Current Date</h4>
                <p class="text-lg font-bold text-blue-800 mt-1">{{ $data['active_semester']['current_date'] ?? '-' }}</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <h4 class="text-xs font-semibold text-purple-700 uppercase tracking-wide">Days Remaining</h4>
                <p class="text-2xl font-bold text-purple-800 mt-1">
                    @if(($data['active_semester']['days_remaining'] ?? 0) < 0)
                        <span class="text-red-600">{{ abs($data['active_semester']['days_remaining']) }} days ago</span>
                    @else
                        {{ $data['active_semester']['days_remaining'] ?? '-' }}
                    @endif
                </p>
            </div>
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg p-4 border border-amber-200">
                <h4 class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Semester</h4>
                <p class="text-lg font-bold text-amber-800 mt-1">{{ $data['active_semester']['label'] }}</p>
            </div>
        </div>
    @else
        <x-feedback-status.alert type="warning" title="No active semester" class="mb-0">
            <p class="text-sm">No academic calendar is marked as active. Set one under Academic Calendars.</p>
        </x-feedback-status.alert>
    @endif
</x-layout.card-section>

<x-layout.card-section title="Upcoming Events" icon="bx-calendar-event" class="mb-6">
    @if (empty($data['upcoming_events']))
        <x-feedback-status.empty-state
            icon="bx-calendar-event"
            title="No upcoming events"
            message="No academic events scheduled for the next 14 days." />
    @else
        <div class="space-y-3">
            @foreach ($data['upcoming_events'] as $event)
                <div class="flex items-center justify-between p-3 rounded-lg bg-[#F9FAFB]">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[#394056] truncate">
                            {{ $event['name'] }}
                        </p>
                        <p class="text-xs text-[#72809E] mt-1">
                            {{ $event['date'] }}
                            @if (isset($event['days_until']))
                                <span class="ml-2 font-semibold {{ $event['days_until'] <= 3 ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ $event['days_until'] == 0 ? 'Today' : ($event['days_until'] == 1 ? 'Tomorrow' : $event['days_until'] . ' days') }}
                                </span>
                            @endif
                        </p>
                    </div>
                    <span class="shrink-0 text-xs font-semibold px-2 py-1 rounded-full
                        @if(($event['type'] ?? 'other') == 'holiday') bg-blue-100 text-blue-700
                        @elseif(($event['type'] ?? 'other') == 'exam') bg-red-100 text-red-700
                        @elseif(($event['type'] ?? 'other') == 'break') bg-purple-100 text-purple-700
                        @elseif(($event['type'] ?? 'other') == 'non_teaching') bg-orange-100 text-orange-700
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ ucfirst($event['type'] ?? 'other') }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</x-layout.card-section>

<x-layout.card-section title="Quick Actions" icon="bx-run" class="mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.button href="{{ route('syllabus.create') }}" variant="primary">
            <i class="bx bx-notepad text-base leading-none me-2"></i>
            Create New Syllabus
        </x-ui.button>

        @if ($data['has_draft'])
            <x-ui.button href="{{ route('syllabus.edit', ['syllabus' => $data['latest_draft_id']]) }}" variant="outline">
                <i class="bx bx-pencil text-base leading-none me-2"></i>
                Continue Draft
            </x-ui.button>
        @endif

        <x-ui.button href="{{ route('syllabus.index') }}" variant="secondary">
            <i class="bx bx-list-ul text-base leading-none me-2"></i>
            View All Syllabi
        </x-ui.button>

        <x-ui.button href="{{ route('syllabus.courses') }}" variant="secondary">
            <i class="bx bx-book-content text-base leading-none me-2"></i>
            Browse Courses
        </x-ui.button>
    </div>
</x-layout.card-section>

<x-layout.card-section title="Courses Without IED Mapping" icon="bx-error-circle" class="mb-6">
    @if (empty($data['courses_without_ied']))
        <x-feedback-status.empty-state
            icon="bx-check-circle"
            title="All courses mapped"
            message="All your courses have IED (Introduce-Emphasize-Develop) mappings configured." />
    @else
        <div class="space-y-3">
            @foreach ($data['courses_without_ied'] as $course)
                <div class="flex items-center justify-between p-3 rounded-lg bg-[#FEF2F2] border border-[#FECACA]">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[#991B1B] truncate">
                            {{ $course['course_code'] }} - {{ $course['title'] }}
                        </p>
                        <p class="text-xs text-[#7F1D1D] mt-1">
                            Program: {{ $course['program'] ?? 'Unknown' }}
                        </p>
                    </div>
                    <x-ui.button href="{{ route('courses.edit', ['course' => $course['id']]) }}" variant="table-danger" size="sm">
                        <i class="bx bx-edit text-xs leading-none"></i> Fix Mapping
                    </x-ui.button>
                </div>
            @endforeach
        </div>
    @endif
</x-layout.card-section>

<x-layout.card-section title="Recent Syllabi Activity" icon="bx-time" height="medium">
    @if (empty($data['recent_syllabi']))
        <x-feedback-status.empty-state
            icon="bx-time"
            title="No recent activity"
            message="You haven't created or updated any syllabi recently." />
    @else
        <div class="space-y-3">
            @foreach ($data['recent_syllabi'] as $syllabus)
                <div class="flex items-center justify-between p-3 rounded-lg bg-[#F9FAFB]">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[#394056] truncate">
                            {{ $syllabus['course_code'] ?? 'Unknown Course' }} - {{ $syllabus['title'] ?? 'Untitled' }}
                        </p>
                        <p class="text-xs text-[#72809E] mt-1">
                            Status: <span class="font-semibold">{{ $syllabus['status_label'] ?? 'Unknown' }}</span>
                            @if (isset($syllabus['updated_at']))
                                | Updated {{ $syllabus['updated_at'] }}
                            @endif
                        </p>
                    </div>
                    <x-feedback-status.status-indicator
                        :status="$syllabus['status'] ?? 'neutral'"
                        size="sm" />
                </div>
            @endforeach
        </div>
    @endif
</x-layout.card-section>
