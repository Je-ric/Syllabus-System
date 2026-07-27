<x-layout.card-section title="System Overview" icon="bx-grid-alt">
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
        @foreach ($data['stats'] as $stat)
            <x-dashboard.stat-card
                :label="$stat['label']"
                :value="$stat['value']"
                :icon="$stat['icon']"
                :color="$stat['color']" />
        @endforeach
    </div>
</x-layout.card-section>
