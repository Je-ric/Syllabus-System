{{-- Partial: review-page-partials/dean-approval.blade.php
     Only rendered when $reviewForm?->approved_by_dean_id is set. --}}
<x-layout.card-section title="Dean Approval" icon="bx-shield-check">
    <div class="flex items-center gap-2 rounded-lg
                bg-emerald-50 border border-emerald-200 px-3 py-2.5">
        <i class="bx bx-check-double text-emerald-600 text-base shrink-0"></i>
        <div>
            <p class="text-xs font-semibold text-emerald-800">Approved by Dean</p>
            <p class="text-[11px] text-emerald-600 mt-0.5">
                {{ $reviewForm->approvedByDean?->name }} ·
                {{ $reviewForm->approved_by_dean_at?->format('M d, Y') }}
            </p>
        </div>
    </div>
</x-layout.card-section>
