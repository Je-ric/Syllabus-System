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

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
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

    <x-layout.card-section title="Active Semester Info" icon="bx-calendar">
        @if ($data['active_semester'])
            <div class="space-y-3">
                <div>
                    <h4 class="text-sm font-semibold text-[#143D57]">Active Semester</h4>
                    <p class="text-lg font-bold text-[#394056]">{{ $data['active_semester']['label'] }}</p>
                </div>

                @if ($data['active_semester']['start_date'] && $data['active_semester']['end_date'])
                    <div>
                        <h4 class="text-sm font-semibold text-[#143D57]">Timeline</h4>
                        <p class="text-sm text-[#72809E]">
                            {{ $data['active_semester']['start_date'] }} → {{ $data['active_semester']['end_date'] }}
                        </p>
                    </div>
                @endif
            </div>
        @else
            <x-feedback-status.alert type="warning" title="No active semester" class="mb-0">
                <p class="text-sm">No academic calendar is marked as active. Set one under Academic Calendars.</p>
            </x-feedback-status.alert>
        @endif
    </x-layout.card-section>
</div>
