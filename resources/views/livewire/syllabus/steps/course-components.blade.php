<div>
    <x-wizard.step-header
        title="Course Components"
        icon="notepad"
        description="Fill in details. Changes auto-save when you navigate away." />

    {{-- ══ Lecture ══════════════════════════════════════════════════════════════ --}}
    <x-wizard.section title="Lecture (LEC)" icon="book-open" color="emerald" class="mb-6">
        <div class="space-y-5">

            <div class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-4">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-emerald-700 mb-3">
                    Instructor Profile
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div>
                        <x-form.label isRequired>
                            <i class="bx bx-user"></i>
                            Instructor Name
                        </x-form.label>
                        <x-form.input wire:model.defer="lec_instructor_name"
                            placeholder="Enter instructor name" required />
                    </div>

                    <div>
                        <x-form.label isRequired>
                            <i class="bx bx-envelope"></i>
                            Instructor Email
                        </x-form.label>
                        <x-form.input type="email" wire:model.defer="lec_instructor_email"
                            placeholder="instructor@clsu.edu.ph" required />
                    </div>

                    <div>
                        <x-form.label>
                            <i class="bx bx-phone"></i>
                            Phone (Optional)
                        </x-form.label>
                        <x-form.input wire:model.defer="lec_phone"
                            placeholder="09XX XXX XXXX" />
                    </div>

                    <div>
                        <x-form.label isRequired>
                            <i class="bx bx-building"></i>
                            Office
                        </x-form.label>
                        <x-form.input wire:model.defer="lec_office"
                            placeholder="Building / Room" required />
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-3">
                    Class Delivery
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    <div>
                        <x-form.label isRequired>
                            <i class="bx bx-time"></i>
                            Class Hours
                        </x-form.label>
                        <x-form.select wire:model.defer="lec_class_hours" required>
                            <option value="1 hr">1 hr</option>
                            <option value="1 hr and 30 min">1 hr and 30 min</option>
                            <option value="2 hr">2 hr</option>
                            <option value="2 hr and 30 min">2 hr and 30 min</option>
                            <option value="3 hr">3 hr</option>
                        </x-form.select>
                    </div>

                    <div>
                        <x-form.label isRequired>
                            <i class="bx bx-calendar-event"></i>
                            Schedule
                        </x-form.label>
                        <x-form.input wire:model.defer="lec_schedule"
                            placeholder="e.g., MWF 9:00–10:00 AM" required />
                    </div>

                    <div>
                        <x-form.label isRequired>
                            <i class="bx bx-chat"></i>
                            Consultation Hours
                        </x-form.label>
                        <x-form.input wire:model.defer="lec_consultation_hours"
                            placeholder="e.g., MW 2:00–4:00 PM" required />
                    </div>

                    <div>
                        <x-form.label isRequired>
                            <i class="bx bx-bar-chart-alt-2"></i>
                            Performance Standard
                        </x-form.label>
                        <x-form.select wire:model.defer="lec_performance_standard" required>
                            @if ($course->has_lec_lab)
                                <option value="67%">67%</option>
                            @endif
                            <option value="50%">50%</option>
                            <option value="50%">55%</option>
                            <option value="60%">60%</option>
                            <option value="60%">65%</option>
                            <option value="70%">70%</option>
                            <option value="75%">75%</option>
                            <option value="80%">80%</option>
                        </x-form.select>
                    </div>
                </div>
            </div>
        </div>
    </x-wizard.section>

    {{-- ══ Laboratory (only shown when course has LEC+LAB) ════════════════════ --}}
    @if ($course->has_lec_lab)
        <x-wizard.section title="Laboratory (LAB)" icon="test-tube" color="blue">
            <div class="space-y-5">

                <div class="rounded-xl border border-blue-100 bg-blue-50/40 p-4">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-700 mb-3">
                        Instructor Profile
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div>
                            <x-form.label isRequired>
                                <i class="bx bx-user"></i>
                                Instructor Name
                            </x-form.label>
                            <x-form.input wire:model.defer="lab_instructor_name"
                                placeholder="Enter instructor name" required />
                        </div>

                        <div>
                            <x-form.label isRequired>
                                <i class="bx bx-envelope"></i>
                                Instructor Email
                            </x-form.label>
                            <x-form.input type="email" wire:model.defer="lab_instructor_email"
                                placeholder="instructor@clsu.edu.ph" required />
                        </div>

                        <div>
                            <x-form.label>
                                <i class="bx bx-phone"></i>
                                Phone (Optional)
                            </x-form.label>
                            <x-form.input wire:model.defer="lab_phone"
                                placeholder="09XX XXX XXXX" />
                        </div>

                        <div>
                            <x-form.label isRequired>
                                <i class="bx bx-building"></i>
                                Office
                            </x-form.label>
                            <x-form.input wire:model.defer="lab_office"
                                placeholder="Building / Room" required />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-3">
                        Class Delivery
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                        <div>
                            <x-form.label isRequired>
                                <i class="bx bx-time"></i>
                                Class Hours
                            </x-form.label>
                            <x-form.select wire:model.defer="lec_class_hours" required>
                                <option value="1 hr">1 hr</option>
                                <option value="1 hr and 30 min">1 hr and 30 min</option>
                                <option value="2 hr">2 hr</option>
                                <option value="2 hr and 30 min">2 hr and 30 min</option>
                                <option value="3 hr">3 hr</option>
                            </x-form.select>
                        </div>

                        <div>
                            <x-form.label isRequired>
                                <i class="bx bx-calendar-event"></i>
                                Schedule
                            </x-form.label>
                            <x-form.input wire:model.defer="lab_schedule"
                                placeholder="e.g., T 1:00–4:00 PM" required />
                        </div>

                        <div>
                            <x-form.label isRequired>
                                <i class="bx bx-chat"></i>
                                Consultation Hours
                            </x-form.label>
                            <x-form.input wire:model.defer="lab_consultation_hours"
                                placeholder="e.g., MW 2:00–4:00 PM" required />
                        </div>

                        <div>
                            <x-form.label isRequired>
                                <i class="bx bx-bar-chart-alt-2"></i>
                                Performance Standard
                            </x-form.label>
                            <x-form.select wire:model.defer="lab_performance_standard" required>
                                <option value="50%">33%</option>
                            </x-form.select>
                        </div>
                    </div>
                </div>
            </div>
        </x-wizard.section>
    @endif

</div>