<?php

namespace App\Notifications;

use App\Models\Syllabus;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifies a user that a syllabus they are involved in has changed status.
 * Uses the 'database' channel — appears in the NotificationBell dropdown.
 *
 * USAGE:
 *   // From a Livewire component (also refreshes bell live):
 *   $recipient->notify(new SyllabusStatusNotification($syllabus, 'submitted'));
 *   $this->dispatch('notification-received');
 *
 *   // From a Controller (bell refreshes on next page load):
 *   $recipient->notify(new SyllabusStatusNotification($syllabus, 'approved'));
 *
 * STATUSES: 'submitted' | 'approved' | 'for_revision' | 'concurred' | 'rejected'
 *
 * PAYLOAD written to notifications.data:
 *   ['message', 'syllabus_id', 'status', 'course_code', 'url']
 */
class SyllabusStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Syllabus $syllabus,
        protected string   $status,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
        // Add 'mail' here and implement toMail() when email notifications are needed.
    }

    public function toArray(object $notifiable): array
    {
        $courseCode = $this->syllabus->relationLoaded('course')
            ? $this->syllabus->course?->code
            : $this->syllabus->course()->value('code');

        return [
            'message'     => $this->statusLabel($courseCode),
            'syllabus_id' => $this->syllabus->id,
            'status'      => $this->status,
            'course_code' => $courseCode,
            'url'         => '/syllabi/' . $this->syllabus->id,
        ];
    }

    protected function statusLabel(?string $courseCode): string
    {
        $course = $courseCode ? " for {$courseCode}" : '';

        return match ($this->status) {
            'submitted'    => "A syllabus{$course} has been submitted for review.",
            'approved'     => "The syllabus{$course} has been approved.",
            'for_revision' => "The syllabus{$course} has been returned for revision.",
            'concurred'    => "The syllabus{$course} has been concurred by the chair.",
            'rejected'     => "The syllabus{$course} has been rejected.",
            default        => "The syllabus{$course} status changed to {$this->status}.",
        };
    }
}
