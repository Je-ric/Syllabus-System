{{-- Bulk confirmation modal --}}
<div x-show="bulkModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    @click.self="bulkModal = false" x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0">

    <div class="bg-white rounded-xl w-full max-w-md mx-4 overflow-hidden"
        :class="{
            'border-t-4 border-emerald-500': bulkAction === 'approve',
            'border-t-4 border-rose-500': bulkAction === 'reject',
            'border-t-4 border-amber-500': bulkAction === 'disable',
            'border-t-4 border-blue-500': bulkAction === 'restore',
        }"
        style="box-shadow: 0 8px 40px rgba(0,0,0,0.18);" @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#e2e8f0]">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0 text-white"
                    :class="{
                        'bg-emerald-600': bulkAction === 'approve',
                        'bg-rose-600': bulkAction === 'reject',
                        'bg-amber-500': bulkAction === 'disable',
                        'bg-blue-600': bulkAction === 'restore',
                    }">
                    <i class="text-base leading-none"
                        :class="{
                            'bx bx-check-shield': bulkAction === 'approve',
                            'bx bx-block': bulkAction === 'reject',
                            'bx bx-pause-circle': bulkAction === 'disable',
                            'bx bx-revision': bulkAction === 'restore',
                        }"></i>
                </span>
                <p class="text-[15px] font-bold text-[#0f172a]">
                    Bulk <span x-text="bulkAction.charAt(0).toUpperCase() + bulkAction.slice(1)"></span>
                </p>
            </div>
            <button @click="bulkModal = false" :disabled="executing"
                class="rounded-lg p-1.5 text-[#94a3b8] hover:bg-[#f8fafc] hover:text-[#475569] transition disabled:opacity-50">
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 space-y-3">
            <p class="text-[14px] text-[#475569]">
                You are about to <strong class="text-[#0f172a]" x-text="bulkAction"></strong>
                <strong class="text-[#0f172a]">
                    <span x-text="selected.length"></span> user<span x-show="selected.length !== 1">s</span>
                </strong>.
            </p>
            <div x-show="bulkAction === 'approve'"
                class="rounded-xl border border-[#bbf7d0] bg-[#f0fdf4] p-3 text-[13px] text-[#166534] flex items-center gap-2">
                <i class="bx bx-check-circle text-base shrink-0"></i> Selected users will be activated and notified via
                email.
            </div>
            <div x-show="bulkAction === 'reject'"
                class="rounded-xl border border-[#fda4af] bg-[#fff1f2] p-3 text-[13px] text-[#9f1239] flex items-center gap-2">
                <i class="bx bx-error-circle text-base shrink-0"></i> Users will be rejected and all assignments
                removed.
            </div>
            <div x-show="bulkAction === 'disable'"
                class="rounded-xl border border-[#fcd34d] bg-[#fffbeb] p-3 text-[13px] text-[#92400e] flex items-center gap-2">
                <i class="bx bx-error text-base shrink-0"></i> Users will be disabled and all assignments removed.
            </div>
            <div x-show="bulkAction === 'restore'"
                class="rounded-xl border border-[#bfdbfe] bg-[#eff6ff] p-3 text-[13px] text-[#1e40af] flex items-center gap-2">
                <i class="bx bx-info-circle text-base shrink-0"></i> Accounts will be restored to pending status.
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-3 px-6 py-4 bg-[#f8fafc] border-t border-[#e2e8f0]">
            <button @click="bulkModal = false" :disabled="executing"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg
                        bg-white text-[#475569] border border-[#e2e8f0] hover:bg-[#f8fafc]
                        disabled:opacity-50 disabled:cursor-not-allowed transition">
                Cancel
            </button>

            <button @click="syncAndExecute()" :disabled="executing"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg
                        shadow-sm transition-all duration-150 active:scale-95 text-white
                        disabled:opacity-60 disabled:cursor-not-allowed disabled:active:scale-100"
                :class="{
                    'bg-emerald-600 hover:bg-emerald-700': bulkAction === 'approve',
                    'bg-rose-600 hover:bg-rose-700': bulkAction === 'reject',
                    'bg-amber-500 hover:bg-amber-600': bulkAction === 'disable',
                    'bg-blue-600 hover:bg-blue-700': bulkAction === 'restore',
                }">

                {{-- Idle state --}}
                <template x-if="!executing">
                    <span class="inline-flex items-center gap-2">
                        <i class="text-base leading-none"
                            :class="{
                                'bx bx-check': bulkAction === 'approve',
                                'bx bx-x': bulkAction === 'reject',
                                'bx bx-pause': bulkAction === 'disable',
                                'bx bx-revision': bulkAction === 'restore',
                            }"></i>
                        Confirm <span x-text="bulkAction.charAt(0).toUpperCase() + bulkAction.slice(1)"></span>
                    </span>
                </template>

                {{-- Loading state --}}
                <template x-if="executing">
                    <span class="inline-flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        Applying…
                    </span>
                </template>
            </button>
        </div>
    </div>
</div>
