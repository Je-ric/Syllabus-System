<div>
    {{-- Header --}}
    <div class="mb-5">
        <h3 class="text-xl font-semibold text-slate-900">Course Components</h3>
        <p class="text-sm text-slate-500 mt-0.5">Fill in lecture and laboratory details. Changes auto-save when you navigate away.</p>
    </div>

    {{-- ══ Lecture ══════════════════════════════════════════════════════════════ --}}
    <div class="mb-6 rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-slate-100 bg-emerald-50">
            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 shrink-0">
                <i class="bx bx-book-open text-base"></i>
            </span>
            <h4 class="text-sm font-semibold text-emerald-800">Lecture (LEC)</h4>
        </div>

        <div class="p-5">
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
        </div>
    </div>

    {{-- ══ Laboratory (only shown when course has LEC+LAB) ════════════════════ --}}
    @if ($course->has_lec_lab)
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-slate-100 bg-blue-50">
                <span class="flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-700 shrink-0">
                    <i class="bx bx-test-tube text-base"></i>
                </span>
                <h4 class="text-sm font-semibold text-blue-800">Laboratory (LAB)</h4>
            </div>

            <div class="p-5">
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
            </div>
        </div>
    @endif
</div>