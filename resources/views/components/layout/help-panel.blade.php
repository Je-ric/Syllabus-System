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
        class="fixed inset-y-0 right-0 z-40 flex flex-col bg-white w-full max-w-lg border-l border-[#E3E8EB]"
        style="box-shadow: -4px 0 32px rgba(16,24,40,0.10);"
    >
        {{-- Header --}}
        <div class="shrink-0 flex items-center justify-between gap-3 px-5 py-4 border-b border-[#E3E8EB]">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-9 h-9 rounded-[10px] bg-[#D5FFF0] border border-[#AEFFE2]">
                    <i class="bx bx-help-circle text-lg leading-none text-[#06754E]"></i>
                </span>
                <div>
                    <p class="text-[14px] font-semibold text-[#1D2836] leading-tight">How to Use</p>
                    <p class="text-[12px] text-[#72809E] mt-0.5">Quick reference guide</p>
                </div>
            </div>
            <button
                type="button"
                x-on:click="open = false"
                class="flex items-center justify-center w-8 h-8 rounded-[8px] text-[#93A1AF] hover:text-[#394056] hover:bg-[#F1F3F5]
                       focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C075]/30
                       transition-colors duration-150"
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
        <div class="shrink-0 px-5 py-3 border-t border-[#E3E8EB] bg-[#F9FAFA]">
            <p class="text-[11px] text-[#93A1AF] text-center">Press <kbd class="px-1.5 py-0.5 rounded-[6px] bg-white border border-[#E3E8EB] font-mono text-[10px] text-[#394056]">Esc</kbd> to close</p>
        </div>
    </div>
</div>