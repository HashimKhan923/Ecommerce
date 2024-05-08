<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Log\Events\MessageLogged;


class LogEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SpecificErrorOccurred $event)
    {
        Mail::raw($event->errorMessage, function ($message) {
            $message->to('khanhash1994@gmail.com');
            $message->subject('Error Notification');
        });
    }
}
