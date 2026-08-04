{{-- Partial: review-page-partials/reviewer-status.blade.php --}}

{{-- ── My assignment status ──────────────────────────────────────────────── --}}
<x-layout.card-section title="My Status" icon="bx-user-check">

    <div class="flex items-center justify-between gap-2">
        <div>
            <p class="text-xs text-[#93A1AF]">Role</p>
            <x-feedback-status.status-indicator :variant="$isChair ? 'violet' : 'slate'" class="mt-0.5">
                {{ $isChair ? 'CQI Chair' : 'CQI Member' }}
            </x-feedback-status.status-indicator>
        </div>
        <div>
            <p class="text-xs text-[#93A1AF]">Checklist</p>
            <x-feedback-status.status-indicator
                :variant="($assignment->status ?? '') === 'approved' ? 'emerald' : 'amber'"
                class="mt-0.5">
                {{ ($assignment->status ?? '') === 'approved' ? 'Completed' : 'Pending' }}
            </x-feedback-status.status-indicator>
        </div>
    </div>

</x-layout.card-section>

{{-- ── Other reviewers ───────────────────────────────────────────────────── --}}
@if (count($otherReviewers) > 0)
    <x-layout.card-section title="Other Reviewers" icon="bx-group" :count="count($otherReviewers)">

        <div class="space-y-2">
            @foreach ($otherReviewers as $r)
                <div class="flex items-center justify-between gap-2 py-1">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                     bg-[#F1F3F5] text-[#72809E] text-xs font-bold shrink-0">
                            {{ strtoupper(substr($r['name'], 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-[13px] font-medium text-[#394056] truncate">{{ $r['name'] }}</p>
                            <p class="text-[11px] text-[#93A1AF]">{{ ucfirst($r['role']) }}</p>
                        </div>
                    </div>
                    <x-feedback-status.status-indicator
                        :variant="$r['status'] === 'approved' ? 'emerald' : 'amber'">
                        {{ $r['status'] === 'approved' ? 'Completed' : 'Pending' }}
                    </x-feedback-status.status-indicator>
                </div>
            @endforeach
        </div>

    </x-layout.card-section>
@endif
