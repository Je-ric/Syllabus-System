<button type="button"
    onclick="window.dispatchEvent(new CustomEvent('open-help-panel'))"
    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-[10px]
           text-[#394056] bg-white border border-[#E3E8EB]
           hover:bg-[#EDFFF8] hover:border-[#00D88B] hover:text-[#00965F]
           active:bg-[#D5FFF0] active:text-[#076042] active:scale-[0.96]
           focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C075]/30
           transition-all duration-200
           shadow-[0_1px_2px_rgba(16,24,40,0.05)]
           [&_i]:leading-none"
    aria-label="Open how to use guide">
    <i class="bx bx-help-circle text-sm"></i> How to Use
</button>