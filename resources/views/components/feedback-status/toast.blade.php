@props([
    'type' => 'info',
    'message' => null,
])

@php
    $toastStyles = [
        'success' => ['container' => 'bg-emerald-50 text-emerald-900 border-emerald-200', 'iconBg' => 'bg-emerald-500', 'icon' => 'bx-check', 'title' => 'Success'],
        'error' => ['container' => 'bg-rose-50 text-rose-900 border-rose-200', 'iconBg' => 'bg-rose-500', 'icon' => 'bx-x', 'title' => 'Error'],
        'warning' => ['container' => 'bg-amber-50 text-amber-900 border-amber-200', 'iconBg' => 'bg-amber-500', 'icon' => 'bx-error', 'title' => 'Attention'],
        'info' => ['container' => 'bg-sky-50 text-sky-900 border-sky-200', 'iconBg' => 'bg-sky-500', 'icon' => 'bx-info-circle', 'title' => 'Information'],
    ];

    $toast = $toastStyles[$type] ?? $toastStyles['info'];
@endphp

@if($message)
    <div
        x-data="{ show: false }"
        x-init="show = true; setTimeout(() => show = false, 5200)"
        x-show="show"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-3"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave-end="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-3"
        class="fixed inset-x-3 top-4 sm:inset-x-auto sm:right-5 z-9999 w-auto sm:w-md max-w-[calc(100vw-1.5rem)] rounded-xl border p-3 shadow-xl backdrop-blur-sm {{ $toast['container'] }}"
        role="status"
        aria-live="polite"
    >
        <div class="flex items-start gap-3">
            <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white {{ $toast['iconBg'] }}">
                <i class="bx {{ $toast['icon'] }} text-lg leading-none"></i>
            </span>

            <div class="min-w-0 flex-1">
                <p class="font-semibold leading-5">{{ $toast['title'] }}</p>
                <p class="mt-1 text-sm leading-5 wrap-break-words">{{ $message }}</p>
            </div>

            <button @click="show = false" class="ml-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-black/5 hover:text-slate-700" aria-label="Close notification">
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        </div>
    </div>
@endif
