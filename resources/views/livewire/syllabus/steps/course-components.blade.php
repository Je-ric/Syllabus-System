<div>
    <x-wizard.step-header
        title="Course Components"
        icon="notepad"
        description="Fill in details. Changes auto-save when you navigate away." />

    {{-- ══ Lecture ══════════════════════════════════════════════════════════════ --}}
    <x-wizard.section title="Lecture (LEC)" icon="book-open" color="emerald" class="mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">

                {{-- Left column --}}
                <div class="space-y-4">
                    <div>
                        <x-form.label isRequired>Instructor Name</x-form.label>
                        <x-form.input wire:model.defer="lec_instructor_name"
                            placeholder="Enter instructor name" required />
                    </div>
                    <div>
                        <x-form.label isRequired>Instructor Email</x-form.label>
                        <x-form.input type="email" wire:model.defer="lec_instructor_email"
                            placeholder="instructor@clsu.edu.ph" required />
                    </div>
                    <div>
                        <x-form.label isRequired>Phone</x-form.label>
                        <x-form.input wire:model.defer="lec_phone"
                            placeholder="09XX XXX XXXX" required />
                    </div>
                    <div>
                        <x-form.label isRequired>Office</x-form.label>
                        <x-form.input wire:model.defer="lec_office"
                            placeholder="Building / Room" required />
                    </div>
                </div>

                {{-- Right column --}}
                <div class="space-y-4">
                    <div>
                        <x-form.label isRequired>Class Hours</x-form.label>
                        <x-form.input wire:model.defer="lec_class_hours"
                            placeholder="e.g., 3 hrs/week" required />
                    </div>
                    <div>
                        <x-form.label isRequired>Schedule</x-form.label>
                        <x-form.input wire:model.defer="lec_schedule"
                            placeholder="e.g., MWF 9:00–10:00 AM" required />
                    </div>
                    <div>
                        <x-form.label isRequired>Consultation Hours</x-form.label>
                        <x-form.input wire:model.defer="lec_consultation_hours"
                            placeholder="e.g., MW 2:00–4:00 PM" required />
                    </div>
                    <div>
                        <x-form.label isRequired>Performance Standard</x-form.label>
                        <x-form.select wire:model.defer="lec_performance_standard" required>
                            <option value="50%">50%</option>
                            <option value="60%">60%</option>
                            <option value="75%">75%</option>
                        </x-form.select>
                    </div>
                </div>
        </div>
    </x-wizard.section>

    {{-- ══ Laboratory (only shown when course has LEC+LAB) ════════════════════ --}}
    @if ($course->has_lec_lab)
        <x-wizard.section title="Laboratory (LAB)" icon="test-tube" color="blue">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">

                    {{-- Left column --}}
                    <div class="space-y-4">
                        <div>
                            <x-form.label isRequired>Instructor Name</x-form.label>
                            <x-form.input wire:model.defer="lab_instructor_name"
                                placeholder="Enter instructor name" required />
                        </div>
                        <div>
                            <x-form.label isRequired>Instructor Email</x-form.label>
                            <x-form.input type="email" wire:model.defer="lab_instructor_email"
                                placeholder="instructor@clsu.edu.ph" required />
                        </div>
                        <div>
                            <x-form.label isRequired>Phone</x-form.label>
                            <x-form.input wire:model.defer="lab_phone"
                                placeholder="09XX XXX XXXX" required />
                        </div>
                        <div>
                            <x-form.label isRequired>Office</x-form.label>
                            <x-form.input wire:model.defer="lab_office"
                                placeholder="Building / Room" required />
                        </div>
                    </div>

                    {{-- Right column --}}
                    <div class="space-y-4">
                        <div>
                            <x-form.label isRequired>Class Hours</x-form.label>
                            <x-form.input wire:model.defer="lab_class_hours"
                                placeholder="e.g., 3 hrs/week" required />
                        </div>
                        <div>
                            <x-form.label isRequired>Schedule</x-form.label>
                            <x-form.input wire:model.defer="lab_schedule"
                                placeholder="e.g., T 1:00–4:00 PM" required />
                        </div>
                        <div>
                            <x-form.label isRequired>Consultation Hours</x-form.label>
                            <x-form.input wire:model.defer="lab_consultation_hours"
                                placeholder="e.g., MW 2:00–4:00 PM" required />
                        </div>
                        <div>
                            <x-form.label isRequired>Performance Standard</x-form.label>
                            <x-form.select wire:model.defer="lab_performance_standard" required>
                                <option value="50%">50%</option>
                                <option value="60%">60%</option>
                                <option value="75%">75%</option>
                            </x-form.select>
                        </div>
                    </div>
            </div>
        </x-wizard.section>
    @endif
</div>
