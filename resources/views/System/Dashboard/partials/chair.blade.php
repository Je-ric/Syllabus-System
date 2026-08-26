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

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach ($data['stats'] as $stat)
                <x-dashboard.stat-card
                    :label="$stat['label']"
                    :value="$stat['value']"
                    :icon="$stat['icon']"
                    :color="$stat['color']" />
            @endforeach
        </div>
    </x-layout.card-section>
@endif
