<?php

namespace App\Jobs;

use App\Mail\SubscribersNotificationMa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailBatch;

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
        try {
            // Attempt to send email
            Mail::mailer('no_reply')->to($this->user->email)->send(new SubscribersNotificationMa($this->details));

            // Update successful email count
            // EmailBatch::where('id', $this->batchId)->increment('successful_emails');
            // EmailBatch::where('id', $this->batchId)->update(['to_id' => $this->user->id]);

        } catch (\Exception $e) {
            // Check if the error is spam-related
            if ($e->getMessage() === 'Spam detected') {
                // EmailBatch::where('id', $this->batchId)->increment('spam_emails');
            } else {
                // Increment failed email count
                // EmailBatch::where('id', $this->batchId)->increment('failed_emails');
            }
        }
    }
}
