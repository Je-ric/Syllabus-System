@if ($data['no_assignment'])
    <x-feedback-status.alert type="warning" title="No department assigned"
        message="You have the Chairperson role but are not assigned to any department. Contact an administrator to be assigned." />
@else
    <x-layout.card-section
        title="Department Overview"
        icon="bx-buildings"
        :subtitle="$data['college']['name'] ?? null">
        <div class="mb-4">
            <p class="text-[14px] font-semibold text-[#394056]">{{ $data['department']['name'] }}</p>
            <p class="text-[12px] text-[#72809E] mt-0.5">{{ $data['college']['name'] ?? 'College not set' }}</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach ($data['stats'] as $stat)
                <x-dashboard.stat-card
                    :label="$stat['label']"
                    :value="$stat['value']"
                    :icon="$stat['icon']"
                    :color="$stat['color']" />
            @endforeach
        </div>
    </x-layout.card-section>

    <x-layout.card-section title="Syllabus Summary" icon="bx-notepad" class="mt-4">
        @if (empty($data['syllabus_stats']))
            <x-feedback-status.empty-state
                icon="bx-notepad"
                title="No syllabus data"
                message="There are no syllabi in your department's programs yet." />
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach ($data['syllabus_stats'] as $stat)
                    <x-dashboard.stat-card
                        :label="$stat['label']"
                        :value="$stat['value']"
                        :icon="$stat['icon']"
                        :color="$stat['color']" />
                @endforeach
            </div>
        @endif
    </x-layout.card-section>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mt-4">
        <x-dashboard.issue-list
            :issues="$data['health']['warnings']"
            title="Academic Health Warnings"
            empty-title="Academic setup looks complete"
            empty-message="No missing PEOs, POs, COs, curriculum maps, or calendar issues were found." />

        <x-dashboard.issue-list
            :issues="$data['health']['mapping_issues']"
            title="Mapping Validation"
            type="mapping"
            empty-title="Mappings look consistent"
            empty-message="No unmapped POs, COs, or curriculum gaps were found." />
    </div>
@endif
