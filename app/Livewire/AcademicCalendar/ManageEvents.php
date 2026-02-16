<?php

namespace App\Livewire\AcademicCalendar;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use Carbon\Carbon;
use Livewire\Component;

class ManageEvents extends Component
{
    public string $academicYear;
    public $semesters;
    public array $newEvent = [];
    public array $editing = [];
    public array $dateConflicts = [
        'new' => [],
        'edit' => [],
    ];

    public function mount(string $academicYear): void
    {
        $this->academicYear = $academicYear;
        $this->reloadSemesters();
    }

    public function addEvent(int $semesterId): void
    {
        $semester = $this->semesters->firstWhere('id', $semesterId);
        if (!$semester) {
            return;
        }

        $payload = $this->newEvent[$semesterId] ?? [];
        $date = (string) ($payload['date'] ?? '');

        $this->validate([
            "newEvent.$semesterId.type" => 'required|in:holiday,exam,break,other',
            "newEvent.$semesterId.name" => 'required|string|max:255',
            "newEvent.$semesterId.date" => 'required|date|after_or_equal:' . $semester->start_date->toDateString() . '|before_or_equal:' . $semester->end_date->toDateString(),
        ]);

        if ($this->hasDateConflict($semesterId, $date)) {
            $this->dispatch('lw-toast', type: 'warning', message: 'Date conflict: an event already exists on that date.');
            return;
        }

        AcademicCalendarEvent::create([
            'academic_calendar_id' => $semesterId,
            'type' => $payload['type'],
            'name' => trim((string) $payload['name']),
            'date' => $date,
        ]);

        $this->newEvent[$semesterId] = ['type' => 'holiday', 'name' => '', 'date' => ''];
        $this->dateConflicts['new'][$semesterId] = false;
        $this->reloadSemesters();
        $this->dispatch('lw-toast', type: 'success', message: 'Event added.');
    }

    public function startEdit(int $eventId): void
    {
        $event = AcademicCalendarEvent::find($eventId);
        if (!$event) {
            return;
        }

        $this->editing[$eventId] = [
            'type' => $event->type,
            'name' => $event->name,
            'date' => Carbon::parse($event->date)->toDateString(),
        ];
    }

    public function cancelEdit(int $eventId): void
    {
        unset($this->editing[$eventId], $this->dateConflicts['edit'][$eventId]);
    }

    public function saveEdit(int $eventId): void
    {
        $event = AcademicCalendarEvent::with('calendar')->find($eventId);
        if (!$event || !isset($this->editing[$eventId])) {
            return;
        }

        $semester = $event->calendar;
        $date = (string) ($this->editing[$eventId]['date'] ?? '');

        $this->validate([
            "editing.$eventId.type" => 'required|in:holiday,exam,break,other',
            "editing.$eventId.name" => 'required|string|max:255',
            "editing.$eventId.date" => 'required|date|after_or_equal:' . $semester->start_date->toDateString() . '|before_or_equal:' . $semester->end_date->toDateString(),
        ]);

        if ($this->hasDateConflict((int) $semester->id, $date, $eventId)) {
            $this->dispatch('lw-toast', type: 'warning', message: 'Date conflict: an event already exists on that date.');
            return;
        }

        $event->update([
            'type' => $this->editing[$eventId]['type'],
            'name' => trim((string) $this->editing[$eventId]['name']),
            'date' => $date,
        ]);

        unset($this->editing[$eventId], $this->dateConflicts['edit'][$eventId]);
        $this->reloadSemesters();
        $this->dispatch('lw-toast', type: 'success', message: 'Event updated.');
    }

    public function deleteEvent(int $eventId): void
    {
        $event = AcademicCalendarEvent::find($eventId);
        if (!$event) {
            return;
        }

        $event->delete();
        unset($this->editing[$eventId], $this->dateConflicts['edit'][$eventId]);
        $this->reloadSemesters();
        $this->dispatch('lw-toast', type: 'success', message: 'Event deleted.');
    }

    public function updatedNewEvent($value, $key): void
    {
        if (!str_ends_with((string) $key, '.date')) {
            return;
        }

        [$semesterId] = explode('.', (string) $key);
        $semesterId = (int) $semesterId;
        $date = (string) $value;

        $this->dateConflicts['new'][$semesterId] = $this->hasDateConflict($semesterId, $date);
    }

    public function updatedEditing($value, $key): void
    {
        if (!str_ends_with((string) $key, '.date')) {
            return;
        }

        [$eventId] = explode('.', (string) $key);
        $eventId = (int) $eventId;

        $event = AcademicCalendarEvent::with('calendar')->find($eventId);
        if (!$event) {
            return;
        }

        $this->dateConflicts['edit'][$eventId] = $this->hasDateConflict((int) $event->academic_calendar_id, (string) $value, $eventId);
    }

    private function reloadSemesters(): void
    {
        $this->semesters = AcademicCalendar::with(['events' => fn($q) => $q->orderBy('date')])
            ->where('academic_year', $this->academicYear)
            ->orderBy('semester')
            ->get();

        foreach ($this->semesters as $semester) {
            $this->newEvent[(int) $semester->id] = $this->newEvent[(int) $semester->id] ?? [
                'type' => 'holiday',
                'name' => '',
                'date' => '',
            ];
            $this->dateConflicts['new'][(int) $semester->id] = false;
        }
    }

    private function hasDateConflict(int $semesterId, string $date, ?int $ignoreEventId = null): bool
    {
        if (trim($date) === '') {
            return false;
        }

        $query = AcademicCalendarEvent::where('academic_calendar_id', $semesterId)
            ->whereDate('date', $date);

        if ($ignoreEventId) {
            $query->where('id', '!=', $ignoreEventId);
        }

        return $query->exists();
    }

    public function getWeeksPreviewProperty(): array
    {
        $preview = [];
        foreach ($this->semesters as $semester) {
            $start = Carbon::parse($semester->start_date)->startOfDay();
            $end = Carbon::parse($semester->end_date)->startOfDay();
            $days = $start->diffInDays($end) + 1;

            $preview[(int) $semester->id] = [
                'total_weeks' => (int) ceil($days / 7),
                'days' => $days,
                'start' => $start->format('M d, Y'),
                'end' => $end->format('M d, Y'),
            ];
        }

        return $preview;
    }

    public function render()
    {
        return view('livewire.academic-calendar.manage-events');
    }
}

