<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifies a user that their system role has been granted or revoked
 * via the Account Approval panel (admin action, no hierarchy placement).
 *
 * USAGE (from AccountApprovalService::assignRoles):
 *   $user->notify(new SystemRoleChangedNotification('dean',  'granted'));
 *   $user->notify(new SystemRoleChangedNotification('admin', 'revoked'));
 *
 * ROLES  : 'admin' | 'dean' | 'chair' | 'faculty'
 * ACTIONS: 'granted' | 'revoked'
 */
class SystemRoleChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $role,   // admin | dean | chair | faculty
        protected string $action, // granted | revoked
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->buildMessage(),
            'role'    => $this->role,
            'action'  => $this->action,
        ];
    }

    private function buildMessage(): string
    {
        $label = match ($this->role) {
            'admin'   => 'Administrator',
            'dean'    => 'College Dean',
            'chair'   => 'Department Chair',
            'faculty' => 'Faculty',
            default   => ucfirst($this->role),
        };

        return match ($this->action) {
            'granted' => "You have been granted the {$label} role.",
            'revoked' => "Your {$label} role has been revoked.",
            default   => "Your {$label} role has been updated.",
        };
    }
}
