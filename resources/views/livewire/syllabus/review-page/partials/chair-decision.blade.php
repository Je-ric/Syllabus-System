{{-- Partial: review-page-partials/chair-decision.blade.php
     Only rendered when $isChair is true. --}}
<x-layout.card-section title="Committee Decision" icon="bx-gavel" :collapsible="true">

    @if ($reviewForm?->decision)
        @php
            $dLabel = match($reviewForm->decision) {
                'approved_as_updating'      => 'Approved as Updating',
                'approved_as_revision'      => 'Approved as Revision',
                'approved_with_corrections' => 'Approved with Corrections',
                'returned_for_revision'     => 'Returned for Revision',
                'reclassified_as_revision'  => 'Reclassified as Revision',
                default                     => ucfirst(str_replace('_', ' ', $reviewForm->decision)),
            };
            $dVariant = match($reviewForm->decision) {
                'approved_as_updating',
                'approved_as_revision'      => 'emerald',
                'approved_with_corrections' => 'amber',
                'returned_for_revision'     => 'rose',
                'reclassified_as_revision'  => 'blue',
                default                     => 'slate',
            };
        @endphp

        <div class="flex items-center gap-2 mb-3">
            <x-feedback-status.status-indicator :variant="$dVariant">
                {{ $dLabel }}
            </x-feedback-status.status-indicator>
            <span class="text-[11px] text-[#93A1AF]">
                {{ $reviewForm->decision_made_at?->format('M d, Y') }}
            </span>
        </div>

        @if ($reviewForm->decision === 'returned_for_revision' && $reviewForm->required_actions)
            <div class="rounded-lg bg-amber-50 border border-amber-200 px-3 py-2.5 mb-3">
                <p class="text-[11px] font-bold text-amber-700 mb-1">Required Actions:</p>
                <p class="text-xs text-amber-900 whitespace-pre-wrap leading-relaxed">{{ $reviewForm->required_actions }}</p>
                @if ($reviewForm->target_compliance_date)
                    <p class="text-[11px] text-amber-700 font-semibold mt-1.5">
                        Deadline: {{ $reviewForm->target_compliance_date->format('M d, Y') }}
                    </p>
                @endif
            </div>
        @endif

        @if (! $reviewForm->recommended_by_chair_id)
            <p class="text-[11px] text-[#93A1AF] mb-2">You can update the decision below.</p>
        @endif
    @endif

    @if (! $reviewForm?->recommended_by_chair_id)
        <div
            x-data="{
                originalRequiredActions: @js($reviewForm?->required_actions ?? ''),
                originalTargetDate: @js($reviewForm?->target_compliance_date?->format('Y-m-d') ?? ''),
                originalDecision: @js($reviewForm?->decision ?? ''),
                needsActions: $wire.decision === 'returned_for_revision',
                get hasChanges() {
                    return $wire.decision !== this.originalDecision ||
                           $wire.requiredActions !== this.originalRequiredActions ||
                           $wire.targetDate !== this.originalTargetDate;
                },
                get decisionWarning() {
                    if ($wire.decision === 'approved_with_corrections') {
                        return 'Faculty must respond with corrections made before verification.';
                    }
                    if ($wire.decision === 'returned_for_revision') {
                        return 'Faculty must make major revisions and resubmit for review.';
                    }
                    if ($wire.decision === 'reclassified_as_revision') {
                        return 'This will reset the review process. Faculty must assign new reviewers.';
                    }
                    return '';
                },
                get decisionVariant() {
                    if ($wire.decision === 'approved_with_corrections') return 'amber';
                    if ($wire.decision === 'returned_for_revision') return 'rose';
                    if ($wire.decision === 'reclassified_as_revision') return 'blue';
                    return 'emerald';
                }
            }"
            x-on:livewire-updated.window="needsActions = $wire.decision === 'returned_for_revision'"
            class="space-y-3">

            <div>
                <label class="block text-[11px] font-bold text-[#72809E] uppercase tracking-widest mb-1.5">
                    Decision
                </label>
                <select wire:model="decision"
                        x-on:change="needsActions = $event.target.value === 'returned_for_revision'"
                        class="w-full text-sm rounded-lg border border-[#E3E8EB] bg-white
                               px-3 py-2 text-[#394056]
                               focus:outline-none focus:border-[#00C075]
                               focus:ring-1 focus:ring-[#00C075]/30 transition-colors">
                    <option value="">— Select decision —</option>
                    <option value="approved_as_updating">Approved as Updating</option>
                    <option value="approved_as_revision">Approved as Revision</option>
                    <option value="approved_with_corrections">Approved with Corrections</option>
                    <option value="returned_for_revision">Returned for Revision</option>
                    <option value="reclassified_as_revision">Reclassified as Revision</option>
                </select>
            </div>

            {{-- Decision-specific warnings and guidance --}}
            <div x-show="decisionWarning" x-cloak
                 class="rounded-lg px-3 py-2.5 border"
                 x-bind:class="{
                    'bg-amber-50 border-amber-200': decisionVariant === 'amber',
                    'bg-rose-50 border-rose-200': decisionVariant === 'rose',
                    'bg-blue-50 border-blue-200': decisionVariant === 'blue'
                 }">
                <div class="flex items-start gap-2">
                    <i class="bx bx-info-circle text-base shrink-0"
                       x-bind:class="{
                           'text-amber-600': decisionVariant === 'amber',
                           'text-rose-600': decisionVariant === 'rose',
                           'text-blue-600': decisionVariant === 'blue'
                       }"></i>
                    <p class="text-xs leading-relaxed"
                       x-bind:class="{
                           'text-amber-800': decisionVariant === 'amber',
                           'text-rose-800': decisionVariant === 'rose',
                           'text-blue-800': decisionVariant === 'blue'
                       }"
                       x-text="decisionWarning"></p>
                </div>
            </div>

            <div x-show="needsActions" x-cloak class="space-y-2">
                <div class="rounded-lg bg-amber-50 border border-amber-200 px-3 py-2">
                    <p class="text-xs text-amber-800">
                        <i class="bx bx-error-circle mr-1"></i>
                        <strong>Required:</strong> You must specify the actions needed and a compliance deadline for this decision type.
                    </p>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#72809E] uppercase tracking-widest mb-1.5">
                        Required Actions <span class="text-rose-400">*</span>
                    </label>
                    <textarea wire:model="requiredActions"
                        rows="3"
                        placeholder="Describe the corrections the faculty must make…"
                        class="w-full text-sm rounded-lg border border-[#E3E8EB] bg-white
                               px-3 py-2 text-[#394056] placeholder:text-[#C1C8D4]
                               focus:outline-none focus:border-[#00C075]
                               focus:ring-1 focus:ring-[#00C075]/30
                               resize-none transition-colors"></textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#72809E] uppercase tracking-widest mb-1.5">
                        Compliance Deadline <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" wire:model="targetDate"
                           class="w-full text-sm rounded-lg border border-[#E3E8EB] bg-white
                                  px-3 py-2 text-[#394056]
                                  focus:outline-none focus:border-[#00C075]
                                  focus:ring-1 focus:ring-[#00C075]/30 transition-colors" />
                </div>
            </div>

            <x-ui.button
                type="button"
                variant="save"
                wire:click="saveDecision"
                wire:loading.attr="disabled"
                wire:target="saveDecision"
                loading="Saving…"
                x-bind:disabled="!hasChanges"
                x-bind:class="!hasChanges ? 'opacity-50 cursor-not-allowed' : ''"
                class="w-full justify-center">
                <i class="bx bx-save text-sm leading-none"></i>
                <span x-text="originalDecision ? 'Update Decision' : 'Save Decision'"></span>
            </x-ui.button>
        </div>
    @endif

    {{-- Recommend approval --}}
    @if ($reviewForm?->decision && ! $reviewForm?->recommended_by_chair_id)
        <div class="mt-4 pt-4 border-t border-[#F1F3F5]">
            <p class="text-xs text-[#72809E] mb-2 leading-relaxed">
                Once satisfied with the checklist and decision,
                recommend this syllabus for dean approval.
            </p>
            <x-ui.button
                type="button"
                variant="add-button"
                wire:click="recommendApproval"
                wire:loading.attr="disabled"
                wire:target="recommendApproval"
                wire:confirm="Recommend this syllabus for dean approval? This cannot be undone."
                loading="Submitting…"
                class="w-full justify-center">
                <i class="bx bx-send text-sm leading-none"></i>
                Recommend for Approval
            </x-ui.button>
        </div>
    @endif

    {{-- Already recommended --}}
    @if ($reviewForm?->recommended_by_chair_id)
        <div class="mt-3 flex items-center gap-2 rounded-lg
                    bg-emerald-50 border border-emerald-200 px-3 py-2.5">
            <i class="bx bx-check-circle text-emerald-600 text-base shrink-0"></i>
            <div>
                <p class="text-xs font-semibold text-emerald-800">Recommended for approval</p>
                <p class="text-[11px] text-emerald-600 mt-0.5">
                    {{ $reviewForm->recommendedByChair?->name }} &middot;
                    {{ $reviewForm->recommended_by_chair_at?->format('M d, Y') }}
                </p>
            </div>
        </div>
    @endif

</x-layout.card-section>
