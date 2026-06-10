@props([
    'id',
    'title'    => '',
    'position' => 'right',
    'width'    => 'w-80',
])

{{--
    Usage:
    <x-offcanvas id="my-drawer" title="My Panel">
        content here
    </x-offcanvas>

    Trigger (anywhere on the page):
    <label for="my-drawer" class="...">Open</label>
    OR via JS: document.getElementById('my-drawer').checked = true
--}}

<div class="drawer {{ $position === 'left' ? 'drawer-start' : 'drawer-end' }} z-50">
    <input id="{{ $id }}" type="checkbox" class="drawer-toggle" />

    {{-- Overlay + panel --}}
    <div class="drawer-side">
        <label for="{{ $id }}" aria-label="close sidebar" class="drawer-overlay"></label>

        <div class="{{ $width }} min-h-full bg-white flex flex-col shadow-2xl">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#e2e8f0] bg-[#f8fafc] shrink-0">
                @if($title)
                    <p class="text-[13px] font-bold text-[#0f172a]">{{ $title }}</p>
                @endif
                <label for="{{ $id }}"
                    class="ml-auto p-1.5 rounded-lg text-[#94a3b8] hover:text-[#0f172a] hover:bg-[#f1f5f9] cursor-pointer transition-colors">
                    <i class="bx bx-x text-xl leading-none"></i>
                </label>
            </div>

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto px-5 py-4">
                {{ $slot }}
            </div>

        </div>
    </div>
</div>
