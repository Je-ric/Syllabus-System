<div class="space-y-6 text-slate-800">
    @php
        $tabs = $semesters->map(fn($s) => [
                            'id' => (string) $s->id,
                            'label' => $s->semester .
                            ' Semester']
                        )->toArray();
    @endphp

    <x-navigation.tabs-modern :tabs="$tabs" :defaultTab="$tabs[0]['id'] ?? null" :stateKey="'academic-calendar-events-livewire:' . $academicYear">
        @foreach($semesters as $semester)
            @slot('slot_' . $semester->id)
                @php
                    $preview = $this->weeksPreview[$semester->id] ?? null;
                @endphp
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl space-y-4 shadow-sm">
                        <h2 class="font-semibold text-slate-800">Add Event</h2>
                        <div class="text-xs rounded-lg border border-blue-200 bg-blue-50 text-blue-700 px-3 py-2">
                            Weeks preview: {{ $preview['total_weeks'] ?? 0 }} week(s), {{ $preview['days'] ?? 0 }} day(s)
                            ({{ $preview['start'] ?? '-' }} to {{ $preview['end'] ?? '-' }})
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Type</label>
                                <select wire:model.live="newEvent.{{ $semester->id }}.type"
                                        class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                                    <option value="holiday">Holiday</option>
                                    <option value="exam">Exam</option>
                                    <option value="break">Break</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Date</label>
                                <input type="date"
                                        wire:model.live="newEvent.{{ $semester->id }}.date"
                                        min="{{ $semester->start_date->toDateString() }}"
                                        max="{{ $semester->end_date->toDateString() }}"
                                        class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                            </div>
                        </div>

                        @if(($dateConflicts['new'][$semester->id] ?? false) === true)
                            <div class="text-xs text-rose-600">Date conflict: an event already exists on this date.</div>
                        @endif

                        <div>
                            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Name</label>
                            <input type="text"
                                    wire:model.live.debounce.250ms="newEvent.{{ $semester->id }}.name"
                                    class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                        </div>

                        <button type="button"
                                wire:click="addEvent({{ $semester->id }})"
                                class="mt-2 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                            <i class="bx bx-plus"></i> Add Event
                        </button>
                    </div>

                    <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl shadow-sm">
                        <h2 class="font-semibold text-slate-800 mb-2">Events for {{ $semester->semester }} Semester</h2>
                        <p class="text-slate-500 text-sm mb-3">
                            Semester Range:
                            {{ $semester->start_date->format('F j, Y') }} - {{ $semester->end_date->format('F j, Y') }}
                        </p>

                        <table class="w-full border-collapse border border-slate-200 text-sm">
                            <thead>
                                <tr class="bg-emerald-50 text-emerald-800">
                                    <th class="border border-slate-200 px-3 py-2 text-left text-xs uppercase tracking-[0.2em]">Date</th>
                                    <th class="border border-slate-200 px-3 py-2 text-left text-xs uppercase tracking-[0.2em]">Type</th>
                                    <th class="border border-slate-200 px-3 py-2 text-left text-xs uppercase tracking-[0.2em]">Name</th>
                                    <th class="border border-slate-200 px-3 py-2 text-left text-xs uppercase tracking-[0.2em]">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($semester->events as $event)
                                    @php $isEditing = isset($editing[$event->id]); @endphp
                                    <tr class="odd:bg-white even:bg-slate-50 hover:bg-emerald-50/60 transition">
                                        <td class="border border-slate-200 px-3 py-2">
                                            @if($isEditing)
                                                <input type="date"
                                                        wire:model.live="editing.{{ $event->id }}.date"
                                                        min="{{ $semester->start_date->toDateString() }}"
                                                        max="{{ $semester->end_date->toDateString() }}"
                                                        class="w-full rounded-lg border border-slate-300 px-2 py-1 text-xs">
                                            @else
                                                {{ \Carbon\Carbon::parse($event->date)->format('F j, Y') }}
                                            @endif
                                        </td>
                                        <td class="border border-slate-200 px-3 py-2">
                                            @if($isEditing)
                                                <select wire:model.live="editing.{{ $event->id }}.type"
                                                        class="w-full rounded-lg border border-slate-300 px-2 py-1 text-xs">
                                                    <option value="holiday">Holiday</option>
                                                    <option value="exam">Exam</option>
                                                    <option value="break">Break</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            @else
                                                {{ ucfirst($event->type) }}
                                            @endif
                                        </td>
                                        <td class="border border-slate-200 px-3 py-2">
                                            @if($isEditing)
                                                <input type="text"
                                                        wire:model.live.debounce.250ms="editing.{{ $event->id }}.name"
                                                        class="w-full rounded-lg border border-slate-300 px-2 py-1 text-xs">
                                            @else
                                                {{ $event->name }}
                                            @endif
                                            @if(($dateConflicts['edit'][$event->id] ?? false) === true)
                                                <div class="text-[11px] text-rose-600 mt-1">Date conflict</div>
                                            @endif
                                        </td>
                                        <td class="border border-slate-200 px-3 py-2">
                                            <div class="flex gap-2">
                                                @if($isEditing)
                                                    <button type="button" wire:click="saveEdit({{ $event->id }})" class="text-emerald-700 hover:text-emerald-900">
                                                        <i class="bx bx-check"></i>
                                                    </button>
                                                    <button type="button" wire:click="cancelEdit({{ $event->id }})" class="text-slate-600 hover:text-slate-800">
                                                        <i class="bx bx-x"></i>
                                                    </button>
                                                @else
                                                    <button type="button" wire:click="startEdit({{ $event->id }})" class="text-emerald-700 hover:text-emerald-900">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                    <button type="button" wire:click="deleteEvent({{ $event->id }})" class="text-rose-600 hover:text-rose-800">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="border border-slate-200 px-3 py-2 text-center text-slate-500" colspan="4">No events yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endslot
        @endforeach
    </x-navigation.tabs-modern>
</div>

