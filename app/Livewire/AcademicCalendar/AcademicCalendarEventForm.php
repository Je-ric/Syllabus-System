<?php

namespace App\Livewire\AcademicCalendar;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class AcademicCalendarEventForm extends Component
{
    use WithFileUploads;

    public int    $semesterId;
    public string $academicYear = '';
    public mixed  $csvFile      = null;

    public function mount(int $semesterId, string $academicYear): void
    {
        $this->semesterId   = $semesterId;
        $this->academicYear = $academicYear;
    }

    public function render()
    {
        $semester = AcademicCalendar::with(['events' => fn($q) => $q->orderBy('date')])
            ->find($this->semesterId);

        return view('livewire.academic-calendar.event-form', compact('semester'));
    }

    /**
     * Bulk-insert events for a date range in a single DB round-trip.
     * Dates that already have an event are silently skipped.
     */
    public function saveEventRange(string $type, string $name, string $dateStart, string $dateEnd): void
    {
        $semester = AcademicCalendar::findOrFail($this->semesterId);

        $validator = Validator::make(
            compact('type', 'name', 'dateStart', 'dateEnd'),
            [
                'type'      => ['required', Rule::in(AcademicCalendarEvent::TYPES)],
                'name'      => ['required', 'string', 'max:255'],
                'dateStart' => ['required', 'date', 'after_or_equal:' . $semester->start_date, 'before_or_equal:' . $semester->end_date],
                'dateEnd'   => ['required', 'date', 'after_or_equal:dateStart', 'before_or_equal:' . $semester->end_date],
            ]
        );

        if ($validator->fails()) {
            $this->dispatch('lw-toast', type: 'error', message: $validator->errors()->first());
            return;
        }

        // Fetch already-occupied dates in the range so we can skip them
        $existing = $semester->events()
            ->whereBetween('date', [$dateStart, $dateEnd])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->flip()
            ->all();

        $rows   = [];
        $now    = now();
        $cursor = Carbon::parse($dateStart);
        $end    = Carbon::parse($dateEnd);

        while ($cursor->lte($end)) {
            $dk = $cursor->format('Y-m-d');
            if (!isset($existing[$dk])) {
                $rows[] = [
                    'academic_calendar_id' => $this->semesterId,
                    'type'       => $type,
                    'name'       => $name,
                    'date'       => $dk,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            $cursor->addDay();
        }

        $inserted = count($rows);

        if ($inserted > 0) {
            AcademicCalendarEvent::insert($rows);
        }

        AuditLog::record(
            action: 'created',
            module: 'Academic Calendar Event',
            referenceId: $this->semesterId,
            description: "Created {$type} event '{$name}' from {$dateStart} to {$dateEnd} ({$inserted} days) for {$this->academicYear} {$semester->semester} semester."
        );

        $this->dispatch('event-saved');
        $this->dispatch('lw-toast', type: 'success', message: "{$inserted} event(s) added.");
    }

    /**
     * Handles both ADD (editingId = null) and UPDATE (editingId = int).
     * Called from Alpine for single-date add or edit.
     */
    public function saveEvent(?int $editingId, string $type, string $name, string $date): void
    {
        $semester = AcademicCalendar::findOrFail($this->semesterId);

        $dateUnique = Rule::unique('academic_calendar_events', 'date')
            ->where('academic_calendar_id', $this->semesterId);

        if ($editingId) {
            $dateUnique->ignore($editingId);
        }

        $validator = Validator::make(compact('type', 'name', 'date'), [
            'type' => ['required', Rule::in(AcademicCalendarEvent::TYPES)],
            'name' => ['required', 'string', 'max:255'],
            'date' => [
                'required', 'date',
                'after_or_equal:' . $semester->start_date,
                'before_or_equal:' . $semester->end_date,
                $dateUnique,
            ],
        ], [
            'type.required'        => 'Event type is required.',
            'type.in'              => 'Invalid event type.',
            'name.required'        => 'Event name is required.',
            'name.max'             => 'Event name must be under 255 characters.',
            'date.required'        => 'Date is required.',
            'date.after_or_equal'  => 'Date must be within the semester range.',
            'date.before_or_equal' => 'Date must be within the semester range.',
            'date.unique'          => 'An event already exists on this date.',
        ]);

        if ($validator->fails()) {
            $this->dispatch('lw-toast', type: 'error', message: $validator->errors()->first());
            return;
        }

        $validated = $validator->validated();

        if ($editingId) {
            $event = AcademicCalendarEvent::findOrFail($editingId);
            $event->update($validated);

            AuditLog::record(
                action: 'updated',
                module: 'Academic Calendar Event',
                referenceId: $event->id,
                description: "Updated {$event->type} event '{$event->name}' on {$event->date} for {$this->academicYear} {$semester->semester} semester."
            );

            $this->dispatch('event-saved');
            $this->dispatch('lw-toast', type: 'success', message: 'Event updated successfully.');
        } else {
            $event = $semester->events()->create($validated);

            AuditLog::record(
                action: 'created',
                module: 'Academic Calendar Event',
                referenceId: $event->id,
                description: "Created {$event->type} event '{$event->name}' on {$event->date} for {$this->academicYear} {$semester->semester} semester."
            );

            $this->dispatch('event-saved');
            $this->dispatch('lw-toast', type: 'success', message: 'Event added successfully.');
        }
    }

    public function importCsv(): void
    {
        $this->validate(['csvFile' => ['required', 'file', 'mimes:csv,txt', 'max:512']]);

        $semester = AcademicCalendar::findOrFail($this->semesterId);
        $handle   = fopen($this->csvFile->getRealPath(), 'r');
        $header   = fgetcsv($handle); // skip header row

        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) { $skipped++; continue; }

            [$type, $name, $date] = array_map('trim', $row);

            $v = Validator::make(
                compact('type', 'name', 'date'),
                [
                    'type' => ['required', Rule::in(AcademicCalendarEvent::TYPES)],
                    'name' => ['required', 'string', 'max:255'],
                    'date' => [
                        'required', 'date',
                        'after_or_equal:' . $semester->start_date,
                        'before_or_equal:' . $semester->end_date,
                        Rule::unique('academic_calendar_events', 'date')
                            ->where('academic_calendar_id', $this->semesterId),
                    ],
                ]
            );

            if ($v->fails()) { $skipped++; continue; }

            $semester->events()->create($v->validated());
            $imported++;
        }

        fclose($handle);
        $this->csvFile = null;

        AuditLog::record(
            action: 'imported',
            module: 'Academic Calendar Event',
            referenceId: $this->semesterId,
            description: "CSV import: {$imported} events added, {$skipped} skipped for {$this->academicYear} {$semester->semester} semester."
        );

        $this->dispatch('event-saved');
        $this->dispatch('lw-toast', type: 'success', message: "{$imported} event(s) imported, {$skipped} skipped.");
    }

    public function deleteEvent(int $eventId): void
    {
        $event    = AcademicCalendarEvent::findOrFail($eventId);
        $semester = AcademicCalendar::findOrFail($this->semesterId);

        AuditLog::record(
            action: 'deleted',
            module: 'Academic Calendar Event',
            referenceId: $event->id,
            description: "Deleted {$event->type} event '{$event->name}' on {$event->date} for {$this->academicYear} {$semester->semester} semester."
        );

        $event->delete();

        $this->dispatch('event-deleted');
        $this->dispatch('lw-toast', type: 'success', message: 'Event deleted.');
    }
}
