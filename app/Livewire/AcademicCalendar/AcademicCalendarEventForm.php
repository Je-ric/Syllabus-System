<?php

namespace App\Livewire\AcademicCalendar;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\AuditLog;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AcademicCalendarEventForm extends Component
{
    public int    $semesterId;
    public string $academicYear = '';

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
     * Handles both ADD (editingId = null) and UPDATE (editingId = int).
     * Called from Alpine with all form values passed as arguments —
     * no Livewire properties needed for form state.
     */
    public function saveEvent(?int $editingId, string $type, string $name, string $date): void
    {
        $semester = AcademicCalendar::findOrFail($this->semesterId);

        $dateUnique = Rule::unique('academic_calendar_events', 'date')
            ->where('academic_calendar_id', $this->semesterId);

        if ($editingId) {
            $dateUnique->ignore($editingId);
        }

        $validated = $this->validate([
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
        ], compact('type', 'name', 'date'));

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
