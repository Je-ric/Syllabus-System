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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
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
    @include('livewire.academic-calendar.partials.event-modal')
    @include('livewire.academic-calendar.partials.import-modal')

</div>
