@props(['module'])

{{--
    Non-blocking help panel — slides in from the right with NO backdrop,
    so the user can keep interacting with the page while reading.
    Trigger: dispatch 'open-help-panel' on window (any source).
    Close:   click ×, press Escape, or click the trigger button again.
--}}
<div
    x-data="{ open: false }"
    x-on:open-help-panel.window="open = true"
    x-on:keydown.escape.window="open = false"
    class="oswald"
>
    {{-- Panel (no backdrop — intentionally non-blocking) --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-x-4"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-4"
        role="complementary"
        aria-label="How to use this page"
        class="fixed inset-y-0 right-0 z-40 flex flex-col bg-white w-full max-w-lg border-l border-[#e4e4e7]"
        style="box-shadow: -4px 0 32px rgba(0,0,0,0.10);"
    >
        {{-- Header --}}
        <div class="shrink-0 flex items-center justify-between gap-3 px-5 py-4 border-b border-[#e4e4e7]">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-9 h-9 rounded-[10px] bg-[#f0fdf4] border border-[#d1fae5]">
                    <i class="bx bx-help-circle text-lg leading-none text-[#16a34a]"></i>
                </span>
                <div>
                    <p class="text-[14px] font-semibold text-[#09090b] leading-tight">How to Use</p>
                    <p class="text-[12px] text-[#71717a] mt-0.5">Quick reference guide</p>
                </div>
            </div>
            <button
                type="button"
                x-on:click="open = false"
                class="flex items-center justify-center w-8 h-8 rounded-lg text-[#a1a1aa] hover:text-[#09090b] hover:bg-[#f4f4f5] transition-colors"
                aria-label="Close help panel"
            >
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        </div>

        {{-- Scrollable content --}}
        <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-4 space-y-3">
            @includeIf('help.' . $module)
        </div>

        {{-- Footer --}}
        <div class="shrink-0 px-5 py-3 border-t border-[#e4e4e7] bg-[#fafafa]">
            <p class="text-[11px] text-[#a1a1aa] text-center">Press <kbd class="px-1.5 py-0.5 rounded bg-[#f4f4f5] border border-[#e4e4e7] font-mono text-[10px] text-[#52525b]">Esc</kbd> to close</p>
        </div>
    </div>
</div>
