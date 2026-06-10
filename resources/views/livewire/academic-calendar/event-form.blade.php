{{-- livewire/academic-calendar/event-form.blade.php --}}

<div
    x-data="{
        /* ── modal state ── */
        showForm:   false,
        showImport: false,

        /* ── form state ── */
        editingId: null,
        type:      'holiday',
        name:      '',
        dateStart: '',
        dateEnd:   '',
        saving:    false,

        /* ── calendar state ── */
        deletingId: null,

        /* ── helpers ── */
        get isRange() {
            return !this.editingId && this.dateEnd && this.dateEnd !== this.dateStart;
        },

        get rangeLabel() {
            if (!this.dateStart) return '';
            if (!this.dateEnd || this.dateEnd === this.dateStart) return this.fmt(this.dateStart);
            return this.fmt(this.dateStart) + ' – ' + this.fmt(this.dateEnd);
        },

        fmt(d) {
            if (!d) return '';
            const [y, m, day] = d.split('-');
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return months[+m - 1] + ' ' + +day;
        },

        /* Local-safe date iteration — avoids UTC-shift bug */
        localISO(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + d;
        },

        datesInRange() {
            if (!this.dateStart) return [];
            const end = this.dateEnd || this.dateStart;
            const dates = [];
            const s = new Date(this.dateStart + 'T00:00:00');
            const e = new Date(end + 'T00:00:00');
            for (let d = new Date(s); d <= e; d.setDate(d.getDate() + 1)) {
                dates.push(this.localISO(d));
            }
            return dates;
        },

        openAdd(prefillDate) {
            this.editingId = null;
            this.type      = 'holiday';
            this.name      = '';
            this.dateStart = prefillDate || '';
            this.dateEnd   = prefillDate || '';
            this.saving    = false;
            this.showForm  = true;
        },

        openEdit(detail) {
            this.editingId = detail.id;
            this.type      = detail.type  ?? 'holiday';
            this.name      = detail.name  ?? '';
            this.dateStart = detail.date  ?? '';
            this.dateEnd   = detail.date  ?? '';
            this.saving    = false;
            this.showForm  = true;
        },

        closeForm() {
            this.showForm  = false;
            this.editingId = null;
            this.dateStart = '';
            this.dateEnd   = '';
            this.name      = '';
            this.saving    = false;
        },

        async submit() {
            if (!this.name.trim() || !this.dateStart) return;
            if (this.dateEnd && this.dateEnd < this.dateStart) {
                [this.dateStart, this.dateEnd] = [this.dateEnd, this.dateStart];
            }
            this.saving = true;
            if (this.editingId) {
                await $wire.saveEvent(this.editingId, this.type, this.name, this.dateStart);
            } else {
                /* Single request for the whole range */
                await $wire.saveEventRange(this.type, this.name, this.dateStart, this.dateEnd || this.dateStart);
            }
            this.saving = false;
        },

        async remove(id) {
            this.deletingId = id;
            await $wire.deleteEvent(id);
            this.deletingId = null;
        }
    }"
    x-on:event-load-form.window="
        if ($event.detail.id) { openEdit($event.detail); }
        else { openAdd($event.detail.date); }
    "
    x-on:event-saved.window="closeForm(); showImport = false;">

    {{-- ══ CALENDAR VIEW ══════════════════════════════════════════════════════ --}}

    {{-- Top bar: legend + action buttons --}}
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">

        {{-- Legend --}}
        @php
            $typeVariantMap = [
                'holiday'      => ['bg' => 'bg-[#dcfce7]', 'text' => 'text-[#166534]', 'dot' => 'bg-[#16a34a]',  'bar' => '#16a34a'],
                'exam'         => ['bg' => 'bg-[#fef3c7]', 'text' => 'text-[#92400e]', 'dot' => 'bg-amber-400',  'bar' => '#f59e0b'],
                'break'        => ['bg' => 'bg-[#eff6ff]', 'text' => 'text-[#1e40af]', 'dot' => 'bg-blue-400',   'bar' => '#60a5fa'],
                'non_teaching' => ['bg' => 'bg-[#fff1f2]', 'text' => 'text-rose-700',  'dot' => 'bg-rose-400',   'bar' => '#fb7185'],
                'other'        => ['bg' => 'bg-[#f1f5f9]', 'text' => 'text-[#475569]', 'dot' => 'bg-slate-400',  'bar' => '#94a3b8'],
            ];
        @endphp
        <div class="flex flex-wrap gap-x-4 gap-y-1">
            @foreach(['holiday' => 'Holiday', 'exam' => 'Exam', 'break' => 'Break', 'non_teaching' => 'Non-Teaching', 'other' => 'Other'] as $type => $label)
                @php $v = $typeVariantMap[$type]; @endphp
                <span class="inline-flex items-center gap-1.5 text-[12px] {{ $v['text'] }}">
                    <span class="w-2 h-2 rounded-full {{ $v['dot'] }}"></span>
                    {{ $label }}
                </span>
            @endforeach
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center gap-2">
            <button type="button"
                x-on:click="showImport = true"
                class="inline-flex items-center gap-1.5 rounded-lg border border-[#e2e8f0] bg-white px-3 py-1.5
                       text-[12px] font-semibold text-[#475569] hover:bg-[#f8fafc] hover:border-[#cbd5e1]
                       transition-colors">
                <i class="bx bx-upload text-sm"></i>
                Import CSV
            </button>
            <button type="button"
                x-on:click="openAdd()"
                class="inline-flex items-center gap-1.5 rounded-lg bg-[#16a34a] px-3 py-1.5
                       text-[12px] font-semibold text-white hover:bg-[#15803d] transition-colors">
                <i class="bx bx-plus text-sm"></i>
                Add Event
            </button>
        </div>
    </div>

    {{-- Calendar months --}}
    @php
        $allEvents    = $semester?->events ?? collect();
        $eventsByDate = $allEvents->keyBy(fn($e) => \Carbon\Carbon::parse($e->date)->format('Y-m-d'));

        $rangePos = [];
        foreach ($eventsByDate as $dk => $ev) {
            $prev    = \Carbon\Carbon::parse($dk)->subDay()->format('Y-m-d');
            $next    = \Carbon\Carbon::parse($dk)->addDay()->format('Y-m-d');
            $hasPrev = isset($eventsByDate[$prev]) && $eventsByDate[$prev]->name === $ev->name && $eventsByDate[$prev]->type === $ev->type;
            $hasNext = isset($eventsByDate[$next]) && $eventsByDate[$next]->name === $ev->name && $eventsByDate[$next]->type === $ev->type;
            if ($hasPrev && $hasNext) $rangePos[$dk] = 'middle';
            elseif ($hasPrev)         $rangePos[$dk] = 'end';
            elseif ($hasNext)         $rangePos[$dk] = 'start';
            else                      $rangePos[$dk] = 'solo';
        }
    @endphp

    @if($semester)
        @php
            $start  = \Carbon\Carbon::parse($semester->start_date)->startOfMonth();
            $end    = \Carbon\Carbon::parse($semester->end_date)->endOfMonth();
            $months = [];
            $cursor = $start->copy();
            while ($cursor->lte($end)) { $months[] = $cursor->copy(); $cursor->addMonth(); }
        @endphp

        <div class="space-y-4">
            @foreach($months as $month)
                @php
                    $firstDay = $month->copy()->startOfMonth();
                    $lastDay  = $month->copy()->endOfMonth();
                    $semStart = \Carbon\Carbon::parse($semester->start_date);
                    $semEnd   = \Carbon\Carbon::parse($semester->end_date);
                    $startDow = $firstDay->dayOfWeek;
                @endphp

                <div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.07);">
                    <div class="px-4 py-2.5 bg-[#f8fafc] border-b border-[#e2e8f0]">
                        <p class="text-[13px] font-bold text-[#0f172a]">{{ $month->format('F Y') }}</p>
                    </div>

                    <div class="grid grid-cols-7 border-b border-[#e2e8f0]">
                        @foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $dow)
                            <div class="py-1.5 text-center text-[10px] font-bold uppercase tracking-wide text-[#94a3b8]">{{ $dow }}</div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-7">
                        @for($i = 0; $i < $startDow; $i++)
                            <div class="min-h-12 border-r border-b border-[#f1f5f9]"></div>
                        @endfor

                        @for($d = 1; $d <= $lastDay->day; $d++)
                            @php
                                $date    = $month->copy()->setDay($d);
                                $dateKey = $date->format('Y-m-d');
                                $event   = $eventsByDate[$dateKey] ?? null;
                                $inSem   = $date->between($semStart, $semEnd);
                                $isToday = $date->isToday();
                                $variant = $event ? ($typeVariantMap[$event->type] ?? $typeVariantMap['other']) : null;
                                $pos     = $rangePos[$dateKey] ?? 'solo';

                                $barStyle = 'position:absolute;bottom:6px;height:5px;';
                                if ($pos === 'solo')        $barStyle .= 'left:4px;right:4px;border-radius:3px;';
                                elseif ($pos === 'start')   $barStyle .= 'left:4px;right:-1px;border-radius:3px 0 0 3px;';
                                elseif ($pos === 'end')     $barStyle .= 'left:-1px;right:4px;border-radius:0 3px 3px 0;';
                                elseif ($pos === 'middle')  $barStyle .= 'left:-1px;right:-1px;border-radius:0;';
                            @endphp

                            <div
                                @if($event)
                                    x-on:click="openEdit({{ json_encode(['id' => $event->id, 'type' => $event->type, 'name' => $event->name, 'date' => $event->date]) }})"
                                    title="{{ $event->name }} ({{ ucfirst(str_replace('_',' ',$event->type)) }}) — click to edit"
                                @elseif($inSem)
                                    x-on:click="openAdd('{{ $dateKey }}')"
                                    title="Add event on {{ $date->format('M j, Y') }}"
                                @endif
                                class="group relative min-h-12 p-1 border-r border-b border-[#f1f5f9] transition-colors
                                    {{ !$inSem ? 'bg-[#f8fafc] opacity-40' : '' }}
                                    {{ $event
                                        ? $variant['bg'] . ' cursor-pointer hover:brightness-95'
                                        : ($inSem ? 'hover:bg-[#f0fdf4] cursor-pointer' : '') }}">

                                <span class="text-[11px] font-semibold leading-none
                                    {{ $isToday ? 'inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#16a34a] text-white' : '' }}
                                    {{ $event ? $variant['text'] : ($inSem ? 'text-[#0f172a]' : 'text-[#94a3b8]') }}">
                                    {{ $d }}
                                </span>

                                @if($event)
                                    <div style="{{ $barStyle }} background:{{ $variant['bar'] }}; opacity:0.55;"></div>

                                    @if($pos === 'solo' || $pos === 'start')
                                        <p class="mt-0.5 text-[9px] font-semibold leading-tight truncate {{ $variant['text'] }}">
                                            {{ $event->name }}
                                        </p>
                                    @endif

                                    <button
                                        type="button"
                                        x-on:click.stop="remove({{ $event->id }})"
                                        x-bind:disabled="deletingId === {{ $event->id }}"
                                        title="Delete {{ $event->name }}"
                                        class="absolute bottom-1 right-1
                                               hidden group-hover:inline-flex items-center justify-center
                                               w-5 h-5 rounded bg-white/80 hover:bg-rose-50
                                               border border-white hover:border-rose-200
                                               text-[#94a3b8] hover:text-rose-500
                                               transition-colors disabled:opacity-50 disabled:cursor-wait">
                                        <span x-show="deletingId !== {{ $event->id }}">
                                            <i class="bx bx-trash text-[10px] leading-none"></i>
                                        </span>
                                        <span x-show="deletingId === {{ $event->id }}" x-cloak>
                                            <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                        </span>
                                    </button>
                                @endif
                            </div>
                        @endfor

                        @php $trailing = (7 - (($startDow + $lastDay->day) % 7)) % 7; @endphp
                        @for($i = 0; $i < $trailing; $i++)
                            <div class="min-h-12 border-r border-b border-[#f1f5f9]"></div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <x-empty-state icon="bx-calendar" title="No semester data" message="" />
    @endif


    {{-- ══ MODAL: Add / Edit Event ═══════════════════════════════════════════ --}}
    <div
        x-show="showForm"
        x-cloak
        x-on:keydown.escape.window="closeForm()"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.45);">

        <div
            x-show="showForm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-on:click.outside="closeForm()"
            class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">

            {{-- Modal header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#e2e8f0] bg-[#f8fafc]">
                <div>
                    <p x-show="!editingId" class="text-[13px] font-bold text-[#0f172a]">Add Event</p>
                    <p x-show="editingId" x-cloak class="text-[13px] font-bold text-[#0f172a] inline-flex items-center gap-1.5">
                        <i class="bx bx-edit-alt"></i> Edit Event
                    </p>
                    @if($semester)
                        <p class="text-[11px] text-[#94a3b8] mt-0.5">
                            Semester: {{ \Carbon\Carbon::parse($semester->start_date)->format('M j') }}
                            – {{ \Carbon\Carbon::parse($semester->end_date)->format('M j, Y') }}
                        </p>
                    @endif
                </div>
                <button type="button" x-on:click="closeForm()"
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-[#94a3b8]
                           hover:bg-[#f1f5f9] hover:text-[#0f172a] transition-colors">
                    <i class="bx bx-x text-lg"></i>
                </button>
            </div>

            {{-- Modal body --}}
            <div class="p-5 flex flex-col gap-4">

                {{-- Type --}}
                <div>
                    <x-form.label isRequired>Type</x-form.label>
                    <select x-model="type"
                        class="mt-1.5 w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                               focus:border-[#16a34a] focus:outline-none transition-colors"
                        style="box-shadow:none"
                        onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                        onblur="this.style.boxShadow='none'">
                        <option value="holiday">Holiday</option>
                        <option value="exam">Exam</option>
                        <option value="break">Break</option>
                        <option value="non_teaching">Non-Teaching</option>
                        <option value="other">Other</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                            <i class="bx bx-error-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Date(s) --}}
                <div>
                    <x-form.label isRequired>
                        <span x-text="editingId ? 'Date' : 'Date Range'"></span>
                    </x-form.label>

                    <div class="mt-1.5 flex gap-3">
                        {{-- Start --}}
                        <div class="flex-1">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-[#94a3b8] mb-1"
                               x-show="!editingId">Start</p>
                            <input type="date" x-model="dateStart"
                                x-on:change="if (!editingId && (!dateEnd || dateEnd < dateStart)) dateEnd = dateStart"
                                min="{{ $semester?->start_date }}"
                                max="{{ $semester?->end_date }}"
                                class="w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                                       hover:border-[#bbf7d0] focus:border-[#16a34a] focus:outline-none transition-colors"
                                style="box-shadow:none"
                                onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                                onblur="this.style.boxShadow='none'" />
                        </div>

                        {{-- End — hidden when editing --}}
                        <template x-if="!editingId">
                            <div class="flex-1">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-[#94a3b8] mb-1">End</p>
                                <input type="date" x-model="dateEnd"
                                    :min="dateStart || '{{ $semester?->start_date }}'"
                                    max="{{ $semester?->end_date }}"
                                    class="w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                                           hover:border-[#bbf7d0] focus:border-[#16a34a] focus:outline-none transition-colors"
                                    style="box-shadow:none"
                                    onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                                    onblur="this.style.boxShadow='none'" />
                            </div>
                        </template>
                    </div>

                    {{-- Range pill --}}
                    <div x-show="isRange" x-cloak class="mt-2">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#166534] bg-[#dcfce7] rounded-full px-2.5 py-0.5">
                            <i class="bx bx-calendar-check text-xs"></i>
                            <span x-text="rangeLabel"></span>
                            <span class="text-[10px] font-normal text-[#16a34a]" x-text="'(' + datesInRange().length + ' days)'"></span>
                        </span>
                    </div>

                    <p x-show="saving && !dateStart" x-cloak class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> Date is required.
                    </p>
                    @error('date')
                        <p class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                            <i class="bx bx-error-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <x-form.label isRequired>Event Name</x-form.label>
                    <input x-model="name" type="text"
                        placeholder="e.g. Christmas Break"
                        class="mt-1.5 w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                               placeholder:text-[#94a3b8] hover:border-[#bbf7d0] focus:border-[#16a34a] focus:outline-none transition-colors"
                        style="box-shadow:none"
                        onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                        onblur="this.style.boxShadow='none'" />
                    <p x-show="saving && !name.trim()" x-cloak class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> Event name is required.
                    </p>
                    @error('name')
                        <p class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                            <i class="bx bx-error-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Modal footer --}}
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-[#e2e8f0] bg-[#f8fafc]">
                <button type="button" x-on:click="closeForm()"
                    class="px-4 py-2 rounded-lg text-[13px] font-semibold text-[#475569]
                           border border-[#e2e8f0] bg-white hover:bg-[#f1f5f9] transition-colors">
                    Cancel
                </button>
                <x-button type="button" variant="add-button"
                    x-on:click="submit()"
                    x-bind:disabled="saving">
                    <span x-show="!saving" class="inline-flex items-center gap-1.5">
                        <i x-show="editingId"  class="bx bx-save leading-none"></i>
                        <i x-show="!editingId" class="bx bx-plus leading-none"></i>
                        <span x-text="editingId ? 'Update Event' : (isRange ? 'Add ' + datesInRange().length + ' Events' : 'Add Event')"></span>
                    </span>
                    <span x-show="saving" x-cloak class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Saving…
                    </span>
                </x-button>
            </div>
        </div>
    </div>


    {{-- ══ MODAL: Import CSV ══════════════════════════════════════════════════ --}}
    <div
        x-show="showImport"
        x-cloak
        x-on:keydown.escape.window="showImport = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.45);">

        <div
            x-show="showImport"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-on:click.outside="showImport = false"
            class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#e2e8f0] bg-[#f8fafc]">
                <p class="text-[13px] font-bold text-[#0f172a] inline-flex items-center gap-1.5">
                    <i class="bx bx-upload text-sm"></i> Import Events from CSV
                </p>
                <button type="button" x-on:click="showImport = false"
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-[#94a3b8]
                           hover:bg-[#f1f5f9] hover:text-[#0f172a] transition-colors">
                    <i class="bx bx-x text-lg"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5">
                <div class="rounded-lg bg-[#f8fafc] border border-[#e2e8f0] p-4 mb-4 text-[12px] text-[#475569] space-y-1.5">
                    <p class="font-semibold text-[#0f172a]">CSV Format</p>
                    <p>Columns: <code class="bg-white border border-[#e2e8f0] rounded px-1">type, name, date</code></p>
                    <p>Date format: <code class="bg-white border border-[#e2e8f0] rounded px-1">YYYY-MM-DD</code></p>
                    <p>Types: <code class="bg-white border border-[#e2e8f0] rounded px-1">holiday</code>
                        <code class="bg-white border border-[#e2e8f0] rounded px-1">exam</code>
                        <code class="bg-white border border-[#e2e8f0] rounded px-1">break</code>
                        <code class="bg-white border border-[#e2e8f0] rounded px-1">non_teaching</code>
                        <code class="bg-white border border-[#e2e8f0] rounded px-1">other</code>
                    </p>
                </div>

                <input type="file" wire:model="csvFile" accept=".csv,.txt"
                    class="w-full text-[13px] text-[#475569]
                           file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                           file:text-[12px] file:font-semibold file:bg-[#dcfce7] file:text-[#166534]
                           hover:file:bg-[#bbf7d0] cursor-pointer" />

                @error('csvFile')
                    <p class="mt-2 text-[13px] text-rose-500 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-[#e2e8f0] bg-[#f8fafc]">
                <button type="button" x-on:click="showImport = false"
                    class="px-4 py-2 rounded-lg text-[13px] font-semibold text-[#475569]
                           border border-[#e2e8f0] bg-white hover:bg-[#f1f5f9] transition-colors">
                    Cancel
                </button>
                <x-button type="button" variant="add-button"
                    wire:click="importCsv"
                    wire:loading.attr="disabled"
                    wire:target="importCsv,csvFile">
                    <span wire:loading.remove wire:target="importCsv" class="inline-flex items-center gap-1.5">
                        <i class="bx bx-import text-sm"></i> Import
                    </span>
                    <span wire:loading wire:target="importCsv" class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Importing…
                    </span>
                </x-button>
            </div>
        </div>
    </div>

</div>
