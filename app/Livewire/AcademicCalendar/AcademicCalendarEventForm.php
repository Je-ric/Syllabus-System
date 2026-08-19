<?php

namespace App\Livewire\AcademicCalendar;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\AuditLog;
use App\Rules\NoInjectionRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
// use Livewire\WithFileUploads; // CSV import disabled for now

class AcademicCalendarEventForm extends Component
{
    // use WithFileUploads; // CSV import disabled for now

    public int    $semesterId;
    public string $academicYear = '';
    // public mixed  $csvFile      = null; // CSV import disabled for now

    public function mount(int $semesterId, string $academicYear): void
    {
        $this->semesterId   = $semesterId;
        $this->academicYear = $academicYear;
    }

    /**
     * Cached semester with events — re-evaluated only when Livewire re-renders.
     * Using #[Computed] so it is memoised within a single request lifecycle.
     */
    #[Computed]
    public function semester(): ?AcademicCalendar
    {
        return AcademicCalendar::with(['events' => fn($q) => $q->orderBy('date')])
            ->find($this->semesterId);
    }

    public function render()
    {
        return view('livewire.academic-calendar.event-form');
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
                'name'      => ['required', 'string', 'max:255', new NoInjectionRule()],
                'dateStart' => ['required', 'date', 'after_or_equal:' . $semester->start_date, 'before_or_equal:' . $semester->end_date],
                'dateEnd'   => ['required', 'date', 'after_or_equal:dateStart', 'before_or_equal:' . $semester->end_date],
            ]
        );

        if ($validator->fails()) {
            $this->dispatch('lw-toast', type: 'error', message: $validator->errors()->first());
            return;
        }

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

        // Add type-specific warnings for bulk events
        if ($type === 'break' && $inserted > 0) {
            $this->dispatch('lw-toast', type: 'info', 
                message: "Break event created: {$inserted} week(s) will be SKIPPED in syllabi.");
        }

        if (in_array($type, ['exam', 'non_teaching']) && $inserted > 0) {
            $typeLabel = $type === 'exam' ? 'Exam' : 'Non-Teaching';
            $this->dispatch('lw-toast', type: 'info', 
                message: "{$typeLabel} event created: {$inserted} week(s) will be LOCKED in syllabi.");
        }

        AuditLog::record(
            action: 'created',
            module: 'Academic Calendar Event',
            referenceId: $this->semesterId,
            description: "Created {$type} event '{$name}' from {$dateStart} to {$dateEnd} ({$inserted} days) for {$this->academicYear} {$semester->semester} semester."
        );

        unset($this->semester); // bust the computed cache so render() re-fetches
        $this->dispatch('event-saved');
        $this->dispatch('lw-toast', type: 'success', message: "{$inserted} event(s) added.");
        // $this->dispatch('reload-page');
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
            'name' => ['required', 'string', 'max:255', new NoInjectionRule()],
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

        // Add type-specific warnings
        if ($validated['type'] === 'break' && !$editingId) {
            $this->dispatch('lw-toast', type: 'info', 
                message: 'Break event created: This week will be SKIPPED in syllabi.');
        }

        if (in_array($validated['type'], ['exam', 'non_teaching']) && !$editingId) {
            $typeLabel = $validated['type'] === 'exam' ? 'Exam' : 'Non-Teaching';
            $this->dispatch('lw-toast', type: 'info', 
                message: "{$typeLabel} event created: This week will be LOCKED in syllabi.");
        }

        if ($validated['type'] === 'holiday' && str_contains(strtolower($validated['name']), 'christmas')) {
            $this->dispatch('lw-toast', type: 'warning', 
                message: 'Tip: Use "Break" type to skip Christmas break, or "Holiday" for reference only.');
        }

        // Guard: if editing, make sure the row still exists (avoids 404 if
        // it was deleted in another tab/request between form-open and submit)
        if ($editingId) {
            $event = AcademicCalendarEvent::find($editingId);

            if (! $event) {
                $this->dispatch('event-saved'); // closes the modal / refreshes list
                $this->dispatch('lw-toast', type: 'error', message: 'This event no longer exists — it may have already been deleted.');
                return;
            }

            $event->update($validated);

            AuditLog::record(
                action: 'updated',
                module: 'Academic Calendar Event',
                referenceId: $event->id,
                description: "Updated {$event->type} event '{$event->name}' on {$event->date} for {$this->academicYear} {$semester->semester} semester."
            );

            unset($this->semester); // bust the computed cache so render() re-fetches
            $this->dispatch('event-saved');
            $this->dispatch('lw-toast', type: 'success', message: 'Event updated successfully.');
            // $this->dispatch('reload-page');
        } else {
            $event = $semester->events()->create($validated);

            AuditLog::record(
                action: 'created',
                module: 'Academic Calendar Event',
                referenceId: $event->id,
                description: "Created {$event->type} event '{$event->name}' on {$event->date} for {$this->academicYear} {$semester->semester} semester."
            );

            unset($this->semester); // bust the computed cache so render() re-fetches
            $this->dispatch('event-saved');
            $this->dispatch('lw-toast', type: 'success', message: 'Event added successfully.');
            // $this->dispatch('reload-page');
        }
    }


    public function deleteEvent(int $eventId): void
    {
        $semester = AcademicCalendar::findOrFail($this->semesterId);

        // Use find() instead of findOrFail() — if this event was already
        // deleted by an earlier in-flight request (rapid double-click,
        // duplicate dispatch, etc.), just no-op instead of throwing a 404.
        $event = AcademicCalendarEvent::find($eventId);

        if (! $event) {
            $this->dispatch('event-deleted');
            return;
        }

        AuditLog::record(
            action: 'deleted',
            module: 'Academic Calendar Event',
            referenceId: $event->id,
            description: "Deleted {$event->type} event '{$event->name}' on {$event->date} for {$this->academicYear} {$semester->semester} semester."
        );

        $event->delete();

        unset($this->semester); // bust the computed cache so render() re-fetches
        $this->dispatch('event-deleted');
        $this->dispatch('lw-toast', type: 'success', message: 'Event deleted.');
        // $this->dispatch('reload-page');
    }
}