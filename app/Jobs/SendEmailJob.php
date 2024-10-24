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
    protected $batchId;

    public function __construct($user, $details, $x)
    {
        $this->user = $user;
        $this->details = $details;
        $this->batchId = $x;
    }

    public function handle()
    {
        try {
            // Attempt to send email
            Mail::mailer('no_reply')->to($this->user->email)->send(new SubscribersNotificationMa($this->details));

            // Update successful email count
            EmailBatch::where('id', $this->x)->increment('successful_emails');
            EmailBatch::where('id', $this->x)->update(['to_id'=>$this->user->id]);

        } catch (\Exception $e) {
            // Check if the error is spam-related
            if ($e->getMessage() === 'Spam detected') {
                EmailBatch::where('id', $this->x)->increment('spam_emails');
            } else {
                // Increment failed email count
                EmailBatch::where('id', $this->x)->increment('failed_emails');
            }
        }
    }

    
}
