@props([
    'title'    => 'Saving changes…',
    'subtitle' => 'Please wait',
    'dual'     => false,
])

<div {{ $attributes->class(['fixed inset-0 z-50 flex items-center justify-center']) }}>
    <div class="absolute inset-0 bg-[#1D2836]/45 backdrop-blur-[3px]"></div>
    <div class="relative flex flex-col items-center gap-5 px-10 py-8 rounded-[14px] border border-[#E3E8EB] bg-white"
         style="width:300px; box-shadow: 0 8px 40px rgba(16,24,40,0.18);">

        {{-- Spinner --}}
        <div class="relative w-14 h-14 flex items-center justify-center">
            {{-- Track ring --}}
            <svg class="absolute inset-0" viewBox="0 0 64 64" fill="none" style="color:#E3E8EB;">
                <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="2" />
            </svg>
            {{-- Spinning arc — Emerald 700 --}}
            <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none" style="color:#00965F;">
                <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="3"
                    stroke-linecap="round" stroke-dasharray="130" stroke-dashoffset="90" />
            </svg>
            @if ($dual)
                {{-- Inner counter-spin — Emerald 800 --}}
                <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none"
                    style="color:#06754E; animation-direction:reverse; animation-duration:1.4s;">
                    <circle cx="32" cy="32" r="18" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-dasharray="60" stroke-dashoffset="40" />
                </svg>
            @endif
            <img src="{{ asset('assets/CLSU-LOGO-removebg.png') }}" alt="CLSU"
                class="relative w-8 h-8 object-contain" />
        </div>

        {{-- Text --}}
        <div class="text-center">
            <p class="text-[13px] font-bold text-[#394056]">{{ $title }}</p>
            <p class="text-[11px] mt-0.5 text-[#72809E]">{{ $subtitle }}</p>
        </div>

        {{-- Slot: extra info rows (used by Save as Done) --}}
        @if ($slot->isNotEmpty())
            <div class="w-full space-y-1.5">{{ $slot }}</div>
            <div class="w-12 h-[2px] rounded-full bg-[#00965F]"></div>
        @else
            {{-- Bounce dots — Emerald 700 --}}
            <div class="flex justify-center gap-1.5">
                <div class="w-1.5 h-1.5 bg-[#00965F] rounded-full animate-bounce"></div>
                <div class="w-1.5 h-1.5 bg-[#00965F] rounded-full animate-bounce" style="animation-delay:0.12s;"></div>
                <div class="w-1.5 h-1.5 bg-[#00965F] rounded-full animate-bounce" style="animation-delay:0.24s;"></div>
            </div>
        @endif

    </div>
</div>
