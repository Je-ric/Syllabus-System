{{--
    flash-message.blade.php
    Fixed centered toast shown while saving or deleting.
    Relies on: isSaving, deletingId from the parent x-data scope.
--}}
<template x-if="deletingId || isSaving">
    <div class="fixed inset-x-0 top-10 z-[9999] flex justify-center pointer-events-none">
        <div class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl shadow-2xl border text-[13px] font-semibold pointer-events-auto"
            :class="deletingId
                ? 'border-rose-200 bg-rose-50 text-rose-700 shadow-rose-100'
                : 'border-blue-200 bg-blue-50 text-blue-700 shadow-blue-100'">

            {{-- Spinner --}}
            <svg class="animate-spin h-4 w-4 shrink-0"
                :class="deletingId ? 'text-rose-500' : 'text-blue-500'"
                viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>

            {{-- Label --}}
            <span x-text="deletingId ? 'Deleting…' : 'Saving…'"></span>

            {{-- Divider --}}
            <span class="opacity-30 select-none">|</span>

            {{-- Caution --}}
            <span class="text-[11px] font-medium opacity-70">Do not close or navigate away.</span>
        </div>
    </div>
</template>
