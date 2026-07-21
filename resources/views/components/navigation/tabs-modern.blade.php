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
            const urlTab    = new URLSearchParams(window.location.search).get('tab');
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
    class="rounded-[12px] border border-[#E3E8EB] bg-white {{ $class }}"
    style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);"
>

    {{-- Optional title / description header --}}
    @if ($title || $description || isset($actions))
        <div class="flex items-start justify-between gap-4 px-6 pt-5">
            <div>
                @if ($title)
                    <h2 class="text-[14px] font-semibold text-[#394056]">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-0.5 text-[13px] text-[#72809E]">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    {{-- Tab strip --}}
    <div class="flex items-center justify-between border-b border-[#E3E8EB] px-5 mt-4">
        <div class="flex gap-5">
            @foreach ($tabs as $tab)
                <button
                    type="button"
                    role="tab"
                    @click="setTab('{{ $tab['id'] }}')"
                    :aria-selected="activeTab === '{{ $tab['id'] }}'"
                    class="relative pb-3.5 text-[13px] font-medium transition-colors duration-150"
                    :class="activeTab === '{{ $tab['id'] }}'
                        ? 'text-[#00965F]'
                        : 'text-[#72809E] hover:text-[#394056]'"
                >
                    <div class="flex items-center gap-1.5">
                        @if (isset($tab['icon']))
                            <i class="bx {{ $tab['icon'] }} text-[14px] leading-none"></i>
                        @endif
                        <span>{{ $tab['label'] }}</span>
                        @if (isset($tab['count']))
                            <span
                                class="ml-0.5 rounded-full px-1.5 py-0.5 text-[10px] font-bold"
                                :class="activeTab === '{{ $tab['id'] }}'
                                    ? 'bg-[#D5FFF0] text-[#06754E]'
                                    : 'bg-[#F1F3F5] text-[#72809E]'"
                            >{{ $tab['count'] }}</span>
                        @endif
                    </div>

                    {{-- Active underline --}}
                    <span
                        class="absolute bottom-0 left-0 h-[2px] w-full rounded-full transition-all duration-200"
                        :class="activeTab === '{{ $tab['id'] }}'
                            ? 'bg-[#00965F]'
                            : 'bg-transparent'"
                    ></span>
                </button>
            @endforeach
        </div>

        @isset($tabActions)
            <div class="flex items-center gap-2 pb-3">{{ $tabActions }}</div>
        @endisset
    </div>

    {{-- Tab panels --}}
    <div class="p-5">
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
