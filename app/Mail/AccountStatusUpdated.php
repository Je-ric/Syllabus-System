<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AccountStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $status;
    public $user;
    /**
     * Create a new message instance.
     */
    public function __construct(User $user,string $status)
    {
        $this->user = $user;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject('CSMS Account Status Update')
            ->view('emails.accountStatus');
    }
}
