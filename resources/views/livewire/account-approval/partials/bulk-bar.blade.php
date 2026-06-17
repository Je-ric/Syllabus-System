{{-- Conflict toast --}}
<div x-show="conflictToast" x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-end="opacity-0"
     class="fixed bottom-5 left-1/2 -translate-x-1/2 z-60 flex items-center gap-2.5
            bg-[#1e293b] text-white text-[13px] font-medium px-4 py-2.5 rounded-xl shadow-xl pointer-events-none">
    <i class="bx bx-error-circle text-amber-400 text-base shrink-0"></i>
    <span x-text="conflictMsg"></span>
</div>

{{-- Count + bulk action bar --}}
<div class="flex items-center justify-between gap-3 flex-wrap min-h-8">
    <p class="text-[13px] text-[#475569]">
        <span class="font-semibold text-[#0f172a]">{{ $users->total() }}</span>
        user{{ $users->total() !== 1 ? 's' : '' }} found
        <template x-if="selected.length">
            <span>
                &mdash;
                <span class="font-semibold text-[#0f172a]" x-text="selected.length"></span> selected
                <span class="inline-flex items-center gap-1 ml-1 text-[11px] font-semibold px-2 py-0.5 rounded-full ring-1"
                    :class="{
                        'bg-[#fef3c7] text-[#92400e] ring-[#fcd34d]': selectedStatus === 'pending',
                        'bg-[#dcfce7] text-[#166534] ring-[#bbf7d0]': selectedStatus === 'active',
                        'bg-[#f1f5f9] text-[#475569] ring-[#e2e8f0]': selectedStatus === 'disabled',
                        'bg-[#ffe4e6] text-[#9f1239] ring-[#fda4af]': selectedStatus === 'rejected',
                    }">
                    <span x-text="selectedStatus"></span>
                </span>
            </span>
        </template>
    </p>

    <div x-show="selected.length" class="flex items-center gap-2 flex-wrap">
        <span class="text-[12px] text-[#475569] font-medium">Bulk:</span>
        <template x-for="act in bulkActions" :key="act.key">
            <button
                @click="openBulk(act.key)"
                :disabled="executing"
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg
                       transition-colors duration-150 text-white disabled:opacity-60 disabled:cursor-not-allowed"
                :class="{
                    'bg-emerald-600 hover:bg-emerald-700': act.variant === 'confirm',
                    'bg-rose-600   hover:bg-rose-700':    act.variant === 'danger',
                    'bg-slate-500  hover:bg-slate-600':   act.variant === 'disable',
                    'bg-indigo-600 hover:bg-indigo-700':  act.variant === 'restore',
                }">
                <i class="bx leading-none" :class="act.icon"></i>
                <span x-text="act.label"></span>
            </button>
        </template>
    </div>
</div>
