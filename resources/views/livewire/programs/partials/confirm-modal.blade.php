{{--
    Reusable Alpine confirmation modal.
    Pass confirmNs to scope the event: @include(..., ['confirmNs' => 'peo'])
    Listens to: confirm-dialog:NS  (e.g. confirm-dialog:peo / confirm-dialog:po)

    NOTE: Cannot be swapped for <x-modal.confirm> because the NS scoping
    (@confirm-dialog:{{ $ns }}) requires runtime Blade interpolation that the
    component system resolves at compile time, breaking multi-instance support.
    Visual tokens are kept in sync with x-modal.confirm manually.

    Variations: pass confirmVariant (danger|warning|info|default) to include
--}}
@php
    $ns = $confirmNs ?? 'default';
    $confirmVariant = $confirmVariant ?? 'danger';
    $vMap = match($confirmVariant) {
        'warning' => [
            'icon'       => 'bx-error',
            'chipBg'     => '#B37100', 'chipRing' => '#FFE9B5', 'chipFg' => '#FFF6E2',
            'msgBg'      => '#FFFDF6', 'msgBorder' => '#FFE9B5', 'msgIconBg' => '#FFF6E2',
            'msgIconFg'  => '#B37100', 'msgText' => '#875200',
            'accent'     => 'linear-gradient(90deg,#F5B126 0%,#FFC646 55%,rgba(245,177,38,0) 100%)',
            'shadow'     => 'box-shadow:0 1px 2px rgba(16,24,40,0.04),0 4px 16px rgba(245,177,38,0.12),0 12px 40px rgba(245,177,38,0.10);',
            'confirmBtn' => 'bg-[#F5B126] hover:bg-[#D79400] text-white',
        ],
        'info' => [
            'icon'       => 'bx-info-circle',
            'chipBg'     => '#194C6E', 'chipRing' => '#AEDFFF', 'chipFg' => '#DAF1FF',
            'msgBg'      => '#F7FCFE', 'msgBorder' => '#AEDFFF', 'msgIconBg' => '#DAF1FF',
            'msgIconFg'  => '#194C6E', 'msgText' => '#143D57',
            'accent'     => 'linear-gradient(90deg,#3197D6 0%,#71BFF1 55%,rgba(49,151,214,0) 100%)',
            'shadow'     => 'box-shadow:0 1px 2px rgba(16,24,40,0.04),0 4px 16px rgba(49,151,214,0.12),0 12px 40px rgba(49,151,214,0.10);',
            'confirmBtn' => 'bg-[#3197D6] hover:bg-[#1F5E89] text-white',
        ],
        'default' => [
            'icon'       => 'bx-check-circle',
            'chipBg'     => '#06754E', 'chipRing' => '#AEFFE2', 'chipFg' => '#EDFFF8',
            'msgBg'      => '#EDFFF8', 'msgBorder' => '#AEFFE2', 'msgIconBg' => '#D5FFF0',
            'msgIconFg'  => '#06754E', 'msgText' => '#003724',
            'accent'     => 'linear-gradient(90deg,#00D88B 0%,#00C075 50%,rgba(0,216,139,0) 100%)',
            'shadow'     => 'box-shadow:0 1px 2px rgba(16,24,40,0.04),0 4px 16px rgba(0,216,139,0.10),0 12px 40px rgba(0,150,95,0.10);',
            'confirmBtn' => 'bg-[#00D88B] hover:bg-[#00C075] text-white',
        ],
        default => [
            'icon'       => 'bx-error',
            'chipBg'     => '#D21B14', 'chipRing' => '#FFA2A2', 'chipFg' => '#FFE3E2',
            'msgBg'      => '#FEF7F6', 'msgBorder' => '#FFA2A2', 'msgIconBg' => '#FFE3E2',
            'msgIconFg'  => '#D21B14', 'msgText' => '#731814',
            'accent'     => 'linear-gradient(90deg,#E52F28 0%,#F45855 55%,rgba(229,47,40,0) 100%)',
            'shadow'     => 'box-shadow:0 1px 2px rgba(16,24,40,0.04),0 4px 16px rgba(229,47,40,0.12),0 12px 40px rgba(229,47,40,0.10);',
            'confirmBtn' => 'bg-[#D21B14] hover:bg-[#E52F28] text-white',
        ],
    };
@endphp
<div
    x-data="{
        show: false,
        title: '',
        message: '',
        confirmLabel: 'Confirm',
        confirmClass: '{{ $vMap['confirmBtn'] }}',
        _resolve: null,
        open(detail) {
            this.title        = detail.title        ?? 'Are you sure?';
            this.message      = detail.message      ?? '';
            this.confirmLabel = detail.confirmLabel ?? 'Confirm';
            this.confirmClass = detail.confirmClass ?? '{{ $vMap['confirmBtn'] }}';
            this._resolve     = detail._resolve     ?? null;
            this.show = true;
        },
        confirm() { this.show = false; if (this._resolve) this._resolve(true); },
        cancel()  { this.show = false; if (this._resolve) this._resolve(false); }
    }"
    @confirm-dialog:{{ $ns }}.window="open($event.detail)"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background:rgba(9,9,11,0.45); backdrop-filter:blur(3px);"
    @keydown.escape.window="cancel()"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-end="opacity-0">

    <div class="w-full max-w-sm bg-white rounded-2xl overflow-hidden flex flex-col
                border border-[#E3E8EB] relative"
        style="{{ $vMap['shadow'] }}"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop>

        <div class="absolute inset-x-0 top-0 h-[2.5px] rounded-t-2xl pointer-events-none z-10"
             style="background:{{ $vMap['accent'] }};"
             aria-hidden="true"></div>

        <header class="px-5 pt-5 pb-4 bg-white shrink-0">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-9 h-9 rounded-[10px] shrink-0 border"
                          style="background:{{ $vMap['chipBg'] }}; border-color:{{ $vMap['chipRing'] }};">
                        <i class="bx {{ $vMap['icon'] }} text-[15px] leading-none" style="color:{{ $vMap['chipFg'] }};"></i>
                    </span>
                    <p class="text-[14.5px] font-bold text-[#253540]" x-text="title"></p>
                </div>
                <button @click="cancel()" type="button"
                    class="shrink-0 rounded-[8px] p-1.5
                           text-[#A5B2BD] hover:text-[#394056] hover:bg-[#F1F3F5]
                           transition-colors duration-150"
                    aria-label="Cancel">
                    <i class="bx bx-x text-xl leading-none"></i>
                </button>
            </div>
        </header>

        <div class="px-5 pb-5">
            <div class="rounded-[10px] border p-3.5 flex items-start gap-3"
                 style="border-color:{{ $vMap['msgBorder'] }}; background:{{ $vMap['msgBg'] }};">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-[8px]"
                      style="background:{{ $vMap['msgIconBg'] }};">
                    <i class="bx bx-error-circle text-[13px] leading-none" style="color:{{ $vMap['msgIconFg'] }};"></i>
                </span>
                <p class="text-[12.5px] leading-relaxed" style="color:{{ $vMap['msgText'] }};" x-text="message"></p>
            </div>
        </div>

        <footer class="border-t border-[#F1F3F5] bg-[#F9FAFA] px-5 py-3.5 flex justify-end gap-2.5 shrink-0">
            <button @click="cancel()" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 text-[13px] font-semibold rounded-[8px]
                       border border-[#E3E8EB] bg-white text-[#394056]
                       hover:bg-[#F1F3F5] hover:border-[#D6DDE3] hover:text-[#253540]
                       transition-colors duration-150">
                Cancel
            </button>
            <button @click="confirm()" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 text-[13px] font-semibold rounded-[8px]
                       shadow-[0_1px_2px_rgba(16,24,40,0.05)]
                       transition-all duration-150 active:scale-95
                       disabled:opacity-60 disabled:pointer-events-none"
                :class="confirmClass">
                <span x-text="confirmLabel"></span>
            </button>
        </footer>

    </div>
</div>
