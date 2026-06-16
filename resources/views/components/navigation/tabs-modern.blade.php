@props([
    'tabs' => [],
    'defaultTab' => null,
    'preserveState' => true,
    'stateKey' => null,
    'title' => null,
    'description' => null,
    'class' => '',
])

@php
    $tabIds = collect($tabs)->pluck('id')->toArray();
@endphp

<div x-cloak x-data="{
    tabIds: @js($tabIds),
    storageKey: @js($stateKey ?? 'tabs:' . request()->path()),
    activeTab: @js($defaultTab),

    init() {
        const urlTab = new URLSearchParams(window.location.search).get('tab');
        const storedTab = localStorage.getItem(this.storageKey);

        const preferred = urlTab || storedTab || this.activeTab;

        this.activeTab = this.tabIds.includes(preferred) ?
            preferred :
            (this.tabIds[0] ?? this.activeTab);

        @if ($preserveState) const u = new URL(window.location);
            u.searchParams.set('tab', this.activeTab);
            window.history.replaceState({}, '', u);
            localStorage.setItem(this.storageKey, this.activeTab); @endif
    },

    setTab(tab) {
        if (!this.tabIds.includes(tab)) return;

        this.activeTab = tab;

        @if ($preserveState) const u = new URL(window.location);

            u.searchParams.set('tab', tab);
            u.searchParams.delete('page');

            window.history.pushState({}, '', u);

            localStorage.setItem(this.storageKey, tab); @endif
    }
}"
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm {{ $class }}">

    {{-- Header --}}
    @if ($title || $description || isset($actions))
        <div class="flex items-start justify-between gap-4 px-5 pt-5">

            <div class="min-w-0">
                @if ($title)
                    <h2 class="text-lg font-semibold text-slate-800">
                        {{ $title }}
                    </h2>
                @endif

                @if ($description)
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $description }}
                    </p>
                @endif
            </div>

            @isset($actions)
                <div class="shrink-0">
                    {{ $actions }}
                </div>
            @endisset

        </div>
    @endif

    {{-- Tabs --}}
    <div class="px-5 pt-4">

        <nav role="tablist" aria-label="Tabs" class="inline-flex rounded-xl bg-slate-100 p-1">

            @foreach ($tabs as $tab)
                <button type="button" role="tab" :aria-selected="activeTab === '{{ $tab['id'] }}'"
                    @click="setTab('{{ $tab['id'] }}')"
                    :class="activeTab === '{{ $tab['id'] }}'
                        ?
                        'bg-white text-emerald-700 shadow-sm' :
                        'text-slate-500 hover:text-slate-700'"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200">

                    @if (isset($tab['icon']))
                        <i class="bx {{ $tab['icon'] }} text-base leading-none"></i>
                    @endif

                    <span>{{ $tab['label'] }}</span>

                    @if (isset($tab['count']))
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">
                            {{ $tab['count'] }}
                        </span>
                    @endif

                </button>
            @endforeach

        </nav>

    </div>

    {{-- Content --}}
    <div class="mt-5 border-t border-slate-100 p-5">

        @foreach ($tabIds as $tabId)
            <div x-show="activeTab === '{{ $tabId }}'" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                role="tabpanel" class="space-y-4">

                {{ ${'slot_' . $tabId} ?? '' }}

            </div>
        @endforeach

    </div>

</div>
