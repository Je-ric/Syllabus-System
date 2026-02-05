<div>
    <h3 class="text-xl font-semibold mb-6">Course Components</h3>

    {{-- LECTURE Component --}}
    <div class="mb-8 border rounded-lg p-6 bg-gray-50">
        <h4 class="text-lg font-semibold text-blue-600 mb-4 flex items-center gap-2">
            <i class="bx bx-book-open"></i> Lecture (LEC)
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Instructor Info --}}
            <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instructor Name</label>
                        <input type="text" wire:model.debounce.1000ms="lec_instructor_name"
                               class="w-full border rounded-lg px-4 py-2" placeholder="Enter instructor name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instructor Email</label>
                        <input type="email" wire:model.debounce.1000ms="lec_instructor_email"
                               class="w-full border rounded-lg px-4 py-2" placeholder="instructor@clsu.edu.ph">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" wire:model.debounce.1000ms="lec_phone"
                               class="w-full border rounded-lg px-4 py-2" placeholder="09XX XXX XXXX">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Office</label>
                        <input type="text" wire:model.debounce.1000ms="lec_office"
                               class="w-full border rounded-lg px-4 py-2" placeholder="Building / Room">
                    </div>
            </div>

            {{-- Schedule & Requirements --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class Hours</label>
                    <input type="text" wire:model.debounce.1000ms="lec_class_hours"
                           placeholder="e.g., 3 hours/week"
                           class="w-full border rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Schedule (Days/Times)</label>
                    <input type="text" wire:model.debounce.1000ms="lec_schedule"
                           placeholder="e.g., MWF 9:00-10:00, TTh 10:00-11:30"
                           class="w-full border rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Consultation Hours</label>
                    <input type="text" wire:model.debounce.1000ms="lec_consultation_hours"
                           placeholder="e.g., MW 2:00-4:00 PM"
                           class="w-full border rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Performance Standard</label>
                    <select wire:model.debounce.500ms="lec_performance_standard" class="w-full border rounded-lg px-4 py-2">
                        <option value="50%">50%</option>
                        <option value="60%">60%</option>
                        <option value="75%">75%</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- LABORATORY Component (if has_lec_lab) --}}
    @if ($course->has_lec_lab)
        <div class="border rounded-lg p-6 bg-gray-50">
            <h4 class="text-lg font-semibold text-purple-600 mb-4 flex items-center gap-2">
                <i class="bx bx-test-tube"></i> Laboratory (LAB)
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Instructor Info --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instructor Name</label>
                        <input type="text" wire:model.debounce.1000ms="lab_instructor_name"
                               class="w-full border rounded-lg px-4 py-2" placeholder="Enter instructor name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instructor Email</label>
                        <input type="email" wire:model.debounce.1000ms="lab_instructor_email"
                               class="w-full border rounded-lg px-4 py-2" placeholder="instructor@clsu.edu.ph">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" wire:model.debounce.1000ms="lab_phone"
                               class="w-full border rounded-lg px-4 py-2" placeholder="09XX XXX XXXX">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Office</label>
                        <input type="text" wire:model.debounce.1000ms="lab_office"
                               class="w-full border rounded-lg px-4 py-2" placeholder="Building / Room">
                    </div>
                </div>

                {{-- Schedule & Requirements --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class Hours</label>
                        <input type="text" wire:model.debounce.1000ms="lab_class_hours"
                               placeholder="e.g., 3 hours/week"
                               class="w-full border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Schedule (Days/Times)</label>
                        <input type="text" wire:model.debounce.1000ms="lab_schedule"
                               placeholder="e.g., T 1:00-4:00 PM"
                               class="w-full border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Consultation Hours</label>
                        <input type="text" wire:model.debounce.1000ms="lab_consultation_hours"
                               placeholder="e.g., MW 2:00-4:00 PM"
                               class="w-full border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Performance Standard</label>
                        <select wire:model.debounce.500ms="lab_performance_standard" class="w-full border rounded-lg px-4 py-2">
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
