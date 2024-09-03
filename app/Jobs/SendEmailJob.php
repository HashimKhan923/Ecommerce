<?php

namespace App\Jobs;

use App\Mail\UserNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $details;

    public function __construct($user, $details)
    {
        $this->user = $user;
        $this->details = $details;
    }

    public function handle()
    {
        Mail::to($this->user->email)->send(new UserNotificationMail($this->details));
    }
}
