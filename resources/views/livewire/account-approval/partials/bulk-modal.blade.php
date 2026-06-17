{{-- Bulk confirmation modal — uses Alpine x-show, styled to match approvalModal --}}
<div x-show="bulkModal" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background:rgba(15,23,42,.45);"
    @click.self="bulkModal = false"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-end="opacity-0 scale-95">

    <div class="w-full max-w-md bg-white rounded-xl overflow-hidden flex flex-col border-t-4 shrink-0"
        :class="{
            'border-emerald-500': bulkAction === 'approve',
            'border-rose-500':    bulkAction === 'reject',
            'border-amber-500':   bulkAction === 'disable',
            'border-blue-500':    bulkAction === 'restore',
        }"
        style="box-shadow: 0 8px 40px rgba(22,163,74,0.18);"
        @click.stop>

        {{-- Header --}}
        <header class="px-6 py-4 border-b border-[#e2e8f0] bg-white shrink-0">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0 text-white"
                        :class="{
                            'bg-emerald-600': bulkAction === 'approve',
                            'bg-rose-600':    bulkAction === 'reject',
                            'bg-amber-500':   bulkAction === 'disable',
                            'bg-blue-600':    bulkAction === 'restore',
                        }">
                        <i class="text-base leading-none"
                            :class="{
                                'bx bx-check-shield': bulkAction === 'approve',
                                'bx bx-block':        bulkAction === 'reject',
                                'bx bx-pause-circle': bulkAction === 'disable',
                                'bx bx-revision':     bulkAction === 'restore',
                            }"></i>
                    </span>
                    <p class="text-[15px] font-bold text-[#0f172a]">
                        Bulk <span x-text="bulkAction.charAt(0).toUpperCase() + bulkAction.slice(1)"></span>
                    </p>
                </div>
                <button @click="bulkModal = false" :disabled="executing" type="button"
                    class="shrink-0 rounded-lg p-1.5 text-[#94a3b8] hover:bg-[#f8fafc] hover:text-[#475569] transition-colors disabled:opacity-50">
                    <i class="bx bx-x text-xl leading-none"></i>
                </button>
            </div>
        </header>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

            <p class="text-[13px] text-[#475569]">
                You are about to
                <strong class="text-[#0f172a]" x-text="bulkAction"></strong>
                <strong class="text-[#0f172a]">
                    <span x-text="selected.length"></span> user<span x-show="selected.length !== 1">s</span>
                </strong>.
            </p>

            {{-- Per-action alert using x-feedback-status.alert style inline (Alpine-driven) --}}
            <div x-show="bulkAction === 'approve'"
                class="rounded-xl border border-[#bbf7d0] bg-[#f0fdf4] p-4 flex items-start gap-3">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#dcfce7] text-[#16a34a]">
                    <i class="bx bx-check-circle text-base leading-none"></i>
                </span>
                <p class="text-[13px] text-[#166534] leading-relaxed">Selected users will be activated and notified via email.</p>
            </div>
            <div x-show="bulkAction === 'reject'"
                class="rounded-xl border border-[#fda4af] bg-[#fff1f2] p-4 flex items-start gap-3">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#ffe4e6] text-[#f43f5e]">
                    <i class="bx bx-error-circle text-base leading-none"></i>
                </span>
                <p class="text-[13px] text-[#9f1239] leading-relaxed">Users will be rejected and all organizational assignments removed.</p>
            </div>
            <div x-show="bulkAction === 'disable'"
                class="rounded-xl border border-[#fcd34d] bg-[#fffbeb] p-4 flex items-start gap-3">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#fef3c7] text-[#f59e0b]">
                    <i class="bx bx-error text-base leading-none"></i>
                </span>
                <p class="text-[13px] text-[#92400e] leading-relaxed">Users will be disabled and all organizational assignments removed.</p>
            </div>
            <div x-show="bulkAction === 'restore'"
                class="rounded-xl border border-[#bfdbfe] bg-[#eff6ff] p-4 flex items-start gap-3">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#e2e8f0] text-[#475569]">
                    <i class="bx bx-info-circle text-base leading-none"></i>
                </span>
                <p class="text-[13px] text-[#1e40af] leading-relaxed">Accounts will be restored to pending status and await admin approval.</p>
            </div>

        </div>

        {{-- Footer --}}
        <footer class="border-t border-[#e2e8f0] bg-[#f8fafc] px-6 py-4 flex justify-end gap-3 shrink-0">
            <button @click="bulkModal = false" :disabled="executing" type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-[13px] font-semibold rounded-lg
                       border border-[#e2e8f0] bg-white text-[#475569]
                       hover:bg-[#f8fafc] hover:border-[#94a3b8] hover:text-[#0f172a]
                       transition-colors disabled:opacity-50 disabled:pointer-events-none">
                Cancel
            </button>

            <button @click="syncAndExecute()" :disabled="executing" type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg shadow-sm
                       transition-all duration-150 active:scale-95 text-white
                       disabled:opacity-60 disabled:pointer-events-none disabled:active:scale-100"
                :class="{
                    'bg-emerald-600 hover:bg-emerald-700': bulkAction === 'approve',
                    'bg-rose-600    hover:bg-rose-700':    bulkAction === 'reject',
                    'bg-amber-500   hover:bg-amber-600':   bulkAction === 'disable',
                    'bg-blue-600    hover:bg-blue-700':    bulkAction === 'restore',
                }">
                <template x-if="!executing">
                    <span class="inline-flex items-center gap-2 leading-none">
                        <i class="text-base leading-none"
                            :class="{
                                'bx bx-check':    bulkAction === 'approve',
                                'bx bx-x':        bulkAction === 'reject',
                                'bx bx-pause':    bulkAction === 'disable',
                                'bx bx-revision': bulkAction === 'restore',
                            }"></i>
                        Confirm <span x-text="bulkAction.charAt(0).toUpperCase() + bulkAction.slice(1)"></span>
                    </span>
                </template>
                <template x-if="executing">
                    <span class="inline-flex items-center gap-2 leading-none">
                        <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Applying…
                    </span>
                </template>
            </button>
        </footer>

    </div>
</div>
