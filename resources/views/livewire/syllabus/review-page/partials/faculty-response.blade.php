{{-- Partial: review-page-partials/faculty-response.blade.php
     Part H — Faculty response to required corrections/revisions (committee read + verify) --}}
<x-layout.card-section title="Part H — Faculty Response" icon="bx-reply">

    @if ($reviewForm->part_h_faculty_response)

        <div class="rounded-lg border border-[#E3E8EB] bg-[#FAFDFB] px-3 py-2.5 mb-3">
            <div class="flex items-center justify-between mb-1">
                <p class="text-[11px] font-bold text-[#72809E] uppercase tracking-widest">Faculty's Response</p>
                @if ($reviewForm->part_h_faculty_response_updated_at)
                    <p class="text-[10px] text-slate-500">
                        Submitted {{ $reviewForm->part_h_faculty_response_updated_at->format('M d, Y') }}
                    </p>
                @endif
            </div>
            <p class="text-sm text-[#394056] whitespace-pre-wrap leading-relaxed">{{ $reviewForm->part_h_faculty_response }}</p>
        </div>

        @if ($reviewForm->decision === 'approved_with_corrections')
            @if ($reviewForm->part_h_verified_at)
                <div class="flex items-center gap-2 rounded-lg bg-emerald-50 border border-emerald-200 px-3 py-2.5">
                    <i class="bx bx-check-circle text-emerald-600 text-base shrink-0"></i>
                    <div>
                        <p class="text-xs font-semibold text-emerald-800">Verified</p>
                        <p class="text-[11px] text-emerald-600 mt-0.5">
                            {{ $reviewForm->partHVerifier?->name ?? '—' }} ·
                            {{ $reviewForm->part_h_verified_at?->format('M d, Y') }}
                        </p>
                    </div>
                </div>
            @else
                @if ($isChair)
                    <div class="mb-3">
                        <p class="text-xs text-slate-500 mb-2">Review the faculty's response and verify if corrections adequately address the required actions.</p>
                        <x-ui.button
                            type="button"
                            variant="save"
                            wire:click="verifyPartH"
                            wire:loading.attr="disabled"
                            wire:target="verifyPartH"
                            loading="Verifying…"
                            class="w-full justify-center">
                            <i class="bx bx-check-circle text-sm leading-none"></i>
                            Verify Response
                        </x-ui.button>
                    </div>
                @else
                    <div class="flex items-center gap-2 rounded-lg bg-amber-50 border border-amber-200 px-3 py-2.5">
                        <i class="bx bx-time text-amber-500 text-base shrink-0"></i>
                        <p class="text-xs text-amber-800">Pending verification by chair.</p>
                    </div>
                @endif
            @endif
        @elseif ($reviewForm->decision === 'returned_for_revision')
            <div class="flex items-center gap-2 rounded-lg bg-blue-50 border border-blue-200 px-3 py-2.5">
                <i class="bx bx-info-circle text-blue-600 text-base shrink-0"></i>
                <p class="text-xs text-blue-800">Faculty can resubmit this syllabus for review after completing revisions.</p>
            </div>
        @endif

    @else

        <div class="flex items-center gap-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2.5">
            <i class="bx bx-time text-slate-400 text-base shrink-0"></i>
            <p class="text-xs text-slate-600">Faculty has not yet submitted their response.</p>
        </div>

    @endif

</x-layout.card-section>
