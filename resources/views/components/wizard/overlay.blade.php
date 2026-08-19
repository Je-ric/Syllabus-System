@props([
    'title'    => 'Saving changes…',
    'subtitle' => 'Please wait',
    'dual'     => false,
])

<div {{ $attributes->class(['fixed inset-0 z-50 flex items-center justify-center']) }}>
    <div class="absolute inset-0 bg-[#0B1520]/55 backdrop-blur-[6px]"></div>

    <div class="relative flex flex-col items-center gap-5 px-10 py-9 rounded-[18px] overflow-hidden"
         style="width:300px;
                background: linear-gradient(180deg, rgba(255,255,255,0.97) 0%, rgba(255,255,255,1) 100%);
                border: 1px solid rgba(0,150,95,0.18);
                box-shadow: 0 2px 8px rgba(16,24,40,0.06), 0 24px 64px rgba(0,150,95,0.20);">

        {{-- Ambient glow accents --}}
        <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full opacity-40 blur-2xl pointer-events-none"
             style="background: radial-gradient(circle, #00D88B 0%, transparent 70%);"></div>
        <div class="absolute -bottom-10 -left-10 w-28 h-28 rounded-full opacity-25 blur-2xl pointer-events-none"
             style="background: radial-gradient(circle, #3197D6 0%, transparent 70%);"></div>

        {{-- Spinner --}}
        <div class="relative w-16 h-16 flex items-center justify-center">
            <svg class="absolute inset-0" viewBox="0 0 64 64" fill="none" style="color:#E3E8EB;">
                <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="2" />
            </svg>
            <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none" style="animation-duration:1.1s;">
                <defs>
                    <linearGradient id="overlaySpinnerGrad" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#00D88B"/>
                        <stop offset="100%" stop-color="#06754E"/>
                    </linearGradient>
                </defs>
                <circle cx="32" cy="32" r="27" stroke="url(#overlaySpinnerGrad)" stroke-width="3"
                    stroke-linecap="round" stroke-dasharray="130" stroke-dashoffset="90" />
            </svg>
            @if ($dual)
                <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none"
                    style="animation-direction:reverse; animation-duration:1.6s;">
                    <circle cx="32" cy="32" r="18" stroke="#3197D6" stroke-width="2"
                        stroke-linecap="round" stroke-dasharray="60" stroke-dashoffset="40" opacity="0.6" />
                </svg>
            @endif
            <img src="{{ asset('assets/CLSU-LOGO-removebg.png') }}" alt="CLSU"
                class="relative w-9 h-9 object-contain animate-[pulse_2.2s_ease-in-out_infinite]" />
        </div>

        {{-- Text --}}
        <div class="relative text-center">
            <p class="text-[13.5px] font-bold text-[#1D2836] tracking-tight">{{ $title }}</p>
            <p class="text-[11.5px] mt-1 text-[#72809E]">{{ $subtitle }}</p>
        </div>

        @if ($slot->isNotEmpty())
            <div class="relative w-full space-y-1.5">{{ $slot }}</div>
            <div class="relative w-12 h-[2px] rounded-full" style="background:linear-gradient(90deg,#00D88B,#06754E);"></div>
        @else
            <div class="relative flex justify-center gap-1.5">
                <div class="w-1.5 h-1.5 rounded-full animate-bounce" style="background:#00965F;"></div>
                <div class="w-1.5 h-1.5 rounded-full animate-bounce" style="background:#00965F; animation-delay:0.12s;"></div>
                <div class="w-1.5 h-1.5 rounded-full animate-bounce" style="background:#00965F; animation-delay:0.24s;"></div>
            </div>
        @endif

    </div>
</div>