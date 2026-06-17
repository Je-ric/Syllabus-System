{{--
    Reusable Alpine confirmation modal.
    Pass confirmNs to scope the event: @include(..., ['confirmNs' => 'peo'])
    Listens to: confirm-dialog:NS  (e.g. confirm-dialog:peo / confirm-dialog:po)
--}}
@php $ns = $confirmNs ?? 'default'; @endphp
<div
    x-data="{
        show: false,
        title: '',
        message: '',
        confirmLabel: 'Confirm',
        confirmClass: 'bg-rose-600 hover:bg-rose-700 text-white',
        _resolve: null,
        open(detail) {
            this.title        = detail.title        ?? 'Are you sure?';
            this.message      = detail.message      ?? '';
            this.confirmLabel = detail.confirmLabel ?? 'Confirm';
            this.confirmClass = detail.confirmClass ?? 'bg-rose-600 hover:bg-rose-700 text-white';
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
    style="background:rgba(15,23,42,.45);"
    @keydown.escape.window="cancel()"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-end="opacity-0 scale-95">

    <div class="w-full max-w-sm bg-white rounded-xl overflow-hidden flex flex-col border-t-4 border-rose-500"
        style="box-shadow: 0 8px 40px rgba(0,0,0,0.18);"
        @click.stop>

        {{-- Header --}}
        <header class="px-6 py-4 border-b border-[#e2e8f0] bg-white shrink-0">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0 bg-rose-600 text-white">
                        <i class="bx bx-error text-base leading-none"></i>
                    </span>
                    <p class="text-[15px] font-bold text-[#0f172a]" x-text="title"></p>
                </div>
                <button @click="cancel()" type="button"
                    class="shrink-0 rounded-lg p-1.5 text-[#94a3b8] hover:bg-[#f8fafc] hover:text-[#475569] transition-colors">
                    <i class="bx bx-x text-xl leading-none"></i>
                </button>
            </div>
        </header>

        {{-- Body --}}
        <div class="px-6 py-5">
            <div class="rounded-xl border border-[#fda4af] bg-[#fff1f2] p-4 flex items-start gap-3">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#ffe4e6] text-[#f43f5e]">
                    <i class="bx bx-error-circle text-base leading-none"></i>
                </span>
                <p class="text-[13px] text-[#9f1239] leading-relaxed" x-text="message"></p>
            </div>
        </div>

        {{-- Footer --}}
        <footer class="border-t border-[#e2e8f0] bg-[#f8fafc] px-6 py-4 flex justify-end gap-3 shrink-0">
            <button @click="cancel()" type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-[13px] font-semibold rounded-lg
                        border border-[#e2e8f0] bg-white text-[#475569]
                        hover:bg-[#f8fafc] hover:border-[#94a3b8] hover:text-[#0f172a] transition-colors">
                Cancel
            </button>
            <button @click="confirm()" type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg shadow-sm
                        transition-all duration-150 active:scale-95
                        disabled:opacity-60 disabled:pointer-events-none"
                :class="confirmClass">
                <span x-text="confirmLabel"></span>
            </button>
        </footer>

    </div>
</div>
