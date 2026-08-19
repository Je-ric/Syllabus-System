@props([
    'type'    => 'info',
    'message' => null,
])

@php
    $toastStyles = [
        'success' => ['container' => 'bg-[#F4FFFA] text-[#06754E] border-[#00965F]', 'iconBg' => 'bg-[#06754E]', 'iconFg' => 'text-[#EDFFF8]', 'bar' => 'bg-[#00C075]', 'icon' => 'bx-check',       'title' => 'Success'],
        'error'   => ['container' => 'bg-[#FFF8F8] text-[#731814] border-[#E52F28]', 'iconBg' => 'bg-[#D21B14]', 'iconFg' => 'text-[#FFE3E2]', 'bar' => 'bg-[#E52F28]', 'icon' => 'bx-x',           'title' => 'Error'],
        'warning' => ['container' => 'bg-[#FFFCF5] text-[#875200] border-[#F5B126]', 'iconBg' => 'bg-[#B37100]', 'iconFg' => 'text-[#FFF6E2]', 'bar' => 'bg-[#F5B126]', 'icon' => 'bx-error',       'title' => 'Attention'],
        'info'    => ['container' => 'bg-[#F5FBFF] text-[#143D57] border-[#3197D6]', 'iconBg' => 'bg-[#194C6E]', 'iconFg' => 'text-[#DAF1FF]', 'bar' => 'bg-[#3197D6]', 'icon' => 'bx-info-circle', 'title' => 'Information'],
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

<style>
    @keyframes toast-shrink {
        from { width: 100%; }
        to   { width: 0%; }
    }
    .toast-progress-bar {
        animation: toast-shrink linear forwards;
        animation-duration: var(--toast-duration, 5200ms);
    }
    .toast-progress-bar.is-paused {
        animation-play-state: paused;
    }
</style>

{{-- ── 1. Session-flash toast ─────────────────────────────────────────────── --}}
@if ($message)
    <div
        x-cloak
        x-data="{
            show: false,
            paused: false,
            _timer: null,
            _remaining: 5200,
            _startedAt: null,
            start() {
                this._startedAt = Date.now();
                this._timer = setTimeout(() => this.show = false, this._remaining);
            },
            pause() {
                this.paused = true;
                clearTimeout(this._timer);
                this._remaining -= (Date.now() - this._startedAt);
            },
            resume() {
                this.paused = false;
                this.start();
            }
        }"
        x-init="$nextTick(() => { show = true; start(); })"
        x-show="show"
        @mouseenter="pause()"
        @mouseleave="resume()"
        x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
        x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 translate-x-3 scale-95"
        class="fixed top-5 right-3 sm:right-5 z-9999
               w-[calc(100vw-1.5rem)] sm:w-96 max-w-sm
               rounded-[12px] border overflow-hidden shadow-xl backdrop-blur-sm {{ $toast['container'] }}"
        role="status"
        aria-live="polite">

        <div class="flex items-start gap-3 p-3.5">
            <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[9px] {{ $toast['iconBg'] }} {{ $toast['iconFg'] }}">
                <i class="bx {{ $toast['icon'] }} text-lg leading-none"></i>
            </span>
            <div class="min-w-0 flex-1">
                <p class="font-semibold leading-5">{{ $toast['title'] }}</p>
                <p class="mt-1 text-[13px] leading-relaxed opacity-90 wrap-break-words">{{ $message }}</p>
            </div>
            <button
                @click="show = false"
                class="ml-1 inline-flex h-8 w-8 shrink-0 items-center justify-center
                       rounded-[8px] opacity-60 hover:opacity-100 hover:bg-black/5 transition-colors duration-150"
                aria-label="Close notification">
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        </div>

        {{-- Countdown progress bar --}}
        <div class="h-[3px] w-full bg-black/5">
            <div class="toast-progress-bar h-full {{ $toast['bar'] }}"
                 :class="{ 'is-paused': paused }"
                 style="--toast-duration: 5200ms;"></div>
        </div>
    </div>
@endif

{{-- ── 2. Livewire / JS event-driven toast ───────────────────────────────── --}}
{{-- Place once in your layout. Listens for 'lw-toast' events globally. --}}
<div
    x-cloak
    x-data="{
        show:    false,
        paused:  false,
        type:    'info',
        message: '',
        _timer:  null,
        _remaining: 5200,
        _startedAt: null,

        styles: {
            success: { container: 'bg-[#F4FFFA] text-[#06754E] border-[#00965F]', iconBg: 'bg-[#06754E]', iconFg: 'text-[#EDFFF8]', bar: 'bg-[#00C075]', icon: 'bx-check',       title: 'Success'     },
            error:   { container: 'bg-[#FFF8F8] text-[#731814] border-[#E52F28]', iconBg: 'bg-[#D21B14]', iconFg: 'text-[#FFE3E2]', bar: 'bg-[#E52F28]', icon: 'bx-x',           title: 'Error'       },
            warning: { container: 'bg-[#FFFCF5] text-[#875200] border-[#F5B126]', iconBg: 'bg-[#B37100]', iconFg: 'text-[#FFF6E2]', bar: 'bg-[#F5B126]', icon: 'bx-error',       title: 'Attention'   },
            info:    { container: 'bg-[#F5FBFF] text-[#143D57] border-[#3197D6]', iconBg: 'bg-[#194C6E]', iconFg: 'text-[#DAF1FF]', bar: 'bg-[#3197D6]', icon: 'bx-info-circle', title: 'Information' },
        },

        get style() { return this.styles[this.type] ?? this.styles.info; },

        open(t, msg) {
            this.type       = t ?? 'info';
            this.message    = msg ?? '';
            this.show       = true;
            this._remaining = 5200;
            this.paused     = false;
            this.start();
        },
        start() {
            this._startedAt = Date.now();
            clearTimeout(this._timer);
            this._timer = setTimeout(() => this.show = false, this._remaining);
        },
        pause() {
            this.paused = true;
            clearTimeout(this._timer);
            this._remaining -= (Date.now() - this._startedAt);
        },
        resume() {
            this.paused = false;
            this.start();
        }
    }"
    @lw-toast.window="open($event.detail.type, $event.detail.message)"
    @mouseenter="pause()"
    @mouseleave="resume()"
    x-show="show"
    x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
    x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-4 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 translate-x-3 scale-95"
    :class="style.container"
    class="fixed top-5 right-3 sm:right-5 z-9999
           w-[calc(100vw-1.5rem)] sm:w-96 max-w-sm
           rounded-[12px] border overflow-hidden shadow-xl backdrop-blur-sm"
    role="status"
    aria-live="polite">

    <div class="flex items-start gap-3 p-3.5">
        <span :class="[style.iconBg, style.iconFg]"
            class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[9px]">
            <i :class="'bx ' + style.icon" class="text-lg leading-none"></i>
        </span>
        <div class="min-w-0 flex-1">
            <p class="font-semibold leading-5" x-text="style.title"></p>
            <p class="mt-1 text-[13px] leading-relaxed opacity-90 wrap-break-words" x-text="message"></p>
        </div>
        <button
            @click="show = false"
            class="ml-1 inline-flex h-8 w-8 shrink-0 items-center justify-center
                   rounded-[8px] opacity-60 hover:opacity-100 hover:bg-black/5 transition-colors duration-150"
            aria-label="Close notification">
            <i class="bx bx-x text-xl leading-none"></i>
        </button>
    </div>

    {{-- Countdown progress bar --}}
    <div class="h-[3px] w-full bg-black/5">
        <div class="toast-progress-bar h-full"
             :class="[style.bar, { 'is-paused': paused }]"
             style="--toast-duration: 5200ms;"></div>
    </div>
</div>