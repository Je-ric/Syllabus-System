<div
    x-data="{ _pushing: false }"
    x-on:request-push-and-navigate.window="
        if (_pushing) return;
        _pushing = true;
        window._beforeSaveAllPromises = [];
        window.dispatchEvent(new CustomEvent('before-save-all'));
        await Promise.all(window._beforeSaveAllPromises);
        await $wire.onPushAndNavigate($event.detail.toStep);
        _pushing = false;
    ">
    <x-wizard.step-header title="Course Components"
        description="Fill in instructor details and class delivery info for each component." />

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
                days: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
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
                        return; // don't push — Save All / Next will still proceed for other sections, but this one's data stays unsaved server-side
                    }
                    await $wire.pushLecSchedules(this.schedules.map(s => ({ day: s.day, time: formatTime(s.startTime, s.endTime) })));
                    await $wire.pushConsultationHours(this.hours.map(h => ({ day: h.day, time: formatTime(h.startTime, h.endTime) })));
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
    </x-wizard.section>

    {{-- ══ Laboratory ═══════════════════════════════════════════════════════ --}}
    @if ($course->has_lec_lab)
        <x-wizard.section title="Laboratory (LAB)" icon="test-tube" color="blue">
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
                        <select x-model="selectedUserId" x-on:change="selectUser($event.target.value)"
                            class="w-full max-w-xl rounded-lg border px-3 py-2 text-sm text-slate-700 transition-colors"
                            :class="hasInstructor
                                ? 'border-blue-200 bg-white focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100'
                                : 'border-amber-300 bg-amber-50 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-100'">
                            <option value="">— Select a Laboratory Instructor —</option>
                            @foreach ($labUsers as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }} ({{ $u['email'] }})</option>
                            @endforeach
                        </select>
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
        </x-wizard.section>
    @endif

    {{-- ── Sticky Save All footer ────────────────────────────────────────────── --}}
    <div class="sticky bottom-0 z-10 mt-4 flex items-center justify-between gap-4 px-5 py-3
                rounded-xl border border-[#dedee2] bg-white/95 backdrop-blur-sm"
         style="box-shadow: 0 -2px 16px rgba(0,0,0,.10);">
        <p class="text-xs text-slate-400 hidden sm:block">
            Changes in both LEC and LAB sections are saved together.
        </p>
        <x-button variant="sm-add"
            x-on:click="
                window._beforeSaveAllPromises = [];
                window.dispatchEvent(new CustomEvent('before-save-all'));
                await Promise.all(window._beforeSaveAllPromises);
                await $wire.save();
            "
            wireTarget="save" loading="Saving…">
            <i class="bx bx-save"></i> Save All
        </x-button>
    </div>

    <script>
        function parseTime(timeStr) {
            if (!timeStr) return { startTime: '', endTime: '' };
            const parts = timeStr.split(' - ');
            if (parts.length !== 2) return { startTime: '', endTime: '' };
            const toInput = (t) => {
                const m = t.trim().match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
                if (!m) return '';
                let h = parseInt(m[1]), min = m[2], period = m[3].toUpperCase();
                if (period === 'PM' && h !== 12) h += 12;
                if (period === 'AM' && h === 12) h = 0;
                return String(h).padStart(2, '0') + ':' + min;
            };
            return { startTime: toInput(parts[0]), endTime: toInput(parts[1]) };
        }

        function formatTime(start, end) {
            if (!start || !end) return '';
            const fmt = (t) => {
                const [h, m] = t.split(':').map(Number);
                const period = h >= 12 ? 'PM' : 'AM';
                const h12 = h % 12 || 12;
                return h12 + ':' + String(m).padStart(2, '0') + ' ' + period;
            };
            return fmt(start) + ' - ' + fmt(end);
        }

        function labSection(initUserId, initUsers, initSchedules, initHours) {
            const userMap = {};
            (initUsers || []).forEach(u => { userMap[u.id] = u; });

            const initUser = initUserId ? userMap[initUserId] : null;

            return {
                selectedUserId: initUserId || '',
                labName: initUser?.name || '',
                labEmail: initUser?.email || '',
                labPhone: initUser?.phone_number || '',
                labOffice: initUser?.office || '',
                schedules: (initSchedules || []).map(s => ({ day: s.day, ...parseTime(s.time) })),
                hours: (initHours || []).map(h => ({ day: h.day, ...parseTime(h.time) })),
                days: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],

                get hasInstructor() {
                    return this.selectedUserId !== '' && this.selectedUserId !== null && this.selectedUserId !== undefined;
                },

                async selectUser(id) {
                    this.selectedUserId = id;
                    if (!id) {
                        await this.$wire.clearLabInstructor();
                        return;
                    }
                    await this.$wire.selectLabInstructor(parseInt(id));
                },

                onInstructorSelected(detail) {
                    const d = Array.isArray(detail) ? detail[0] : detail;
                    this.labName = d?.name || '';
                    this.labEmail = d?.email || '';
                    this.labPhone = d?.phone || '';
                    this.labOffice = d?.office || '';
                    this.hours = (d?.consultationHours || []).map(h => ({ day: h.day, ...parseTime(h.time) }));
                },

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
                            detail: { type: 'error', message: 'Fix overlapping consultation hours (LAB) before saving.' }
                        }));
                        return;
                    }
                    await this.$wire.pushLabSchedules(
                        this.schedules.map(s => ({ day: s.day, time: formatTime(s.startTime, s.endTime) }))
                    );
                    await this.$wire.pushLabConsultationHours(
                        this.hours.map(h => ({ day: h.day, time: formatTime(h.startTime, h.endTime) }))
                    );
                },
            };
        }
    </script>

</div>
