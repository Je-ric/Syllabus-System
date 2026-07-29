<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifies a user that they have been assigned to or removed from a role
 * (dean, chair, or faculty) in the organizational hierarchy.
 *
 * USAGE (from OrganizationalHierarchyService):
 *   $user->notify(new RoleAssignmentNotification('dean', 'assigned', $college->name));
 *   $user->notify(new RoleAssignmentNotification('chair', 'removed', $department->name));
 *
 * CONTEXTS : 'dean' | 'chair' | 'faculty'
 * ACTIONS  : 'assigned' | 'removed'
 */
class RoleAssignmentNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string  $context,   // dean | chair | faculty
        protected string  $action,    // assigned | removed
        protected string  $placeName, // college or department name
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->buildMessage(),
            'context' => $this->context,
            'action'  => $this->action,
            'place'   => $this->placeName,
        ];
    }

    private function buildMessage(): string
    {
        $role  = ucfirst($this->context);
        $place = $this->placeName;

        return match ($this->action) {
            'assigned' => match ($this->context) {
                'dean'    => "You have been assigned as Dean of {$place}.",
                'chair'   => "You have been assigned as Chair of {$place}.",
                'faculty' => "You have been assigned as faculty in {$place}.",
                default   => "You have been assigned as {$role} in {$place}.",
            },
            'removed' => match ($this->context) {
                'dean'    => "Your Dean assignment from {$place} has been removed.",
                'chair'   => "Your Chair assignment from {$place} has been removed.",
                'faculty' => "Your faculty assignment from {$place} has been removed.",
                default   => "Your {$role} assignment from {$place} has been removed.",
            },
            default => "Your {$role} assignment in {$place} has been updated.",
        };
    }
}
