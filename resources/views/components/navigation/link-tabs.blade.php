@props(['tabs' => [], 'active' => null])

<div class="flex items-center justify-between border-b border-[#E3E8EB] mb-4">
    <div class="flex gap-1">
        @foreach ($tabs as $tab)
            @php $isActive = ($active === $tab['id']); @endphp
            <a href="{{ $tab['href'] }}"
               class="relative inline-flex items-center gap-1.5 px-3 pb-3 pt-1 text-[13px] font-medium
                      transition-colors duration-150
                      {{ $isActive ? 'text-[#00965F]' : 'text-[#72809E] hover:text-[#394056]' }}"
               @if($isActive) aria-current="page" @endif>

                @if (isset($tab['icon']))
                    <i class="bx {{ $tab['icon'] }} text-[14px] leading-none"></i>
                @endif

                {{ $tab['label'] }}

                @if (isset($tab['count']))
                    <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold
                                 {{ $isActive
                                    ? 'bg-[#D5FFF0] text-[#06754E] ring-1 ring-[#00965F]'
                                    : 'bg-[#F1F3F5] text-[#72809E]' }}">
                        {{ $tab['count'] }}
                    </span>
                @endif

                {{-- Active underline --}}
                <span class="absolute bottom-0 left-0 h-0.5 w-full rounded-full transition-all duration-200
                             {{ $isActive ? 'bg-[#00965F]' : 'bg-transparent' }}"></span>
            </a>
        @endforeach
    </div>

    @isset($actions)
        <div class="flex items-center gap-2 pb-3">{{ $actions }}</div>
    @endisset
</div>
