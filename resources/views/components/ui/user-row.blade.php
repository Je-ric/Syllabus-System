@props(['user', 'canRemove' => false, 'removeModalId' => null])

<div {{ $attributes->class(['flex items-start justify-between gap-3']) }}>
    <div class="min-w-0">
        <p class="text-sm font-semibold text-slate-800 truncate" title="{{ $user->name }}">
            {{ $user->name }}
        </p>
        <p class="text-xs text-slate-500 mt-0.5 truncate" title="{{ $user->email }}">
            {{ $user->email }}
        </p>
    </div>

    @if ($canRemove && $removeModalId)
        <button type="button"
            onclick="document.getElementById('{{ $removeModalId }}').showModal()"
            class="shrink-0 p-1.5 rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"
            title="Remove">
            <i class="bx bx-trash text-base leading-none"></i>
        </button>
    @endif
</div>
