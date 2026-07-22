{{-- Icon-only ✕ close button — Design.md: Grey 800 rest, Charcoal 500 hover, Grey 300 hover bg --}}
<button type="button" onclick="this.closest('dialog')?.close()"
    class="shrink-0 rounded-lg p-1.5
           text-[#A5B2BD] hover:text-[#394056] hover:bg-[#F1F3F5]
           transition-colors duration-150 focus:outline-none"
    aria-label="Close">
    <i class="bx bx-x text-xl leading-none"></i>
</button>
