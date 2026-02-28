@props([
    'tabs'          => [],
    'defaultTab'    => null,
    'preserveState' => true,
    'stateKey'      => null,
    'class'         => '',
])

@php
    $tabIds = collect($tabs)->pluck('id')->toArray();
@endphp

{{--
    x-cloak prevents all tab panels flashing before Alpine initialises.
    Add this to your app.css if not already present:
      [x-cloak] { display: none !important; }
--}}
<div
    x-cloak
    x-data="{
        tabIds:     @js($tabIds),
        storageKey: @js($stateKey ?? 'tabs:' . request()->path()),
        activeTab:  @js($defaultTab),

        init() {
            const urlTab    = new URLSearchParams(window.location.search).get('tab');
            const storedTab = localStorage.getItem(this.storageKey);
            const preferred = urlTab || storedTab || this.activeTab;
            this.activeTab  = this.tabIds.includes(preferred)
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
    class="{{ $class }}">

    {{-- ── Tab bar ─────────────────────────────────────────────────────── --}}
    <div class="border-b border-slate-200 bg-white">
        <nav class="flex flex-wrap px-4 gap-x-1" aria-label="Tabs" role="tablist">
            @foreach ($tabs as $tab)
                <button
                    type="button"
                    role="tab"
                    :aria-selected="activeTab === '{{ $tab['id'] }}'"
                    @click="setTab('{{ $tab['id'] }}')"
                    :class="activeTab === '{{ $tab['id'] }}'
                        ? 'border-b-2 border-emerald-600 text-emerald-700 font-semibold'
                        : 'border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    class="inline-flex items-center gap-2 whitespace-nowrap
                           -mb-px py-3 px-3 text-sm
                           transition-colors duration-150
                           focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-1">
                    @if (isset($tab['icon']))
                        <i class="bx {{ $tab['icon'] }} text-base leading-none"></i>
                    @endif
                    <span>{{ $tab['label'] }}</span>
                </button>
            @endforeach
        </nav>
    </div>

    {{-- ── Tab panels ──────────────────────────────────────────────────── --}}
    <div class="py-5">
        @foreach ($tabIds as $tabId)
            <div
                x-show="activeTab === '{{ $tabId }}'"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                role="tabpanel"
                class="space-y-4">
                {{ ${'slot_' . $tabId} ?? '' }}
            </div>
        @endforeach
    </div>
</div>

{{--
USAGE:
<x-navigation.tabs-modern
    :tabs="[
        ['id' => 'peo', 'label' => 'PEOs', 'icon' => 'bx-graduation'],
        ['id' => 'po',  'label' => 'POs',  'icon' => 'bx-target'],
    ]"
    defaultTab="peo"
    stateKey="programs-tabs">

    <x-slot name="slot_peo">...</x-slot>
    <x-slot name="slot_po">...</x-slot>
</x-navigation.tabs-modern>
--}}
