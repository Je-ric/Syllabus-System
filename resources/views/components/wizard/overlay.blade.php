@props([
    'title'    => 'Saving changes…',
    'subtitle' => 'Please wait',
    'dual'     => false,
])

<div {{ $attributes->class(['fixed inset-0 z-50 flex items-center justify-center']) }}>
    <div class="absolute inset-0 bg-[#09090b]/40 backdrop-blur-[3px]"></div>
    <div class="relative flex flex-col items-center gap-5 px-10 py-8 rounded-[20px] border border-[#e4e4e7] bg-white"
         style="width:300px; box-shadow: 0 8px 40px rgba(0,0,0,0.12);">

        {{-- Spinner --}}
        <div class="relative w-14 h-14 flex items-center justify-center">
            <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none" style="color:#ffd700;">
                <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="3"
                    stroke-linecap="round" stroke-dasharray="130" stroke-dashoffset="90" />
            </svg>
            <svg class="absolute inset-0" viewBox="0 0 64 64" fill="none" style="color:#e4e4e7;">
                <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="2" />
            </svg>
            @if ($dual)
                <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none"
                    style="color:#16a34a; animation-direction:reverse; animation-duration:1.4s;">
                    <circle cx="32" cy="32" r="18" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-dasharray="60" stroke-dashoffset="40" />
                </svg>
            @endif
            <img src="{{ asset('assets/CLSU-LOGO-removebg.png') }}" alt="CLSU"
                class="relative w-8 h-8 object-contain" />
        </div>

        {{-- Text --}}
        <div class="text-center">
            <p class="text-[13px] font-bold text-[#09090b]">{{ $title }}</p>
            <p class="text-[11px] mt-0.5 text-[#71717a]">{{ $subtitle }}</p>
        </div>

        {{-- Slot: extra info rows (used by Save as Done) --}}
        @if ($slot->isNotEmpty())
            <div class="w-full space-y-1.5">{{ $slot }}</div>
            <div class="w-12 h-[2px] rounded-full bg-[#16a34a]"></div>
        @else
            {{-- Bounce dots --}}
            <div class="flex justify-center gap-1">
                <div class="w-1.5 h-1.5 bg-[#16a34a] rounded-full animate-bounce"></div>
                <div class="w-1.5 h-1.5 bg-[#16a34a] rounded-full animate-bounce" style="animation-delay:0.1s;"></div>
                <div class="w-1.5 h-1.5 bg-[#16a34a] rounded-full animate-bounce" style="animation-delay:0.2s;"></div>
            </div>
        @endif

    </div>
</div>
