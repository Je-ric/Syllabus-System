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

<div
    x-cloak
    x-data="{
        tabIds: @js($tabIds),
        storageKey: @js($stateKey ?? 'tabs:' . request()->path()),
        activeTab: @js($defaultTab),

        init() {
            const urlTab = new URLSearchParams(window.location.search).get('tab');
            const storedTab = localStorage.getItem(this.storageKey);

            const preferred = urlTab || storedTab || this.activeTab;

            this.activeTab = this.tabIds.includes(preferred)
                ? preferred
                : (this.tabIds[0] ?? this.activeTab);

            @if ($preserveState)
                const u = new URL(window.location);
                u.searchParams.set('tab', this.activeTab);
                window.history.replaceState({}, '', u);
                localStorage.setItem(this.storageKey, this.activeTab);
            @endif
        },

        setTab(tab) {
            if (!this.tabIds.includes(tab)) return;

            this.activeTab = tab;

            @if ($preserveState)
                const u = new URL(window.location);
                u.searchParams.set('tab', tab);
                u.searchParams.delete('page');
                window.history.pushState({}, '', u);
                localStorage.setItem(this.storageKey, tab);
            @endif
        }
    }"
    class="rounded-2xl border border-slate-200 bg-white shadow-sm {{ $class }}"
>

    {{-- HEADER --}}
    @if ($title || $description || isset($actions))
        <div class="flex items-start justify-between gap-4 px-6 pt-6">
            <div>
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
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    {{-- TABS (Material style like your sample) --}}
    <div class="flex items-center justify-between border-b border-slate-200 px-6 mt-4">

        <div class="flex gap-8">
            @foreach ($tabs as $tab)
                <button
                    type="button"
                    role="tab"
                    @click="setTab('{{ $tab['id'] }}')"
                    :aria-selected="activeTab === '{{ $tab['id'] }}'"
                    class="relative pb-4 text-sm font-medium transition-colors"
                    :class="activeTab === '{{ $tab['id'] }}'
                        ? 'text-emerald-600'
                        : 'text-slate-500 hover:text-slate-700'"
                >
                    <div class="flex items-center gap-2">

                        @if (isset($tab['icon']))
                            <i class="bx {{ $tab['icon'] }} text-base"></i>
                        @endif

                        <span>{{ $tab['label'] }}</span>

                        @if (isset($tab['count']))
                            <span
                                class="ml-1 rounded-full px-2 py-0.5 text-[10px]"
                                :class="activeTab === '{{ $tab['id'] }}'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-slate-100 text-slate-600'"
                            >
                                {{ $tab['count'] }}
                            </span>
                        @endif
                    </div>

                    {{-- ACTIVE INDICATOR BAR --}}
                    <span
                        class="absolute bottom-0 left-0 h-0.5 w-full rounded-full transition-all"
                        :class="activeTab === '{{ $tab['id'] }}'
                            ? 'bg-emerald-600'
                            : 'bg-transparent'"
                    ></span>
                </button>
            @endforeach
        </div>

        {{-- RIGHT ACTIONS SLOT (grid/list etc.) --}}
        @isset($tabActions)
            <div class="flex items-center gap-2 pb-3">
                {{ $tabActions }}
            </div>
        @endisset

    </div>

    {{-- CONTENT --}}
    <div class="p-6">

        @foreach ($tabIds as $tabId)
            <div
                x-show="activeTab === '{{ $tabId }}'"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                role="tabpanel"
            >
                {{ ${'slot_' . $tabId} ?? '' }}
            </div>
        @endforeach

    </div>

</div>