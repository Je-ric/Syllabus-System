@props([
    'type'    => 'info',
    'message' => null,
])

@php
    $toastStyles = [
        'success' => ['container' => 'bg-emerald-50 text-emerald-900 border-emerald-200', 'iconBg' => 'bg-emerald-500', 'icon' => 'bx-check',       'title' => 'Success'],
        'error'   => ['container' => 'bg-rose-50 text-rose-900 border-rose-200',          'iconBg' => 'bg-rose-500',    'icon' => 'bx-x',           'title' => 'Error'],
        'warning' => ['container' => 'bg-amber-50 text-amber-900 border-amber-200',       'iconBg' => 'bg-amber-500',   'icon' => 'bx-error',       'title' => 'Attention'],
        'info'    => ['container' => 'bg-sky-50 text-sky-900 border-sky-200',             'iconBg' => 'bg-sky-500',     'icon' => 'bx-info-circle', 'title' => 'Information'],
    ];
    $toast = $toastStyles[$type] ?? $toastStyles['info'];
@endphp

{{--
    ┌──────────────────────────────────────────────────────────────────────┐
    │  x-feedback-status.toast                                            │
    ├──────────────────────────────────────────────────────────────────────┤
    │  Two modes:                                                          │
    │                                                                      │
    │  1) SESSION FLASH — rendered server-side when $message is set.      │
    │     Controller:  ->with('toast', ['type'=>'success', 'message'=>…]) │
    │     Layout:                                                          │
    │       @if(session('toast'))                                          │
    │         <x-feedback-status.toast                                     │
    │           :type="session('toast')['type']"                          │
    │           :message="session('toast')['message']" />                 │
    │       @endif                                                         │
    │                                                                      │
    │  2) LIVEWIRE EVENT — dispatched from any Livewire component.        │
    │     PHP:  $this->dispatch('lw-toast', type: 'success',              │
    │                           message: 'Saved successfully.');          │
    │     JS:   window.dispatchEvent(new CustomEvent('lw-toast',          │
    │             { detail: { type: 'success', message: '…' } }))        │
    │     The lw-toast Alpine listener below handles these automatically. │
    │     Place this component once in your layout — no extra Blade needed│
    └──────────────────────────────────────────────────────────────────────┘
--}}

{{-- ── 1. Session-flash toast ─────────────────────────────────────────────── --}}
@if ($message)
    <div
        x-cloak
        x-data="{ show: false }"
        x-init="$nextTick(() => { show = true; setTimeout(() => show = false, 5200); })"
        x-show="show"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-3"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 translate-x-3"
        class="fixed top-5 right-3 sm:right-5 z-9999
               w-[calc(100vw-1.5rem)] sm:w-96 max-w-sm
               rounded-xl border p-3 shadow-xl backdrop-blur-sm {{ $toast['container'] }}"
        role="status"
        aria-live="polite">

        <div class="flex items-start gap-3">
            <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white {{ $toast['iconBg'] }}">
                <i class="bx {{ $toast['icon'] }} text-lg leading-none"></i>
            </span>
            <div class="min-w-0 flex-1">
                <p class="font-semibold leading-5">{{ $toast['title'] }}</p>
                <p class="mt-1 text-sm leading-relaxed wrap-break-words">{{ $message }}</p>
            </div>
            <button
                @click="show = false"
                class="ml-1 inline-flex h-8 w-8 shrink-0 items-center justify-center
                       rounded-lg text-slate-500 hover:bg-black/5 hover:text-slate-700 transition"
                aria-label="Close notification">
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        </div>
    </div>
@endif

{{-- ── 2. Livewire / JS event-driven toast ───────────────────────────────── --}}
{{-- Place once in your layout. Listens for 'lw-toast' events globally. --}}
<div
    x-cloak
    x-data="{
        show:    false,
        type:    'info',
        message: '',
        _timer:  null,

        styles: {
            success: { container: 'bg-emerald-50 text-emerald-900 border-emerald-200', iconBg: 'bg-emerald-500', icon: 'bx-check',       title: 'Success'     },
            error:   { container: 'bg-rose-50 text-rose-900 border-rose-200',          iconBg: 'bg-rose-500',    icon: 'bx-x',           title: 'Error'       },
            warning: { container: 'bg-amber-50 text-amber-900 border-amber-200',       iconBg: 'bg-amber-500',   icon: 'bx-error',       title: 'Attention'   },
            info:    { container: 'bg-sky-50 text-sky-900 border-sky-200',             iconBg: 'bg-sky-500',     icon: 'bx-info-circle', title: 'Information' },
        },

        get style() { return this.styles[this.type] ?? this.styles.info; },

        open(t, msg) {
            this.type    = t ?? 'info';
            this.message = msg ?? '';
            this.show    = true;
            clearTimeout(this._timer);
            this._timer  = setTimeout(() => this.show = false, 5200);
        }
    }"
    @lw-toast.window="open($event.detail.type, $event.detail.message)"
    x-show="show"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-3"
    x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 translate-x-3"
    :class="style.container"
    class="fixed top-5 right-3 sm:right-5 z-9999
           w-[calc(100vw-1.5rem)] sm:w-96 max-w-sm
           rounded-xl border p-3 shadow-xl backdrop-blur-sm"
    role="status"
    aria-live="polite">

    <div class="flex items-start gap-3">
        <span :class="style.iconBg"
            class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white">
            <i :class="'bx ' + style.icon" class="text-lg leading-none"></i>
        </span>
        <div class="min-w-0 flex-1">
            <p class="font-semibold leading-5" x-text="style.title"></p>
            <p class="mt-1 text-sm leading-relaxed wrap-break-words" x-text="message"></p>
        </div>
        <button
            @click="show = false"
            class="ml-1 inline-flex h-8 w-8 shrink-0 items-center justify-center
                   rounded-lg text-slate-500 hover:bg-black/5 hover:text-slate-700 transition"
            aria-label="Close notification">
            <i class="bx bx-x text-xl leading-none"></i>
        </button>
    </div>
</div>
