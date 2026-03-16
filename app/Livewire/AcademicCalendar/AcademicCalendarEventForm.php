<?php

namespace App\Livewire\AcademicCalendar;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\AuditLog;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * AcademicCalendarEventForm
 *
 * Handles ADD and EDIT for calendar events with real-time validation.
 * Mounted once per semester tab — receives the semester id.
 * Edit mode is triggered by Alpine opening the modal and calling
 * $wire.startEdit(eventId) which loads the event data into the form.
 */
class AcademicCalendarEventForm extends Component
{
    // ── Public state ──────────────────────────────────────────────────────────

    public int    $semesterId;
    public string $academicYear = '';

    // Form fields
    public string $type = 'holiday';
    public string $name = '';
    public string $date = '';

    // Edit mode
    public ?int  $editingEventId = null;
    public bool  $showEditModal  = false;

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(int $semesterId, string $academicYear): void
    {
        $this->semesterId   = $semesterId;
        $this->academicYear = $academicYear;
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $semester = AcademicCalendar::with('events')->find($this->semesterId);
        return view('livewire.academic-calendar.event-form', compact('semester'));
    }

    // ── Real-time validation ───────────────────────────────────────────────────

    public function updated(string $property): void
    {
        $this->validateOnly($property, $this->rules());
    }

    // ── Add new event ─────────────────────────────────────────────────────────

    public function store(): void
    {
        $validated = $this->validate($this->rules());

        $semester = AcademicCalendar::findOrFail($this->semesterId);

        $event = $semester->events()->create([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'date' => $validated['date'],
        ]);

        AuditLog::record(
            action: 'created',
            module: 'Academic Calendar Event',
            referenceId: $event->id,
            description: "Created {$event->type} event '{$event->name}' on {$event->date} for {$this->academicYear} {$semester->semester} semester."
        );

        $this->reset(['type', 'name', 'date']);
        $this->type = 'holiday';

        $this->dispatch('lw-toast', type: 'success', message: 'Event added successfully.');
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function startEdit(int $eventId): void
    {
        $event = AcademicCalendarEvent::findOrFail($eventId);

        $this->editingEventId = $eventId;
        $this->type           = $event->type;
        $this->name           = $event->name;
        $this->date           = $event->date;
        $this->showEditModal  = true;

        $this->resetErrorBag();
    }

    public function update(): void
    {
        $validated = $this->validate($this->rules(editing: true));

        $event    = AcademicCalendarEvent::findOrFail($this->editingEventId);
        $semester = AcademicCalendar::findOrFail($this->semesterId);

        $event->update([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'date' => $validated['date'],
        ]);

        AuditLog::record(
            action: 'updated',
            module: 'Academic Calendar Event',
            referenceId: $event->id,
            description: "Updated {$event->type} event '{$event->name}' on {$event->date} for {$this->academicYear} {$semester->semester} semester."
        );

        $this->closeEdit();
        $this->dispatch('lw-toast', type: 'success', message: 'Event updated successfully.');
    }

    public function closeEdit(): void
    {
        $this->showEditModal  = false;
        $this->editingEventId = null;
        $this->reset(['type', 'name', 'date']);
        $this->type = 'holiday';
        $this->resetErrorBag();
    }

    // ── Rules ─────────────────────────────────────────────────────────────────

    private function rules(bool $editing = false): array
    {
        $semester  = AcademicCalendar::findOrFail($this->semesterId);

        // Date must be unique within the semester, excluding the event being edited
        $dateUnique = Rule::unique('academic_calendar_events', 'date')
            ->where('academic_calendar_id', $this->semesterId);

        if ($editing && $this->editingEventId) {
            $dateUnique->ignore($this->editingEventId);
        }

        return [
            'type' => ['required', Rule::in(AcademicCalendarEvent::TYPES)],
            'name' => ['required', 'string', 'max:255'],
            'date' => [
                'required',
                'date',
                'after_or_equal:' . $semester->start_date,
                'before_or_equal:' . $semester->end_date,
                $dateUnique,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'          => 'Event type is required.',
            'type.in'                => 'Invalid event type.',
            'name.required'          => 'Event name is required.',
            'name.max'               => 'Event name is too long.',
            'date.required'          => 'Date is required.',
            'date.after_or_equal'    => 'Date must be within the semester range.',
            'date.before_or_equal'   => 'Date must be within the semester range.',
            'date.unique'            => 'An event already exists on this date.',
        ];
    }
}