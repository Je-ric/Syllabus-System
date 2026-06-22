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

    @php
        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
    @endphp

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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-form.label>Class Hours</x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-[13px] text-[#475569]">{{ $lec_class_hours }}</p>
                        <p class="mt-1 text-[11px] text-[#94a3b8]">Set in course settings.</p>
                    </div>
                    <div>
                        <x-form.label>
                            Passing Mark
                            @if ($course->has_lec_lab)
                                <span class="text-[#94a3b8] font-normal normal-case tracking-normal">(LEC &amp; LAB)</span>
                            @endif
                        </x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-[13px] text-[#475569]">{{ $lec_performance_standard }}%</p>
                        <p class="mt-1 text-[11px] text-[#94a3b8]">Set in course settings.</p>
                    </div>
                </div>

                {{-- LEC Schedules --}}
                <div class="space-y-2 mb-1">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">Class Schedule</p>
                        <button type="button" wire:click="addSchedule('lec')"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[12px] font-semibold bg-[#f0fdf4] text-[#16a34a] border border-[#bbf7d0] hover:bg-[#dcfce7] transition">
                            <i class="bx bx-plus text-sm"></i> Add
                        </button>
                    </div>
                    @forelse ($lec_schedules as $i => $s)
                        <div class="flex items-center gap-2">
                            <select wire:model.defer="lec_schedules.{{ $i }}.day"
                                class="rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] focus:border-emerald-400 focus:outline-none">
                                @foreach ($days as $d)
                                    <option value="{{ $d }}" {{ ($s['day'] ?? '') === $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                            <input type="text" wire:model.defer="lec_schedules.{{ $i }}.time"
                                placeholder="e.g. 07:30 AM – 09:00 AM"
                                class="flex-1 text-[13px] rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2 focus:border-emerald-400 focus:outline-none focus:bg-white placeholder:text-[#94a3b8]" />
                            <button type="button" wire:click="removeSchedule('lec', {{ $i }})"
                                class="p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition">
                                <i class="bx bx-trash text-sm"></i>
                            </button>
                        </div>
                    @empty
                        <p class="text-[12px] italic text-[#94a3b8]">No schedule added yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="border-t border-[#e2e8f0]"></div>

            {{-- Consultation Hours (from profile, read-only) --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">Consultation Hours</p>
                    <a href="{{ route('profile.index') }}" target="_blank"
                        class="text-[11px] text-emerald-600 hover:underline flex items-center gap-1">
                        <i class="bx bx-edit-alt text-sm"></i> Manage in Profile
                    </a>
                </div>
                @forelse ($userConsultationHours as $ch)
                    <div class="flex items-center gap-3 mb-1.5">
                        <span class="inline-flex items-center justify-center w-10 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-bold py-1 shrink-0">
                            {{ substr($ch['day'], 0, 3) }}
                        </span>
                        <span class="text-[13px] text-slate-700">{{ $ch['day'] }}</span>
                        <span class="text-[13px] text-slate-500">{{ $ch['time'] }}</span>
                    </div>
                @empty
                    <p class="text-[12px] italic text-[#94a3b8]">No consultation hours set. <a href="{{ route('profile.index') }}" class="text-emerald-600 hover:underline">Add them in your profile.</a></p>
                @endforelse
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
                    <div class="mb-4">
                        <x-form.label>Class Hours</x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-[13px] text-[#475569]">{{ $lab_class_hours ?? '—' }}</p>
                        <p class="mt-1 text-[11px] text-[#94a3b8]">Set in course settings.</p>
                    </div>

                    {{-- LAB Schedules --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">Class Schedule</p>
                            <button type="button" wire:click="addSchedule('lab')"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[12px] font-semibold bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100 transition">
                                <i class="bx bx-plus text-sm"></i> Add
                            </button>
                        </div>
                        @forelse ($lab_schedules as $i => $s)
                            <div class="flex items-center gap-2">
                                <select wire:model.defer="lab_schedules.{{ $i }}.day"
                                    class="rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] focus:border-blue-400 focus:outline-none">
                                    @foreach ($days as $d)
                                        <option value="{{ $d }}" {{ ($s['day'] ?? '') === $d ? 'selected' : '' }}>{{ $d }}</option>
                                    @endforeach
                                </select>
                                <input type="text" wire:model.defer="lab_schedules.{{ $i }}.time"
                                    placeholder="e.g. 01:00 PM – 04:00 PM"
                                    class="flex-1 text-[13px] rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2 focus:border-blue-400 focus:outline-none focus:bg-white placeholder:text-[#94a3b8]" />
                                <button type="button" wire:click="removeSchedule('lab', {{ $i }})"
                                    class="p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition">
                                    <i class="bx bx-trash text-sm"></i>
                                </button>
                            </div>
                        @empty
                            <p class="text-[12px] italic text-[#94a3b8]">No schedule added yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </x-wizard.section>
    @endif

</div>
