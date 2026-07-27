@if ($data['no_assignment'])
    <x-feedback-status.alert type="warning" title="No college assigned"
        message="You have the Dean role but are not assigned to any college. Contact an administrator to be assigned." />
@else
    <x-layout.card-section
        title="College Overview"
        icon="bx-buildings"
        :subtitle="$data['college']['name']">
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
                message="There are no syllabi in this college's programs yet." />
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

    <x-layout.card-section title="Departments" icon="bx-sitemap" :count="count($data['departments'])" class="mt-4">
        @if (empty($data['departments']))
            <x-feedback-status.empty-state
                icon="bx-sitemap"
                title="No departments"
                message="This college has no departments configured yet." />
        @else
            <div class="divide-y divide-[#F1F3F5]">
                @foreach ($data['departments'] as $department)
                    <div class="flex items-center justify-between gap-3 py-2.5 first:pt-0 last:pb-0">
                        <p class="text-[13px] font-medium text-[#394056] truncate">{{ $department['name'] }}</p>
                        <span class="shrink-0 text-[11px] font-semibold text-[#72809E]">
                            {{ $department['program_count'] }} {{ Str::plural('program', $department['program_count']) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </x-layout.card-section>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mt-4">
        <x-dashboard.issue-list
            :issues="$data['health']['warnings']"
            title="Academic Health Warnings"
            empty-title="Academic setup looks complete"
            empty-message="No missing PEOs, POs, COs, curriculum maps, or calendar issues were found in this college." />

        <x-dashboard.issue-list
            :issues="$data['health']['mapping_issues']"
            title="Mapping Validation"
            type="mapping"
            empty-title="Mappings look consistent"
            empty-message="No unmapped POs, COs, or curriculum gaps were found in this college." />
    </div>
@endif
