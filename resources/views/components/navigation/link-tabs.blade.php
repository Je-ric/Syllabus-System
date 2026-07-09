@props(['tabs' => [], 'active' => null])

<div class="flex items-center justify-between border-b border-[#e4e4e7] mb-4">
    <div class="flex gap-1">
        @foreach ($tabs as $tab)
            @php $isActive = ($active === $tab['id']); @endphp
            <a href="{{ $tab['href'] }}"
               class="relative inline-flex items-center gap-1.5 px-3 pb-3 pt-1 text-[13px] font-medium transition-colors
                      {{ $isActive ? 'text-[#16a34a]' : 'text-[#71717a] hover:text-[#18181b]' }}"
               @if($isActive) aria-current="page" @endif>
                @if (isset($tab['icon']))
                    <i class="bx {{ $tab['icon'] }} text-[15px] leading-none"></i>
                @endif
                {{ $tab['label'] }}
                @if (isset($tab['count']))
                    <span class="rounded-full px-1.5 py-0.5 text-[10px] font-semibold
                                 {{ $isActive ? 'bg-[#dcfce7] text-[#166534]' : 'bg-[#f4f4f5] text-[#71717a]' }}">
                        {{ $tab['count'] }}
                    </span>
                @endif
                <span class="absolute bottom-0 left-0 h-0.5 w-full rounded-full
                             {{ $isActive ? 'bg-[#16a34a]' : 'bg-transparent' }}"></span>
            </a>
        @endforeach
    </div>

    @isset($actions)
        <div class="flex items-center gap-2 pb-3">{{ $actions }}</div>
    @endisset
</div>
