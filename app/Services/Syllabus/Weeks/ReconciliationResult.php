<?php

namespace App\Services\Syllabus\Weeks;

// Simple value object returned by WeekReconciliationService::reconcile().
// Lets the Livewire component build an accurate toast without knowing internals.
final class ReconciliationResult
{
    public function __construct(
        public readonly int $datesUpdated,
        public readonly int $weeksAdded,
        public readonly int $weeksDropped,
        public readonly int $labelsResynced,
    ) {}

    // True when the calendar had no material effect on the week set.
    public function hasNoChanges(): bool
    {
        return $this->datesUpdated === 0
            && $this->weeksAdded   === 0
            && $this->weeksDropped === 0
            && $this->labelsResynced === 0;
    }

    // Human-readable summary for the toast message.
    public function toMessage(): string
    {
        if ($this->hasNoChanges()) {
            return 'Weeks are already in sync with the calendar — nothing changed.';
        }

        $parts = [];

        if ($this->datesUpdated > 0) {
            $parts[] = "{$this->datesUpdated} week date" . ($this->datesUpdated !== 1 ? 's' : '') . ' updated';
        }

        if ($this->weeksAdded > 0) {
            $parts[] = "{$this->weeksAdded} new week" . ($this->weeksAdded !== 1 ? 's' : '') . ' added';
        }

        if ($this->weeksDropped > 0) {
            $parts[] = "{$this->weeksDropped} surplus week" . ($this->weeksDropped !== 1 ? 's' : '') . ' removed';
        }

        if ($this->labelsResynced > 0) {
            $parts[] = 'exam and non-teaching labels resynced';
        }

        return implode(', ', $parts) . '.';
    }

    public function toArray(): array
    {
        return [
            'datesUpdated'   => $this->datesUpdated,
            'weeksAdded'     => $this->weeksAdded,
            'weeksDropped'   => $this->weeksDropped,
            'labelsResynced' => $this->labelsResynced,
        ];
    }
}
