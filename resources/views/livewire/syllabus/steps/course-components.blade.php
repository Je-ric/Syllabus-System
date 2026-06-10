<div>
    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <x-wizard.step-header
        title="Course Components"
        icon="notepad"
        description="Fill in instructor details and class delivery info.">
        <x-button variant="sm-add" wire:click="save" wireTarget="save" loading="Saving…">
            <i class="bx bx-save"></i> Save All
        </x-button>
    </x-wizard.step-header>

    {{-- ══ Lecture ═══════════════════════════════════════════════════════════ --}}
    <x-wizard.section title="Lecture (LEC)" icon="book-open" color="emerald" class="mb-5">
        <div class="space-y-5">

            {{-- Instructor Profile --}}
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">Instructor Profile</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-form.label isRequired>Instructor Name</x-form.label>
                        <x-form.input wire:model.defer="lec_instructor_name" placeholder="Enter instructor name" />
                    </div>
                    <div>
                        <x-form.label isRequired>Instructor Email</x-form.label>
                        <x-form.input type="email" wire:model.defer="lec_instructor_email" placeholder="instructor@clsu.edu.ph" />
                    </div>
                    <div>
                        <x-form.label>Phone <span class="text-[#94a3b8] font-normal normal-case tracking-normal">(optional)</span></x-form.label>
                        <x-form.input wire:model.defer="lec_phone" placeholder="09XX XXX XXXX" />
                    </div>
                    <div>
                        <x-form.label>Office</x-form.label>
                        <x-form.input wire:model.defer="lec_office" placeholder="Building / Room" />
                    </div>
                </div>
            </div>

            <div class="border-t border-[#e2e8f0]"></div>

            {{-- Class Delivery --}}
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">Class Delivery</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-form.label>Class Hours</x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-[13px] text-[#475569]">
                            {{ $lec_class_hours }}
                        </p>
                        <p class="mt-1 text-[11px] text-[#94a3b8]">Set in course settings.</p>
                    </div>
                    <div>
                        <x-form.label isRequired>Schedule</x-form.label>
                        <x-form.input wire:model.defer="lec_schedule" placeholder="e.g., MWF 9:00–10:00 AM" />
                    </div>
                    <div>
                        <x-form.label isRequired>Consultation Hours</x-form.label>
                        <x-form.input wire:model.defer="lec_consultation_hours" placeholder="e.g., MW 2:00–4:00 PM" />
                    </div>
                    <div>
                        <x-form.label>
                            Passing Mark
                            @if ($course->has_lec_lab)
                                <span class="text-[#94a3b8] font-normal normal-case tracking-normal">(LEC &amp; LAB)</span>
                            @endif
                        </x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-[13px] text-[#475569]">
                            {{ $lec_performance_standard }}%
                        </p>
                        <p class="mt-1 text-[11px] text-[#94a3b8]">Set in course settings.</p>
                    </div>
                </div>
            </div>

        </div>
    </x-wizard.section>

    {{-- ══ Laboratory ═══════════════════════════════════════════════════════ --}}
    @if ($course->has_lec_lab)
        <x-wizard.section title="Laboratory (LAB)" icon="test-tube" color="blue">
            <div class="space-y-5">

                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">Instructor Profile</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-form.label isRequired>Instructor Name</x-form.label>
                            <x-form.input wire:model.defer="lab_instructor_name" placeholder="Enter instructor name" />
                        </div>
                        <div>
                            <x-form.label isRequired>Instructor Email</x-form.label>
                            <x-form.input type="email" wire:model.defer="lab_instructor_email" placeholder="instructor@clsu.edu.ph" />
                        </div>
                        <div>
                            <x-form.label>Phone <span class="text-[#94a3b8] font-normal normal-case tracking-normal">(optional)</span></x-form.label>
                            <x-form.input wire:model.defer="lab_phone" placeholder="09XX XXX XXXX" />
                        </div>
                        <div>
                            <x-form.label>Office</x-form.label>
                            <x-form.input wire:model.defer="lab_office" placeholder="Building / Room" />
                        </div>
                    </div>
                </div>

                <div class="border-t border-[#e2e8f0]"></div>

                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">Class Delivery</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-form.label>Class Hours</x-form.label>
                            <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-[13px] text-[#475569]">
                                {{ $lab_class_hours ?? '—' }}
                            </p>
                            <p class="mt-1 text-[11px] text-[#94a3b8]">Set in course settings.</p>
                        </div>
                        <div>
                            <x-form.label isRequired>Schedule</x-form.label>
                            <x-form.input wire:model.defer="lab_schedule" placeholder="e.g., T 1:00–4:00 PM" />
                        </div>
                        <div>
                            <x-form.label isRequired>Consultation Hours</x-form.label>
                            <x-form.input wire:model.defer="lab_consultation_hours" placeholder="e.g., MW 2:00–4:00 PM" />
                        </div>
                    </div>
                </div>

            </div>
        </x-wizard.section>
    @endif

</div>
