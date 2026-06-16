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
        confirm() {
            this.show = false;
            if (this._resolve) this._resolve(true);
        },
        cancel() {
            this.show = false;
            if (this._resolve) this._resolve(false);
        }
    }"
    @confirm-dialog:{{ $ns }}.window="open($event.detail)"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background:rgba(15,23,42,.45);"
    @keydown.escape.window="cancel()">

    <div class="w-full max-w-sm rounded-2xl bg-white shadow-2xl p-6 space-y-4" @click.stop>

        <div class="flex items-start gap-3">
            <span class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-full bg-rose-50 ring-1 ring-rose-200">
                <i class="bx bx-error text-rose-500 text-xl"></i>
            </span>
            <div>
                <p class="text-[14px] font-bold text-slate-800" x-text="title"></p>
                <p class="mt-1 text-[13px] text-slate-500" x-text="message"></p>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-1">
            <button @click="cancel()" type="button"
                class="px-4 py-2 rounded-lg text-[13px] font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                Cancel
            </button>
            <button @click="confirm()" type="button"
                class="px-4 py-2 rounded-lg text-[13px] font-semibold transition-colors"
                :class="confirmClass">
                <span x-text="confirmLabel"></span>
            </button>
        </div>
    </div>
</div>
