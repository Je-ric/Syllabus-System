@props([
    'class'   => '',
    'modalId' => null,
])

<header {{ $attributes->merge(['class' => "px-6 py-4 border-b border-[#e2e8f0] bg-white flex-shrink-0 $class"]) }}>
    <div class="flex items-center justify-between gap-4">
        <div class="flex-1 min-w-0 text-[15px] font-bold text-[#0f172a]">
            {{ $slot }}
        </div>

        @if ($modalId)
            <button
                type="button"
                onclick="document.getElementById('{{ $modalId }}').close()"
                class="shrink-0 rounded-lg p-1.5 text-[#94a3b8]
                       hover:bg-[#f8fafc] hover:text-[#475569]
                       transition-colors duration-150"
                aria-label="Close">
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        @endif
    </div>
</header>
