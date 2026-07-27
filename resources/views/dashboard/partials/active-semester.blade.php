@if ($activeSemester)
    <x-feedback-status.alert type="info" :show-title="false" class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <p class="text-[13px] font-semibold text-[#143D57]">
                    Active Semester: {{ $activeSemester['label'] }}
                </p>
                @if ($activeSemester['start_date'] && $activeSemester['end_date'])
                    <p class="text-[12px] mt-0.5 opacity-80">
                        {{ $activeSemester['start_date'] }} → {{ $activeSemester['end_date'] }}
                    </p>
                @endif
            </div>
        </div>
    </x-feedback-status.alert>
@else
    <x-feedback-status.alert type="warning" title="No active semester"
        message="No academic calendar is marked as active. Set one under Academic Calendars."
        class="mb-4" />
@endif
