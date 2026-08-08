@props([
    'title'       => null,
    'icon'        => null,
    'count'       => null,
    'subtitle'    => null,
    'padded'      => true,
    'collapsible' => false,
])
{{-- border-[#E3E8EB]  --}}

@if($collapsible)
<div 
    x-data="{ open: true }"
    {{ $attributes->merge([
    'class' => 'rounded-2xl border
    border-white
    bg-white/[.97] backdrop-blur-sm overflow-hidden
                shadow-[0_1px_2px_rgba(16,24,40,0.03),0_2px_8px_rgba(16,24,40,0.06),0_0_0_0.5px_rgba(16,24,40,0.04)]
                relative'
]) }}>

    {{-- 2 px brand accent rail at the top of the card --}}
    <div class="absolute inset-x-0 top-0 h-[2px] rounded-t-2xl
                bg-[linear-gradient(90deg,#00D88B_0%,#00C075_50%,rgba(0,216,139,0)_100%)]
                opacity-70 pointer-events-none" aria-hidden="true"></div>

    {{-- Card header --}}
    <div 
        x-on:click="open = !open"
        class="px-4 pt-3.5 pb-3 flex items-center gap-2.5
            border-b border-[linear-gradient(90deg,#E3E8EB,#F1F3F5_70%,transparent)]
            cursor-pointer hover:bg-slate-50 transition-colors"
         style="border-bottom: 1px solid; border-image: linear-gradient(90deg,#E3E8EB 0%,#F1F3F5 60%,transparent 100%) 1;">

        @if($icon)
            <span class="flex items-center justify-center w-6 h-6 rounded-[7px]
                         bg-[#EDFFF8] border border-[#AEFFE2] shrink-0">
                <i class="bx {{ $icon }} text-[#00965F] text-[13px] leading-none"></i>
            </span>
        @endif

        <div class="flex flex-col gap-0.5 min-w-0">
            <p class="text-[11.5px] font-bold uppercase tracking-[0.09em] text-[#394056] leading-none">
                {{ $title }}
            </p>
            @if ($subtitle)
                <p class="text-[11px] text-[#93A1AF] leading-none truncate mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="ml-auto flex items-center gap-2 shrink-0">
            {{ $actions ?? '' }}

            <i class="bx text-[#94a3b8] text-lg transition-transform duration-200"
               x-bind:class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>

            @if ($count !== null)
                <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5
                             rounded-full bg-[#D5FFF0] text-[#06754E] text-[10px] font-bold
                             border border-[#AEFFE2] shadow-[inset_0_1px_0_rgba(255,255,255,0.6)]">
                    {{ $count }}
                </span>
            @endif
        </div>
    </div>

    {{-- Card body --}}
    <div x-show="open" x-collapse class="{{ $padded ? 'p-4' : '' }}">
        {{ $slot }}
    </div>

</div>
@else
<div {{ $attributes->merge([
    'class' => 'rounded-2xl border
    border-white
    bg-white/[.97] backdrop-blur-sm overflow-hidden
                shadow-[0_1px_2px_rgba(16,24,40,0.03),0_2px_8px_rgba(16,24,40,0.06),0_0_0_0.5px_rgba(16,24,40,0.04)]
                relative'
]) }}>

    {{-- 2 px brand accent rail at the top of the card --}}
    <div class="absolute inset-x-0 top-0 h-[2px] rounded-t-2xl
                bg-[linear-gradient(90deg,#00D88B_0%,#00C075_50%,rgba(0,216,139,0)_100%)]
                opacity-70 pointer-events-none" aria-hidden="true"></div>

    {{-- Card header --}}
    <div class="px-4 pt-3.5 pb-3 flex items-center gap-2.5
                border-b border-[linear-gradient(90deg,#E3E8EB,#F1F3F5_70%,transparent)]"
         style="border-bottom: 1px solid; border-image: linear-gradient(90deg,#E3E8EB 0%,#F1F3F5 60%,transparent 100%) 1;">

        @if($icon)
            <span class="flex items-center justify-center w-6 h-6 rounded-[7px]
                         bg-[#EDFFF8] border border-[#AEFFE2] shrink-0">
                <i class="bx {{ $icon }} text-[#00965F] text-[13px] leading-none"></i>
            </span>
        @endif

        <div class="flex flex-col gap-0.5 min-w-0">
            <p class="text-[11.5px] font-bold uppercase tracking-[0.09em] text-[#394056] leading-none">
                {{ $title }}
            </p>
            @if ($subtitle)
                <p class="text-[11px] text-[#93A1AF] leading-none truncate mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="ml-auto flex items-center gap-2 shrink-0">
            {{ $actions ?? '' }}

            @if ($count !== null)
                <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5
                             rounded-full bg-[#D5FFF0] text-[#06754E] text-[10px] font-bold
                             border border-[#AEFFE2] shadow-[inset_0_1px_0_rgba(255,255,255,0.6)]">
                    {{ $count }}
                </span>
            @endif
        </div>
    </div>

    {{-- Card body --}}
    <div class="{{ $padded ? 'p-4' : '' }}">
        {{ $slot }}
    </div>

</div>
@endif
