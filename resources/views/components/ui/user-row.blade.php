@props(['user', 'canRemove' => false, 'removeModalId' => null])

<div {{ $attributes->class(['flex items-start justify-between gap-3']) }}>
    <div class="min-w-0">
        <p class="text-sm font-semibold text-[#394056] truncate" title="{{ $user->name }}">
            {{ $user->name }}
        </p>
        <p class="text-xs text-[#72809E] mt-0.5 truncate" title="{{ $user->email }}">
            {{ $user->email }}
        </p>
    </div>

    @if ($canRemove && $removeModalId)
        <button type="button"
            onclick="document.getElementById('{{ $removeModalId }}').showModal()"
            class="shrink-0 p-1.5 rounded-[8px] text-[#E52F28]
                   hover:bg-[#FFE3E2] hover:text-[#D21B14]
                   active:bg-[#FFA2A2]
                   transition-all duration-200
                   shadow-[0_1px_2px_rgba(16,24,40,0.05)]"
            title="Remove">
            <i class="bx bx-trash text-base leading-none"></i>
        </button>
    @endif
</div>
