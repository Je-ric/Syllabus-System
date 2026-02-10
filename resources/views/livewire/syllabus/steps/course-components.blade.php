<div>
    <div class="mb-4">
        <h3 class="text-xl font-semibold text-slate-900">Course Components</h3>
        <p class="text-sm text-slate-600">Fill out lecture and laboratory details. Autosaves on blur.</p>
    </div>

    {{-- LECTURE --}}
    <div class="mb-8 border border-slate-200 rounded-xl p-6">
        <h4 class="text-lg font-semibold text-green-700 mb-4 flex items-center gap-2">
            <i class="bx bx-book-open"></i> Lecture (LEC)
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Instructor Info --}}
            <div class="space-y-4">
                <div>
                    <x-form.label isRequired>
                        Instructor Name
                    </x-form.label>
                    <x-form.input
                        wire:model.blur="lec_instructor_name"
                        placeholder="Enter instructor name"
                        required
                    />
                </div>

                <div>
                    <x-form.label isRequired>
                        Instructor Email
                    </x-form.label>
                    <x-form.input
                        type="email"
                        wire:model.blur="lec_instructor_email"
                        placeholder="instructor@clsu.edu.ph"
                        required
                    />
                </div>

                <div>
                    <x-form.label isRequired>
                        Phone
                    </x-form.label>
                    <x-form.input
                        wire:model.blur="lec_phone"
                        placeholder="09XX XXX XXXX"
                        required
                    />
                </div>

                <div>
                    <x-form.label isRequired>
                        Office
                    </x-form.label>
                    <x-form.input
                        wire:model.blur="lec_office"
                        placeholder="Building / Room"
                        required
                    />
                </div>
            </div>

            {{-- Schedule & Requirements --}}
            <div class="space-y-4">
                <div>
                    <x-form.label isRequired>
                        Class Hours
                    </x-form.label>
                    <x-form.input
                        wire:model.blur="lec_class_hours"
                        placeholder="e.g., 3 hours/week"
                        required
                    />
                </div>

                <div>
                    <x-form.label isRequired>
                        Schedule (Days / Times)
                    </x-form.label>
                    <x-form.input
                        wire:model.blur="lec_schedule"
                        placeholder="e.g., MWF 9:00–10:00"
                        required
                    />
                </div>

                <div>
                    <x-form.label isRequired>
                        Consultation Hours
                    </x-form.label>
                    <x-form.input
                        wire:model.blur="lec_consultation_hours"
                        placeholder="e.g., MW 2:00–4:00 PM"
                        required
                    />
                </div>

                <div>
                    <x-form.label isRequired>
                        Performance Standard
                    </x-form.label>
                    <select
                        wire:model.blur="lec_performance_standard"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    >
                        <option value="50%">50%</option>
                        <option value="60%">60%</option>
                        <option value="75%">75%</option>
                    </select>
                </div>
            </div>

        </div>
    </div>

    @if ($course->has_lec_lab)
        <div class="border border-slate-200 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-green-700 mb-4 flex items-center gap-2">
                <i class="bx bx-test-tube"></i> Laboratory (LAB)
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Instructor Info --}}
                <div class="space-y-4">
                    <div>
                        <x-form.label isRequired>
                            Instructor Name
                        </x-form.label>
                        <x-form.input
                            wire:model.blur="lab_instructor_name"
                            placeholder="Enter instructor name"
                            required
                        />
                    </div>

                    <div>
                        <x-form.label isRequired>
                            Instructor Email
                        </x-form.label>
                        <x-form.input
                            type="email"
                            wire:model.blur="lab_instructor_email"
                            placeholder="instructor@clsu.edu.ph"
                            required
                        />
                    </div>

                    <div>
                        <x-form.label isRequired>
                            Phone
                        </x-form.label>
                        <x-form.input
                            wire:model.blur="lab_phone"
                            placeholder="09XX XXX XXXX"
                            required
                        />
                    </div>

                    <div>
                        <x-form.label isRequired>
                            Office
                        </x-form.label>
                        <x-form.input
                            wire:model.blur="lab_office"
                            placeholder="Building / Room"
                            required
                        />
                    </div>
                </div>

                {{-- Schedule & Requirements --}}
                <div class="space-y-4">
                    <div>
                        <x-form.label isRequired>
                            Class Hours
                        </x-form.label>
                        <x-form.input
                            wire:model.blur="lab_class_hours"
                            placeholder="e.g., 3 hours/week"
                            required
                        />
                    </div>

                    <div>
                        <x-form.label isRequired>
                            Schedule (Days / Times)
                        </x-form.label>
                        <x-form.input
                            wire:model.blur="lab_schedule"
                            placeholder="e.g., T 1:00–4:00 PM"
                            required
                        />
                    </div>

                    <div>
                        <x-form.label isRequired>
                            Consultation Hours
                        </x-form.label>
                        <x-form.input
                            wire:model.blur="lab_consultation_hours"
                            placeholder="e.g., MW 2:00–4:00 PM"
                            required
                        />
                    </div>

                    <div>
                        <x-form.label isRequired>
                            Performance Standard
                        </x-form.label>
                        <select
                            wire:model.blur="lab_performance_standard"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        >
                            <option value="50%">50%</option>
                            <option value="60%">60%</option>
                            <option value="75%">75%</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
