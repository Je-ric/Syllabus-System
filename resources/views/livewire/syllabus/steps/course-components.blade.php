<div>
    <x-wizard.step-header
        title="Course Components"
        description="Fill in instructor details and class delivery info for each component." />

    @php $days = ['Monday','Tuesday','Wednesday','Thursday','Friday']; @endphp

    {{-- ══ Lecture ═══════════════════════════════════════════════════════════ --}}
    <x-wizard.section title="Lecture (LEC)" icon="book-open" color="emerald">
        <div class="space-y-5">

            {{-- Instructor Profile --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3 flex items-center gap-2">
                    <span class="h-px w-4 bg-[#16a34a]"></span> Instructor Profile
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-form.label isRequired>Instructor Name</x-form.label>
                        <x-form.input wire:model.defer="lec_instructor_name" placeholder="Enter instructor name" disabled />
                    </div>
                    <div>
                        <x-form.label isRequired>Instructor Email</x-form.label>
                        <x-form.input type="email" wire:model.defer="lec_instructor_email" placeholder="instructor@clsu.edu.ph" disabled />
                    </div>
                    <div>
                        <x-form.label>Phone <span class="text-slate-400 font-normal normal-case tracking-normal">(optional)</span></x-form.label>
                        <x-form.input wire:model.defer="lec_phone" placeholder="09XX XXX XXXX" disabled />
                    </div>
                    <div>
                        <x-form.label>Office</x-form.label>
                        <x-form.input wire:model.defer="lec_office" placeholder="Building / Room" disabled />
                    </div>
                </div>
            </div>

            <div class="border-t border-[#e2e8f0]"></div>

            {{-- Class Delivery --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3 flex items-center gap-2">
                    <span class="h-px w-4 bg-[#16a34a]"></span> Class Delivery
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-form.label>Class Hours</x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">{{ $lec_class_hours }}</p>
                        <p class="mt-1 text-xs text-[#94a3b8]">Set in course settings.</p>
                    </div>
                    <div>
                        <x-form.label>
                            Passing Mark
                            @if ($course->has_lec_lab)
                                <span class="text-[#94a3b8] font-normal normal-case tracking-normal">(LEC &amp; LAB)</span>
                            @endif
                        </x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">{{ $lec_performance_standard }}%</p>
                        <p class="mt-1 text-xs text-[#94a3b8]">Set in course settings.</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-[#e2e8f0]"></div>

            {{-- ── Side-by-side: Class Schedule | Consultation Hours ─────── --}}
            <div
                x-data="{
                    days: ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                    schedules: @js($lec_schedules ?? []),
                    hours: @js($userConsultationHours ?? []),
                    saving: false,

                    addSchedule()    { this.schedules.push({ day: 'Monday', time: '' }); },
                    removeSchedule(i){ this.schedules.splice(i, 1); },

                    addRow()       { this.hours.push({ day: 'Monday', time: '' }); },
                    removeRow(i)   { this.hours.splice(i, 1); },

                    async saveConsultation() {
                        this.saving = true;
                        await $wire.saveConsultationHours(this.hours);
                        this.saving = false;
                    },

                    async pushToWire() {
                        await $wire.pushLecSchedules(this.schedules);
                        await $wire.pushConsultationHours(this.hours);
                    },
                }"
                x-on:lec-schedules-updated.window="schedules = $event.detail.schedules"
                x-on:consultation-hours-updated.window="hours = $event.detail.hours"
                x-on:before-save-all.window="await pushToWire()"
            >

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- ── Class Schedule (LEC) ─────────────────────────── --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-bold uppercase tracking-widest text-[#475569]">Class Schedule</p>
                            <button type="button" x-on:click="addSchedule()"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                                       bg-[#f0fdf4] text-[#16a34a] border border-[#bbf7d0]
                                       hover:bg-[#dcfce7] transition">
                                <i class="bx bx-plus text-sm"></i> Add
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(row, i) in schedules" :key="i">
                                <div class="flex items-center gap-2">
                                    <x-form.select x-model="row.day">
                                        <template x-for="d in days" :key="d">
                                            <option :value="d" x-text="d"></option>
                                        </template>
                                    </x-form.select>
                                    <input type="text" x-model="row.time"
                                        placeholder="e.g. 07:30 AM – 09:00 AM"
                                        class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                               focus:border-emerald-400 focus:outline-none focus:bg-white
                                               placeholder:text-[#94a3b8]" />
                                    <button type="button" x-on:click="removeSchedule(i)"
                                        class="p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition">
                                        <i class="bx bx-trash text-sm"></i>
                                    </button>
                                </div>
                            </template>
                            <template x-if="schedules.length === 0">
                                <p class="text-sm italic text-[#94a3b8]">No schedule added yet.</p>
                            </template>
                        </div>
                    </div>

                    {{-- ── Consultation Hours ───────────────────────────── --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-bold uppercase tracking-widest text-[#475569]">Consultation Hours</p>
                            <button type="button" x-on:click="addRow()"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                                       bg-amber-50 text-amber-600 border border-amber-200
                                       hover:bg-amber-100 transition">
                                <i class="bx bx-plus text-sm"></i> Add
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(row, i) in hours" :key="i">
                                <div class="flex items-center gap-2">
                                    <x-form.select x-model="row.day">
                                        <template x-for="d in days" :key="d">
                                            <option :value="d" x-text="d"></option>
                                        </template>
                                    </x-form.select>
                                    <input type="text" x-model="row.time"
                                        placeholder="e.g. 09:00 AM – 11:00 AM"
                                        class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                               focus:border-amber-400 focus:outline-none focus:bg-white
                                               placeholder:text-[#94a3b8]" />
                                    <button type="button" x-on:click="removeRow(i)"
                                        class="p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition">
                                        <i class="bx bx-trash text-sm"></i>
                                    </button>
                                </div>
                            </template>
                            <template x-if="hours.length === 0">
                                <p class="text-sm italic text-[#94a3b8]">No consultation hours added.</p>
                            </template>
                        </div>
                    </div>

                </div><!-- /grid -->
            </div><!-- /x-data consultation -->

        </div>
    </x-wizard.section>

    {{-- ══ Laboratory ═══════════════════════════════════════════════════════ --}}
    @if ($course->has_lec_lab)
        <x-wizard.section title="Laboratory (LAB)" icon="test-tube" color="blue">
            <div class="space-y-5">

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3 flex items-center gap-2">
                        <span class="h-px w-4 bg-[#2563eb]"></span> Instructor Profile
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-form.label isRequired>Instructor Name</x-form.label>
                            <x-form.input wire:model.defer="lab_instructor_name" placeholder="Enter instructor name" disabled />
                        </div>
                        <div>
                            <x-form.label isRequired>Instructor Email</x-form.label>
                            <x-form.input type="email" wire:model.defer="lab_instructor_email" placeholder="instructor@clsu.edu.ph" disabled />
                        </div>
                        <div>
                            <x-form.label>Phone <span class="text-slate-400 font-normal normal-case tracking-normal">(optional)</span></x-form.label>
                            <x-form.input wire:model.defer="lab_phone" placeholder="09XX XXX XXXX" disabled />
                        </div>
                        <div>
                            <x-form.label>Office</x-form.label>
                            <x-form.input wire:model.defer="lab_office" placeholder="Building / Room" disabled />
                        </div>
                    </div>
                </div>

                <div class="border-t border-[#e2e8f0]"></div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3 flex items-center gap-2">
                        <span class="h-px w-4 bg-[#2563eb]"></span> Class Delivery
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-form.label>Class Hours</x-form.label>
                            <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">{{ $lab_class_hours ?? '—' }}</p>
                            <p class="mt-1 text-xs text-[#94a3b8]">Set in course settings.</p>
                        </div>
                        <div>
                            <x-form.label>
                                Passing Mark
                                @if ($course->has_lec_lab)
                                    <span class="text-[#94a3b8] font-normal normal-case tracking-normal">(LEC & LAB)</span>
                                @endif
                            </x-form.label>
                            <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">{{ $lec_performance_standard }}%</p>
                            <p class="mt-1 text-xs text-[#94a3b8]">Set in course settings.</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-[#e2e8f0]"></div>

                {{-- ── Side-by-side: Class Schedule | Consultation Hours ─────── --}}
                <div
                    x-data="{
                        days: ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                        schedules: @js($lab_schedules ?? []),
                        hours: @js($labConsultationHours ?? []),
                        saving: false,

                        addSchedule()    { this.schedules.push({ day: 'Monday', time: '' }); },
                        removeSchedule(i){ this.schedules.splice(i, 1); },

                        addRow()       { this.hours.push({ day: 'Monday', time: '' }); },
                        removeRow(i)   { this.hours.splice(i, 1); },

                        async saveConsultation() {
                            this.saving = true;
                            await $wire.saveLabConsultationHours(this.hours);
                            this.saving = false;
                        },

                        async pushToWire() {
                            await $wire.pushLabSchedules(this.schedules);
                            await $wire.pushLabConsultationHours(this.hours);
                        },
                    }"
                    x-on:lab-schedules-updated.window="schedules = $event.detail.schedules"
                    x-on:lab-consultation-hours-updated.window="hours = $event.detail.hours"
                    x-on:before-save-all.window="await pushToWire()"
                >

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        {{-- ── Class Schedule (LAB) ─────────────────────────── --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-bold uppercase tracking-widest text-[#475569]">Class Schedule</p>
                                <button type="button" x-on:click="addSchedule()"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                                           bg-blue-50 text-blue-600 border border-blue-200
                                           hover:bg-blue-100 transition">
                                    <i class="bx bx-plus text-sm"></i> Add
                                </button>
                            </div>

                            <div class="space-y-2">
                                <template x-for="(row, i) in schedules" :key="i">
                                    <div class="flex items-center gap-2">
                                        <x-form.select x-model="row.day">
                                            <template x-for="d in days" :key="d">
                                                <option :value="d" x-text="d"></option>
                                            </template>
                                        </x-form.select>
                                        <input type="text" x-model="row.time"
                                            placeholder="e.g. 01:00 PM – 04:00 PM"
                                            class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                                   focus:border-blue-400 focus:outline-none focus:bg-white
                                                   placeholder:text-[#94a3b8]" />
                                        <button type="button" x-on:click="removeSchedule(i)"
                                            class="p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition">
                                            <i class="bx bx-trash text-sm"></i>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="schedules.length === 0">
                                    <p class="text-sm italic text-[#94a3b8]">No schedule added yet.</p>
                                </template>
                            </div>
                        </div>

                        {{-- ── Consultation Hours (from user) ───────────────────────────── --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-bold uppercase tracking-widest text-[#475569]">Consultation Hours</p>
                                <button type="button" x-on:click="addRow()"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                                           bg-amber-50 text-amber-600 border border-amber-200
                                           hover:bg-amber-100 transition">
                                    <i class="bx bx-plus text-sm"></i> Add
                                </button>
                            </div>

                            <div class="space-y-2">
                                <template x-for="(row, i) in hours" :key="i">
                                    <div class="flex items-center gap-2">
                                        <x-form.select x-model="row.day">
                                            <template x-for="d in days" :key="d">
                                                <option :value="d" x-text="d"></option>
                                            </template>
                                        </x-form.select>
                                        <input type="text" x-model="row.time"
                                            placeholder="e.g. 09:00 AM – 11:00 AM"
                                            class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                                   focus:border-amber-400 focus:outline-none focus:bg-white
                                                   placeholder:text-[#94a3b8]" />
                                        <button type="button" x-on:click="removeRow(i)"
                                            class="p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition">
                                            <i class="bx bx-trash text-sm"></i>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="hours.length === 0">
                                    <p class="text-sm italic text-[#94a3b8]">No consultation hours added.</p>
                                </template>
                            </div>
                        </div>

                    </div><!-- /grid -->
                </div><!-- /x-data consultation -->

               
            </div>
        </x-wizard.section>
    @endif

    {{-- ── Bottom Save All ──────────────────────────────────────────────────── --}}
    {{--
        We dispatch `before-save-all` first so the Alpine consultation component
        can push its local `hours` array into Livewire ($wire.pushConsultationHours)
        before $wire.save() runs. Because Alpine's x-on listener is async-aware,
        the await in pushToWire() completes before Livewire.dispatch resolves.
    --}}
    <div class="flex justify-end pt-1">
        <x-button variant="sm-add"
            x-on:click="
                $dispatch('before-save-all');
                await $nextTick();
                $wire.save();
            "
            wireTarget="save"
            loading="Saving…">
            <i class="bx bx-save"></i> Save All
        </x-button>
    </div>

</div>
