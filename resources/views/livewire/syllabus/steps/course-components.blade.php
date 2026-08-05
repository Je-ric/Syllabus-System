<div
    x-data="{ _pushing: false, _saving: false }"
    x-on:request-push-and-navigate.window="
        if (_pushing) return;
        _pushing = true;
        window._beforeSaveAllPromises = [];
        window.dispatchEvent(new CustomEvent('before-save-all'));
        try {
            await Promise.all(window._beforeSaveAllPromises);
        } catch { _pushing = false; return; }
        await $wire.onPushAndNavigate($event.detail.toStep);
        _pushing = false;
    ">
    <x-wizard.step-header :step="2" icon="bx-notepad" title="Course Components"
        description="Fill in instructor details and class delivery info for each component." />

    {{-- ══ Lecture ═══════════════════════════════════════════════════════════ --}}
    <x-layout.card title="Lecture (LEC)" icon="book-open" color="emerald" class="mb-5">
        <div class="space-y-5">

            {{-- Instructor Profile --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3 flex items-center gap-2">
                    <span class="h-px w-4 bg-[#16a34a]"></span> Instructor Profile
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-form.label>Instructor Name</x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">
                            {{ $lecUser?->name ?? '—' }}</p>
                        <p class="mt-1 text-xs text-slate-400">Auto-populated from your account profile.</p>
                    </div>
                    <div>
                        <x-form.label>Instructor Email</x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">
                            {{ $lecUser?->email ?? '—' }}</p>
                        <p class="mt-1 text-xs text-slate-400">Auto-populated from your account profile.</p>
                    </div>
                    <div>
                        <x-form.label>Phone</x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">
                            {{ $lecUser?->phone_number ?? '—' }}</p>
                        <p class="mt-1 text-xs text-slate-400">Auto-populated from your account profile.</p>
                    </div>
                    <div>
                        <x-form.label>Office</x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">
                            {{ $lecUser?->office ?? '—' }}</p>
                        <p class="mt-1 text-xs text-slate-400">Auto-populated from your account profile.</p>
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
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">
                            {{ $lec_class_hours }}</p>
                        <p class="mt-1 text-xs text-[#94a3b8]">Set in course settings.</p>
                    </div>
                    <div>
                        <x-form.label>
                            Passing Mark
                            @if ($course->has_lec_lab)
                                <span class="text-[#94a3b8] font-normal normal-case tracking-normal">(LEC &amp; LAB)</span>
                            @endif
                        </x-form.label>
                        <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">
                            {{ $lec_performance_standard }}%</p>
                        <p class="mt-1 text-xs text-[#94a3b8]">Set in course settings.</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-[#e2e8f0]"></div>

            {{-- ── Side-by-side: Class Schedule | Consultation Hours ─────── --}}
            <div x-data="{
                schedules: @js($lec_schedules ?? []).map(s => ({ day: s.day, ...parseTime(s.time) })),
                hours: @js($userConsultationHours ?? []).map(h => ({ day: h.day, ...parseTime(h.time) })),

                addSchedule() { this.schedules.push({ day: 'Monday', startTime: '', endTime: '' }); },
                removeSchedule(i) { this.schedules.splice(i, 1); },

                timesOverlap(aStart, aEnd, bStart, bEnd) {
                    if (!aStart || !aEnd || !bStart || !bEnd) return false;
                    return aStart < bEnd && bStart < aEnd;
                },
                hasConflict(hourRow) {
                    return this.schedules.some(s =>
                        s.day === hourRow.day &&
                        this.timesOverlap(hourRow.startTime, hourRow.endTime, s.startTime, s.endTime)
                    );
                },
                hasAnyConflicts() {
                    return this.hours.some(h => this.hasConflict(h));
                },

                async pushToWire() {
                    if (this.hasAnyConflicts()) {
                        window.dispatchEvent(new CustomEvent('lw-toast', {
                            detail: { type: 'error', message: 'Fix overlapping consultation hours (LEC) before saving.' }
                        }));
                        return Promise.reject('lec-conflict');
                    }
                    await this.$wire.pushLecSchedules(this.schedules.map(s => ({ day: s.day, time: formatTime(s.startTime, s.endTime) })));
                    await this.$wire.pushConsultationHours(this.hours.map(h => ({ day: h.day, time: formatTime(h.startTime, h.endTime) })));
                },
            }" x-on:lec-schedules-updated.window="schedules = $event.detail.schedules"
                x-on:before-save-all.window="window._beforeSaveAllPromises.push(pushToWire())">

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
                                <div class="flex items-center gap-2" role="group" :aria-label="'LEC schedule row ' + (i + 1)">
                                    <x-form.select x-model="row.day" aria-label="Day">
                                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $d)
                                            <option value="{{ $d }}">{{ $d }}</option>
                                        @endforeach
                                    </x-form.select>
                                    <input type="time" x-model="row.startTime" aria-label="Start time"
                                        class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                               focus:border-emerald-400 focus:outline-none focus:bg-white" />
                                    <span class="text-xs text-slate-400 shrink-0">to</span>
                                    <input type="time" x-model="row.endTime" aria-label="End time"
                                        class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                               focus:border-emerald-400 focus:outline-none focus:bg-white" />
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
                            <button type="button" x-on:click="hours.push({ day: 'Monday', startTime: '', endTime: '' })"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                                       bg-amber-50 text-amber-600 border border-amber-200
                                       hover:bg-amber-100 transition">
                                <i class="bx bx-plus text-sm"></i> Add
                            </button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(row, i) in hours" :key="i">
                                <div>
                                    <div class="flex items-center gap-2" role="group" :aria-label="'LEC consultation row ' + (i + 1)" :class="hasConflict(row) ? 'ring-1 ring-rose-300 rounded-lg p-1' : ''">
                                        <x-form.select x-model="row.day" aria-label="Day">
                                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $d)
                                                <option value="{{ $d }}">{{ $d }}</option>
                                            @endforeach
                                        </x-form.select>
                                        <input type="time" x-model="row.startTime" aria-label="Start time"
                                            class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                                   focus:border-amber-400 focus:outline-none focus:bg-white" />
                                        <span class="text-xs text-slate-400 shrink-0">to</span>
                                        <input type="time" x-model="row.endTime" aria-label="End time"
                                            class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                                   focus:border-amber-400 focus:outline-none focus:bg-white" />
                                        <button type="button" x-on:click="hours.splice(i, 1)"
                                            class="p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition">
                                            <i class="bx bx-trash text-sm"></i>
                                        </button>
                                    </div>
                                    <p x-show="hasConflict(row)" x-cloak class="text-xs text-rose-500 mt-1 ml-1 flex items-center gap-1">
                                        <i class="bx bx-error-circle"></i> Overlaps with a class schedule on <span x-text="row.day"></span>.
                                    </p>
                                </div>
                            </template>
                            <template x-if="hours.length === 0">
                                <p class="text-sm italic text-[#94a3b8]">No consultation hours added.</p>
                            </template>
                        </div>
                    </div>

                </div><!-- /grid -->
            </div><!-- /x-data lec -->

        </div>
    </x-layout.card>

    {{-- ══ Laboratory ═══════════════════════════════════════════════════════ --}}
    @if ($course->has_lec_lab)
        <x-layout.card title="Laboratory (LAB)" icon="test-tube" color="blue" class="mb-5">
            <div x-data="labSection(@js($lab_user_id ?? ''),
                @js($labUsers),
                @js($lab_schedules ?? []),
                @js($labConsultationHours ?? []))"
                x-on:lab-instructor-selected.window="onInstructorSelected($event.detail)"
                x-on:before-save-all.window="window._beforeSaveAllPromises.push(pushToWire())">
                <div class="space-y-5">

                    {{-- ── Instructor Selector ──────────────────────────────── --}}
                    <div class="px-4 py-3 rounded-xl border transition-colors"
                        :class="hasInstructor ? 'bg-blue-50/60 border-blue-200' : 'bg-amber-50 border-amber-200'">
                        <label class="block text-xs font-bold uppercase tracking-widest mb-1.5"
                            :class="hasInstructor ? 'text-blue-700' : 'text-amber-600'">
                            Laboratory Instructor
                            <span x-show="!hasInstructor" class="text-rose-500 normal-case font-medium">* Required</span>
                        </label>
                        <x-form.select x-model="selectedUserId" x-on:change="selectUser($event.target.value)"
                            class="max-w-xl">
                            <option value="">— Select a Laboratory Instructor —</option>
                            @if ($lecUser)
                                <option value="{{ $lecUser->id }}">{{ $lecUser->name }} — Same as LEC ({{ $lecUser->email }})</option>
                            @endif
                            @foreach ($labUsers as $u)
                                @if (!$lecUser || $u['id'] !== $lecUser->id)
                                    <option value="{{ $u['id'] }}">{{ $u['name'] }} ({{ $u['email'] }})</option>
                                @endif
                            @endforeach
                        </x-form.select>
                    </div>

                    {{-- ── Blocker ──────────────────────────────────────────── --}}
                    <div x-show="!hasInstructor" x-cloak
                        class="flex flex-col items-center justify-center py-12 text-center rounded-xl border border-dashed border-amber-200 bg-amber-50/50">
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center mb-4">
                            <i class="bx bx-user-circle text-2xl text-amber-500"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-700 mb-1">Select a Laboratory Instructor first</h3>
                        <p class="text-xs text-slate-500 max-w-xs leading-relaxed">
                            Choose an instructor from the dropdown above to fill in the LAB component details.
                        </p>
                    </div>

                    {{-- ── Instructor Profile ───────────────────────────────── --}}
                    <div x-show="hasInstructor" x-cloak>
                        <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3 flex items-center gap-2">
                            <span class="h-px w-4 bg-[#2563eb]"></span> Instructor Profile
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-form.label>Instructor Name</x-form.label>
                                <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]"
                                    x-text="labName || '—'"></p>
                            </div>
                            <div>
                                <x-form.label>Instructor Email</x-form.label>
                                <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]"
                                    x-text="labEmail || '—'"></p>
                            </div>
                            <div>
                                <x-form.label>Phone</x-form.label>
                                <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]"
                                    x-text="labPhone || '—'"></p>
                            </div>
                            <div>
                                <x-form.label>Office</x-form.label>
                                <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]"
                                    x-text="labOffice || '—'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- ── Class Delivery ───────────────────────────────────── --}}
                    <template x-if="hasInstructor">
                        <div>
                            <div class="border-t border-[#e2e8f0] mb-5"></div>
                            <p class="text-xs font-bold uppercase tracking-widest text-[#475569] mb-3 flex items-center gap-2">
                                <span class="h-px w-4 bg-[#2563eb]"></span> Class Delivery
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-form.label>Class Hours</x-form.label>
                                    <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">
                                        {{ $lab_class_hours ?? '—' }}</p>
                                    <p class="mt-1 text-xs text-[#94a3b8]">Set in course settings.</p>
                                </div>
                                <div>
                                    <x-form.label>Passing Mark <span
                                            class="text-[#94a3b8] font-normal normal-case tracking-normal">(LEC &amp; LAB)</span></x-form.label>
                                    <p class="mt-1 px-3 py-2 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569]">
                                        {{ $lec_performance_standard }}%</p>
                                    <p class="mt-1 text-xs text-[#94a3b8]">Set in course settings.</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- ── Schedule + Consultation ──────────────────────────── --}}
                    <div x-show="hasInstructor" x-cloak>
                        <div class="border-t border-[#e2e8f0] mb-5"></div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            {{-- Class Schedule --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs font-bold uppercase tracking-widest text-[#475569]">Class Schedule</p>
                                    <button type="button" x-on:click="schedules.push({ day: 'Monday', startTime: '', endTime: '' })"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                                               bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100 transition">
                                        <i class="bx bx-plus text-sm"></i> Add
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="(row, i) in schedules" :key="i">
                                        <div class="flex items-center gap-2" role="group" :aria-label="'LAB schedule row ' + (i + 1)">
                                            <x-form.select x-model="row.day" aria-label="Day">
                                                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $d)
                                                    <option value="{{ $d }}">{{ $d }}</option>
                                                @endforeach
                                            </x-form.select>
                                            <input type="time" x-model="row.startTime" aria-label="Start time"
                                                class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                                       focus:border-blue-400 focus:outline-none focus:bg-white" />
                                            <span class="text-xs text-slate-400 shrink-0">to</span>
                                            <input type="time" x-model="row.endTime" aria-label="End time"
                                                class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                                       focus:border-blue-400 focus:outline-none focus:bg-white" />
                                            <button type="button" x-on:click="schedules.splice(i, 1)"
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

                            {{-- Consultation Hours --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs font-bold uppercase tracking-widest text-[#475569]">Consultation Hours</p>
                                    <button type="button" x-on:click="hours.push({ day: 'Monday', startTime: '', endTime: '' })"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                                               bg-amber-50 text-amber-600 border border-amber-200 hover:bg-amber-100 transition">
                                        <i class="bx bx-plus text-sm"></i> Add
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="(row, i) in hours" :key="i">
                                        <div>
                                            <div class="flex items-center gap-2" role="group" :aria-label="'LAB consultation row ' + (i + 1)" :class="hasConflict(row) ? 'ring-1 ring-rose-300 rounded-lg p-1' : ''">
                                                <x-form.select x-model="row.day" aria-label="Day">
                                                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $d)
                                                        <option value="{{ $d }}">{{ $d }}</option>
                                                    @endforeach
                                                </x-form.select>
                                                <input type="time" x-model="row.startTime" aria-label="Start time"
                                                    class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                                           focus:border-amber-400 focus:outline-none focus:bg-white" />
                                                <span class="text-xs text-slate-400 shrink-0">to</span>
                                                <input type="time" x-model="row.endTime" aria-label="End time"
                                                    class="flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                                           focus:border-amber-400 focus:outline-none focus:bg-white" />
                                                <button type="button" x-on:click="hours.splice(i, 1)"
                                                    class="p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition">
                                                    <i class="bx bx-trash text-sm"></i>
                                                </button>
                                            </div>
                                            <p x-show="hasConflict(row)" x-cloak class="text-xs text-rose-500 mt-1 ml-1 flex items-center gap-1">
                                                <i class="bx bx-error-circle"></i> Overlaps with a class schedule on <span x-text="row.day"></span>.
                                            </p>
                                        </div>
                                    </template>
                                    <template x-if="hours.length === 0">
                                        <p class="text-sm italic text-[#94a3b8]">No consultation hours added.</p>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>{{-- /x-data labSection --}}
        </x-layout.card>
    @endif

    {{-- ── Sticky Save All footer ────────────────────────────────────────────── --}}
    <x-wizard.save-bar hint="Changes in both LEC and LAB sections are saved together.">
        <x-slot:action>
            <x-ui.button type="button" variant="save"
                x-bind:disabled="_saving"
                wire:loading.attr="disabled"
                wire:target="save"
                submitting="_saving" loadingText="Saving…"
                x-on:click="async () => {
                    _saving = true;
                    await $nextTick();
                    window._beforeSaveAllPromises = [];
                    window.dispatchEvent(new CustomEvent('before-save-all'));
                    try {
                        await Promise.all(window._beforeSaveAllPromises);
                    } catch { _saving = false; return; }
                    await $wire.save();
                    _saving = false;
                }">
                <i class="bx bx-save text-base leading-none"></i> Save All
            </x-ui.button>
        </x-slot:action>
    </x-wizard.save-bar>


</div>
