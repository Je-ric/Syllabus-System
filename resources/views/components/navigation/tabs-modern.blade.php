@props([
    'tabs'          => [],
    'defaultTab'    => null,
    'preserveState' => true,
    'stateKey'      => null,
    'title'         => null,
    'description'   => null,
    'class'         => '',
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
    class="rounded-[20px] border border-[#e4e4e7] bg-white {{ $class }}"
    style="box-shadow: 0 1px 8px rgba(0,0,0,0.05);"
>

    {{-- HEADER --}}
    @if ($title || $description || isset($actions))
        <div class="flex items-start justify-between gap-4 px-6 pt-6">
            <div>
                @if ($title)
                    <h2 class="text-base font-semibold text-[#09090b]">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-0.5 text-sm text-[#71717a]">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    {{-- TABS --}}
    <div class="flex items-center justify-between border-b border-[#e4e4e7] px-6 mt-4">
        <div class="flex gap-6">
            @foreach ($tabs as $tab)
                <button
                    type="button"
                    role="tab"
                    @click="setTab('{{ $tab['id'] }}')"
                    :aria-selected="activeTab === '{{ $tab['id'] }}'"
                    class="relative pb-3.5 text-[13px] font-medium transition-colors"
                    :class="activeTab === '{{ $tab['id'] }}'
                        ? 'text-[#16a34a]'
                        : 'text-[#71717a] hover:text-[#18181b]'"
                >
                    <div class="flex items-center gap-1.5">
                        @if (isset($tab['icon']))
                            <i class="bx {{ $tab['icon'] }} text-[15px]"></i>
                        @endif
                        <span>{{ $tab['label'] }}</span>
                        @if (isset($tab['count']))
                            <span
                                class="ml-0.5 rounded-full px-1.5 py-0.5 text-[10px] font-semibold"
                                :class="activeTab === '{{ $tab['id'] }}'
                                    ? 'bg-[#dcfce7] text-[#166534]'
                                    : 'bg-[#f4f4f5] text-[#71717a]'"
                            >{{ $tab['count'] }}</span>
                        @endif
                    </div>

                    {{-- Active indicator --}}
                    <span
                        class="absolute bottom-0 left-0 h-0.5 w-full rounded-full transition-all duration-200"
                        :class="activeTab === '{{ $tab['id'] }}'
                            ? 'bg-[#16a34a]'
                            : 'bg-transparent'"
                    ></span>
                </button>
            @endforeach
        </div>

        @isset($tabActions)
            <div class="flex items-center gap-2 pb-3">{{ $tabActions }}</div>
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
