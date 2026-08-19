{{-- livewire/academic-calendar/event-form.blade.php --}}

<div
    x-data="{
        /* ── modal state ── */
        showForm:   false,

        /* ── form state ── */
        editingId: null,
        type:      'holiday',
        name:      '',
        dateStart: '',
        dateEnd:   '',
        saving:    false,
        hasAttempted: false,
        _fpSyncing: false,
        _userSelecting: false,

        /* ── calendar state ── */
        deletingIds: [],   // array now — supports multiple concurrent deletes safely
        isDeleting(id) { return this.deletingIds.includes(id); },

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
            this.hasAttempted = false;
            this.showForm  = true;

            // Reinitialize Flatpickr when opening the modal
            $nextTick(() => {
                this.initFlatpickr();
            });
        },

        initFlatpickr() {
            const semMin = '{{ $semester?->start_date ?? '' }}';
            const semMax = '{{ $semester?->end_date ?? '' }}';
            const semId  = '{{ $semesterId }}';

            const fpConfig = {
                dateFormat : 'Y-m-d',
                altInput   : true,
                altFormat  : 'F j, Y',
                allowInput : false,
                minDate    : semMin || null,
                maxDate    : semMax || null,
            };

            const fpStartKey = '_evFpStart_' + semId;
            const fpEndKey   = '_evFpEnd_'   + semId;

            // Only destroy and reinitialize if instances don't exist or are broken
            if (!window[fpStartKey] || !window[fpStartKey].input) {
                if (window[fpStartKey]) {
                    window[fpStartKey].destroy();
                }
                if (window[fpEndKey]) {
                    window[fpEndKey].destroy();
                }

                // Store component reference for callbacks
                const component = this;

                window[fpStartKey] = flatpickr('#ev-date-start-' + semId, {
                    ...fpConfig,
                    onChange([date], dateStr) {
                        component._userSelecting = true;
                        component._fpSyncing = true;
                        component.dateStart = dateStr;
                        if (!component.editingId && (!component.dateEnd || component.dateEnd < dateStr)) {
                            component.dateEnd = dateStr;
                            window[fpEndKey]?.setDate(dateStr, false);
                        }
                        window[fpEndKey]?.set('minDate', dateStr);
                        component._fpSyncing = false;
                        setTimeout(() => { component._userSelecting = false; }, 100);
                    },
                    onClose(selectedDates, dateStr, instance) {
                        // Ensure dateStart is updated when Flatpickr closes
                        if (dateStr) {
                            component._userSelecting = true;
                            component.dateStart = dateStr;
                            setTimeout(() => { component._userSelecting = false; }, 100);
                        }
                    }
                });

                window[fpEndKey] = flatpickr('#ev-date-end-' + semId, {
                    ...fpConfig,
                    onChange([date], dateStr) {
                        component._fpSyncing = true;
                        component.dateEnd = dateStr;
                        component._fpSyncing = false;
                    }
                });

                $watch('dateStart', val => {
                    if (component._fpSyncing || component._userSelecting) return;
                    val ? window[fpStartKey]?.setDate(val, false)
                        : window[fpStartKey]?.clear();
                });
                $watch('dateEnd', val => {
                    if (component._fpSyncing || component._userSelecting) return;
                    val ? window[fpEndKey]?.setDate(val, false)
                        : window[fpEndKey]?.clear();
                });
            } else {
                // If instances exist, just update the date if needed
                if (this.dateStart) {
                    window[fpStartKey].setDate(this.dateStart, false);
                }
                if (this.dateEnd) {
                    window[fpEndKey].setDate(this.dateEnd, false);
                }
            }
        },

        openEdit(detail) {
            this.editingId = detail.id;
            this.type      = detail.type  ?? 'holiday';
            this.name      = detail.name  ?? '';
            this.dateStart = detail.date  ?? '';
            this.dateEnd   = detail.date  ?? '';
            this.saving    = false;
            this.hasAttempted = false;
            this.showForm  = true;

            // Reinitialize Flatpickr when opening the modal and set the date
            $nextTick(() => {
                this.initFlatpickr();
                const semId = '{{ $semesterId }}';
                const fpStartKey = '_evFpStart_' + semId;
                if (window[fpStartKey] && this.dateStart) {
                    window[fpStartKey].setDate(this.dateStart, false);
                }
            });
        },

        closeForm() {
            this.showForm  = false;
            this.editingId = null;
            this.dateStart = '';
            this.dateEnd   = '';
            this.name      = '';
            this.saving    = false;
            this.hasAttempted = false;

            // Destroy Flatpickr instances when closing modal
            const semId = '{{ $semesterId }}';
            const fpStartKey = '_evFpStart_' + semId;
            const fpEndKey = '_evFpEnd_' + semId;
            if (window[fpStartKey]) {
                window[fpStartKey].destroy();
                window[fpStartKey] = null;
            }
            if (window[fpEndKey]) {
                window[fpEndKey].destroy();
                window[fpEndKey] = null;
            }
        },

        async submit() {
            this.hasAttempted = true;
            if (!this.name.trim() || !this.dateStart) return;
            if (this.dateEnd && this.dateEnd < this.dateStart) {
                [this.dateStart, this.dateEnd] = [this.dateEnd, this.dateStart];
            }

            // Get the actual value from Flatpickr to ensure we have the latest selection
            const semId = '{{ $semesterId }}';
            const fpStartKey = '_evFpStart_' + semId;
            const actualDate = window[fpStartKey]?.selectedDates[0];
            if (actualDate) {
                const year = actualDate.getFullYear();
                const month = String(actualDate.getMonth() + 1).padStart(2, '0');
                const day = String(actualDate.getDate()).padStart(2, '0');
                this.dateStart = `${year}-${month}-${day}`;
            }

            this.saving = true;
            let success = false;
            if (this.editingId) {
                success = await $wire.saveEvent(this.editingId, this.type, this.name, this.dateStart);
            } else {
                success = await $wire.saveEventRange(this.type, this.name, this.dateStart, this.dateEnd || this.dateStart);
            }
            this.saving = false;

            // Close modal on success or validation error
            if (success) {
                this.closeForm();
            } else {
                // Close modal on validation error - toast will be shown by Livewire
                this.closeForm();
            }

            return success;
        },

        async remove(id) {
            if (this.isDeleting(id)) return; // guard: already in flight, ignore repeat clicks
            this.deletingIds.push(id);
            await $wire.deleteEvent(id);
            this.deletingIds = this.deletingIds.filter(x => x !== id);
        }
    }"
    x-on:event-load-form.window="
        if ($event.detail.id) { openEdit($event.detail); }
        else { openAdd($event.detail.date); }
    "
    x-on:event-saved.window="closeForm();"
    x-on:reload-page.window="setTimeout(() => window.location.reload(), 500);"
    x-init="
        // ── Initialize Flatpickr for event modal ────────────────────────────────────────
        $nextTick(() => {
            this.initFlatpickr();
        });

        // Watch for modal visibility changes to reinitialize Flatpickr
        $watch('showForm', (val) => {
            if (val) {
                $nextTick(() => {
                    this.initFlatpickr();
                });
            }
        });
    ">

    {{-- ══ CALENDAR VIEW ══════════════════════════════════════════════════════ --}}

    {{-- Top bar: legend + action buttons --}}
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">

        {{-- Legend --}}
        @php
            $typeVariantMap = [
                'holiday'      => ['bg' => 'bg-[#dcfce7]', 'text' => 'text-[#166534]', 'dot' => 'bg-[#16a34a]',  'bar' => '#16a34a', 'icon' => 'bx-info-circle'],
                'exam'         => ['bg' => 'bg-[#fef3c7]', 'text' => 'text-[#92400e]', 'dot' => 'bg-amber-400',  'bar' => '#f59e0b', 'icon' => 'bx-lock'],
                'break'        => ['bg' => 'bg-[#eff6ff]', 'text' => 'text-[#1e40af]', 'dot' => 'bg-blue-400',   'bar' => '#60a5fa', 'icon' => 'bx-skip-next'],
                'non_teaching' => ['bg' => 'bg-[#fff1f2]', 'text' => 'text-rose-700',  'dot' => 'bg-rose-400',   'bar' => '#fb7185', 'icon' => 'bx-lock'],
                'other'        => ['bg' => 'bg-[#f1f5f9]', 'text' => 'text-[#475569]', 'dot' => 'bg-slate-400',  'bar' => '#94a3b8', 'icon' => 'bx-info-circle'],
            ];
        @endphp
        <div class="flex flex-wrap gap-x-4 gap-y-1">
            @foreach(['holiday' => 'Holiday (Ref)', 'exam' => 'Exam (Lock)', 'break' => 'Break (Skip)', 'non_teaching' => 'Non-Teaching (Lock)', 'other' => 'Other (Ref)'] as $type => $label)
                @php $v = $typeVariantMap[$type]; @endphp
                <span class="inline-flex items-center gap-1.5 text-xs {{ $v['text'] }}">
                    <i class="bx {{ $v['icon'] }} text-xs"></i>
                    {{ $label }}
                </span>
            @endforeach
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center gap-2">
            {{--
            <button type="button"
                x-on:click="showImport = true"
                class="inline-flex items-center gap-1.5 rounded-lg border border-[#e2e8f0] bg-white px-3 py-1.5
                       text-xs font-semibold text-[#475569] hover:bg-[#f8fafc] hover:border-[#cbd5e1]
                       transition-colors">
                <i class="bx bx-upload text-sm"></i>
                Import CSV
            </button>
            --}}
            <button type="button"
                x-on:click="openAdd()"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5
                       text-xs font-semibold text-white transition-colors"
                style="background: var(--clsu-green);"
                onmouseover="this.style.background='var(--clsu-cobra)'"
                onmouseout="this.style.background='var(--clsu-green)'">
                <i class="bx bx-plus text-sm"></i>
                Add Event
            </button>
        </div>
    </div>

    {{-- Calendar months — VIEW & DELETE ONLY. Adding is via the "Add Event" button/modal above. --}}
    @php
        $semester     = $this->semester;
        $allEvents    = $semester?->events ?? collect();
        $eventsByDate = $allEvents->groupBy(fn($e) => \Carbon\Carbon::parse($e->date)->format('Y-m-d'));

        $rangePos = [];
        foreach ($eventsByDate as $dk => $dayGroup) {
            $ev      = $dayGroup->first();
            $prev    = \Carbon\Carbon::parse($dk)->subDay()->format('Y-m-d');
            $next    = \Carbon\Carbon::parse($dk)->addDay()->format('Y-m-d');
            $prevEv  = $eventsByDate->get($prev)?->first();
            $nextEv  = $eventsByDate->get($next)?->first();
            $hasPrev = $prevEv && $prevEv->name === $ev->name && $prevEv->type === $ev->type;
            $hasNext = $nextEv && $nextEv->name === $ev->name && $nextEv->type === $ev->type;
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

        <div class="relative">
            {{-- Loading overlay — shown while any Livewire action is in flight --}}
            <div wire:loading class="absolute inset-0 z-10 bg-white/60 flex items-center justify-center rounded-xl">
                <svg class="animate-spin h-6 w-6 text-green-700" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" wire:loading.class="opacity-50 pointer-events-none">
            @foreach($months as $month)
                @php
                    $firstDay = $month->copy()->startOfMonth();
                    $lastDay  = $month->copy()->endOfMonth();
                    $semStart = \Carbon\Carbon::parse($semester->start_date);
                    $semEnd   = \Carbon\Carbon::parse($semester->end_date);
                    $startDow = $firstDay->dayOfWeek;
                @endphp

                <div class="rounded-xl border-t-2 border-green-700 bg-white overflow-hidden shadow-lg">
                    <div class="px-4 py-2.5 bg-green-50 border-b border-[#e2e8f0]">
                        <p class="text-sm font-bold text-[#0f172a]">{{ $month->format('F Y') }}</p>
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
                                $dayEvents = $eventsByDate[$dateKey] ?? collect();
                                $event     = $dayEvents->first();     // first event for display
                                $hasConflict = $dayEvents->count() > 1; // flag for badge
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

                            {{--
                                VIEW / DELETE ONLY:
                                - If a day has an event, clicking it opens the read-only/edit-in-modal view (still allowed to view details).
                                - Empty days are NOT clickable anymore — no more click-to-add-on-empty-day.
                                  Adding only happens via the "Add Event" button at the top, which opens the modal.
                            --}}
                            <div
                                @if($event)
                                    x-on:click="openEdit({{ json_encode(['id' => $event->id, 'type' => $event->type, 'name' => $event->name, 'date' => $event->date]) }})"
                                    title="{{ $event->name }} ({{ ucfirst(str_replace('_',' ',$event->type)) }}) — click to view"
                                @endif
                                wire:key="cal-cell-{{ $dateKey }}"
                                class="group relative min-h-12 p-1 border-r border-b border-[#f1f5f9] transition-colors
                                    {{ !$inSem ? 'bg-[#f8fafc] opacity-40' : '' }}
                                    {{ $event ? $variant['bg'] . ' cursor-pointer hover:brightness-95' : '' }}">

                                <span class="text-xs font-semibold leading-none
                                    {{ $isToday ? 'inline-flex items-center justify-center w-5 h-5 rounded-full text-white' : '' }}
                                    {{ $event ? $variant['text'] : ($inSem ? 'text-[#0f172a]' : 'text-[#94a3b8]') }}"
                                    @if($isToday) style="background: var(--clsu-green);" @endif>
                                    {{ $d }}
                                </span>

                                @if($event)
                                    <div style="{{ $barStyle }} background:{{ $variant['bar'] }}; opacity:0.55;"></div>

                                    @if($pos === 'solo' || $pos === 'start')
                                        <p class="mt-0.5 text-[9px] font-semibold leading-tight truncate {{ $variant['text'] }}">
                                            {{ $event->name }}
                                        </p>
                                        @if($hasConflict)
                                            <p class="mt-0.5 text-[8px] font-bold leading-tight text-rose-600 truncate">
                                                +{{ $dayEvents->count() - 1 }} more
                                            </p>
                                        @endif
                                    @endif

                                    <button
                                        type="button"
                                        x-on:click.stop="remove({{ $event->id }})"
                                        x-bind:disabled="isDeleting({{ $event->id }})"
                                        title="Delete {{ $event->name }}"
                                        class="absolute bottom-1 right-1
                                               hidden group-hover:inline-flex items-center justify-center
                                               w-5 h-5 rounded bg-white/80 hover:bg-rose-50
                                               border border-white hover:border-rose-200
                                               text-[#94a3b8] hover:text-rose-500
                                               transition-colors disabled:opacity-50 disabled:cursor-wait">
                                        <span x-show="!isDeleting({{ $event->id }})">
                                            <i class="bx bx-trash text-[10px] leading-none"></i>
                                        </span>
                                        <span x-show="isDeleting({{ $event->id }})" x-cloak>
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
        </div>{{-- end grid --}}
        </div>{{-- end relative wrapper --}}
    @else
        <x-feedback-status.empty-state icon="bx-calendar" title="No semester data" message="" />
    @endif

    @include('livewire.academic-calendar.partials.event-modal')

    {{-- Flash message: shown while saving or deleting --}}
    <div x-data="{ get isSaving() { return saving; }, get deletingId() { return deletingIds.length > 0; } }">
        @include('livewire.programs.include.flash-message')
    </div>

</div>
