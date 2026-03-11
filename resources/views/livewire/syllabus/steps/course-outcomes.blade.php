<div class="space-y-4 text-slate-800">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <x-wizard.step-header
        title="Course Outcomes"
        icon="book-open"
        description="Define what students should be able to do after completing this course.
                    Align each outcome with the most relevant Program Outcomes —
                    not every CO needs to cover all POs.">

        <x-button variant="sm-success"
            wire:click="saveAll"
            wire:target="saveAll"
            loading="Saving…">
            <i class="bx bx-save"></i> Save All
        </x-button>

    </x-wizard.step-header>

    {{-- ── CO rows ─────────────────────────────────────────────────────────── --}}
    <x-wizard.section title="Build Outcomes" icon="list-check" color="emerald">

        <div class="space-y-3">

            @forelse ($rows as $index => $row)

                <div wire:key="co-row-{{ $index }}"
                    class="flex items-start gap-3 rounded-2xl border p-4 transition-colors duration-150
                           {{ empty($row['id']) ? 'border-amber-200 bg-amber-50/40' : 'border-slate-200 bg-white' }}">

                    {{-- CO badge --}}
                    <span class="mt-1 shrink-0 inline-flex items-center justify-center
                                 min-w-11 h-8 px-2 rounded-xl text-xs font-bold uppercase
                                 {{ empty($row['id'])
                                     ? 'bg-amber-100 text-amber-700 ring-1 ring-amber-200'
                                     : 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200' }}">
                        CO{{ $index + 1 }}
                    </span>

                    {{-- Textarea --}}
                    <div class="flex-1 min-w-0">
                        <textarea
                            wire:model="rows.{{ $index }}.description"
                            rows="2"
                            placeholder="Describe what students will be able to do after completing this course…"
                            class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-3 py-2
                                   text-sm text-slate-800 placeholder:text-slate-300
                                   focus:bg-white focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300
                                   focus:outline-none transition-colors"></textarea>

                        @if (empty($row['id']))
                            <p class="mt-1 text-xs text-amber-600 flex items-center gap-1">
                                <i class="bx bx-error-circle"></i>
                                Unsaved — click <strong class="mx-0.5">Save All</strong> to persist.
                            </p>
                        @endif
                    </div>

                    {{-- Remove button --}}
                    <button type="button"
                        wire:click="removeRow({{ $index }})"
                        wire:confirm="{{ empty($row['id']) ? 'Remove this unsaved row?' : 'Remove CO' . ($index + 1) . '? This cannot be undone.' }}"
                        wire:loading.attr="disabled"
                        wire:target="removeRow({{ $index }})"
                        class="mt-1 shrink-0 p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                        title="Remove">
                        <i class="bx {{ empty($row['id']) ? 'bx-x' : 'bx-trash' }} text-base"></i>
                    </button>

                </div>

            @empty

                <div class="flex flex-col items-center gap-2 py-10 text-center">
                    <i class="bx bx-book-open text-3xl text-slate-200"></i>
                    <p class="text-sm font-medium text-slate-500">No Course Outcomes yet</p>
                    <p class="text-xs text-slate-400">Add the first one below.</p>
                </div>

            @endforelse

        </div>

        {{-- Add row button --}}
        <div class="pt-2">
            <button type="button"
                wire:click="addRow"
                class="flex w-full items-center justify-center gap-2 px-4 py-3
                        border-2 border-dashed border-emerald-300 rounded-2xl
                        text-sm font-semibold text-emerald-700
                        hover:border-emerald-50a0 hover:bg-emerald-50
                        transition-colors duration-150">
                <i class="bx bx-plus text-base"></i>
                Add Course Outcome
            </button>
        </div>

    </x-wizard.section>

    {{-- ── Program Outcomes reference ──────────────────────────────────────── --}}
    @if (count($programOutcomes) > 0)
        <x-wizard.section title="Program Outcomes Reference" icon="list-check" color="slate">
            <div class="space-y-2">
                @foreach ($programOutcomes as $po)
                    <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                        <span class="mt-0.5 shrink-0 inline-flex items-center justify-center
                                     w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700
                                     text-xs font-bold uppercase ring-1 ring-emerald-200">
                            {{ $po['po_code'] }}.
                        </span>
                        <p class="text-sm text-slate-600 leading-relaxed flex-1">{{ $po['po_text'] }}</p>
                        @if (! empty($po['ied']))
                            <x-feedback-status.ied-badge :level="$po['ied']" />
                        @endif
                    </div>
                @endforeach
            </div>
        </x-wizard.section>
    @else
        <x-empty-state
            icon="list-check"
            title="No Program Outcomes"
            description="Program Outcomes are defined at the program level.
                        If you think there should be POs here, check with your department's CSMS coordinator.">
        </x-empty-state>
    @endif

</div>
