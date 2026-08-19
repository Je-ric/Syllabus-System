{{--
    x-modal.confirm
    ────────────────────────────────────────────────────────────────────────
    Shared Alpine confirmation dialog. Scoped by `ns` to allow multiple
    instances on the same page without event collisions.

    Usage (Blade include):
        <x-modal.confirm ns="co" />
        <x-modal.confirm ns="week" />

    Trigger from Alpine JS:
        _confirm({ title, message, confirmLabel, confirmClass })
        → returns Promise<bool>

        _confirm(detail) {
            return new Promise(resolve => {
                window.dispatchEvent(new CustomEvent('confirm-dialog:co', {
                    detail: { ...detail, _resolve: resolve }
                }));
            });
        }
    ────────────────────────────────────────────────────────────────────────
    Design.md compliance:
    • Radius      : rounded-2xl (--radius-lg 16px)
    • Accent rail : Red 500→600 gradient (for default danger confirm)
    • Shadows     : Red-tinted multi-layer elevation
    • Icon chip   : Red 600 bg (#D21B14) / Red 300 ring (#FFA2A2) / Red 200 fg (#FFE3E2)
    • Message box : Red 100 bg (#FEF7F6) / Red 300 border (#FFA2A2)
    • Cancel btn  : --border-default + Charcoal 500 text
    • Confirm btn : dynamic via :class from caller
──────────────────────────────────────────────────────────────────────────--}}
@props(['ns' => 'default'])

<div
    x-data="{
        show: false,
        title: '',
        message: '',
        confirmLabel: 'Confirm',
        confirmClass: 'bg-[#D21B14] hover:bg-[#E52F28] text-white',
        _resolve: null,
        open(detail) {
            this.title        = detail.title        ?? 'Are you sure?';
            this.message      = detail.message      ?? '';
            this.confirmLabel = detail.confirmLabel ?? 'Confirm';
            this.confirmClass = detail.confirmClass ?? 'bg-[#D21B14] hover:bg-[#E52F28] text-white';
            this._resolve     = detail._resolve     ?? null;
            this.show = true;
        },
        confirm() { this.show = false; if (this._resolve) this._resolve(true); },
        cancel()  { this.show = false; if (this._resolve) this._resolve(false); }
    }"
    @confirm-dialog:{{ $ns }}.window="open($event.detail)"
    @click="cancel()"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[60] flex items-center justify-center p-4"
    style="background:rgba(9,9,11,0.45); backdrop-filter:blur(3px);"
    @keydown.escape.window="cancel()"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-end="opacity-0">

    {{-- Dialog card --}}
    <div class="w-full max-w-sm bg-white rounded-2xl overflow-hidden flex flex-col
                border border-[#E3E8EB] relative"
        style="box-shadow:0 1px 2px rgba(16,24,40,0.04),0 4px 16px rgba(229,47,40,0.12),0 12px 40px rgba(229,47,40,0.10);"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop>

        {{-- Gradient accent rail — Red 500 → 600, fades right --}}
        <div class="absolute inset-x-0 top-0 h-[2.5px] rounded-t-2xl pointer-events-none z-10"
             style="background:linear-gradient(90deg,#E52F28 0%,#F45855 55%,rgba(229,47,40,0) 100%);"
             aria-hidden="true"></div>

        {{-- Header --}}
        <header class="px-5 pt-5 pb-4 bg-white shrink-0">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    {{-- Icon chip: Red 600 bg / Red 300 ring / Red 200 icon --}}
                    <span class="flex items-center justify-center w-9 h-9 rounded-[10px] shrink-0 border"
                          style="background:#D21B14; border-color:#FFA2A2;">
                        <i class="bx bx-error text-[15px] leading-none" style="color:#FFE3E2;"></i>
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

        {{-- Body — Red 100 bg (#FEF7F6) / Red 300 border (#FFA2A2) --}}
        <div class="px-5 pb-5">
            <div class="rounded-[10px] border border-[#FFA2A2] bg-[#FEF7F6] p-3.5 flex items-start gap-3">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-[8px]"
                      style="background:#FFE3E2;">
                    <i class="bx bx-error-circle text-[13px] leading-none" style="color:#D21B14;"></i>
                </span>
                <p class="text-[12.5px] text-[#731814] leading-relaxed" x-text="message"></p>
            </div>
        </div>

        {{-- Footer --}}
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
                       transition-all duration-150 active:scale-95 disabled:opacity-60 disabled:pointer-events-none"
                :class="confirmClass">
                <span x-text="confirmLabel"></span>
            </button>
        </footer>

    </div>
</div>
