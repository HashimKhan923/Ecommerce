<?php

namespace App\Jobs;

use App\Mail\SubscribersNotificationMa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Subscriber;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $details;
    protected $batchId;

    public function __construct($user, $details, $batchId)
    {
        $this->user = $user;
        $this->details = $details;
        $this->batchId = $batchId;
    }

    public function handle()
    {
        try {
            // Attempt to send the email
            Mail::mailer('no_reply')->to($this->user->email)->send(new SubscribersNotificationMa($this->details));

            // Increment the count of successful emails
            DB::table('email_batches')->where('id', $this->batchId)->increment('successful_emails');
            DB::table('email_batches')->where('id', $this->batchId)->update(['to_id' => $this->user->id]);
            Subscriber::where('id', $this->user->id)->update(['status'=>'sent']);

        } catch (\Exception $e) {
            // Check for specific spam detection (optional)
            if ($e->getMessage() === 'Spam detected') {
                DB::table('email_batches')->where('id', $this->batchId)->increment('spam_emails');
            } else {
                // Increment the count of failed emails
                DB::table('email_batches')->where('id', $this->batchId)->increment('failed_emails');
            }
        }
    }
}
