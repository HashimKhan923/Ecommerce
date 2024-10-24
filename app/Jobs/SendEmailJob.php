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
            // Attempt to send email
            // Mail::mailer('no_reply')->to($this->user->email)->send(new SubscribersNotificationMa($this->details));

            // Update successful email count
            EmailBatch::where('id', $this->batchId)->increment('successful_emails');
            EmailBatch::where('id', $this->batchId)->update(['to_id' => $this->user->id]);
            Subscriber::where('id',$this->user->id)->update(['status' => 'sent']);


                    // Update completed date if this is the last job in the batch
        $this->updateBatchCompletionDate();

        } catch (\Exception $e) {
            // Check if the error is spam-related
            if ($e->getMessage() === 'Spam detected') {
                EmailBatch::where('id', $this->batchId)->increment('spam_emails');
            } else {
                // Increment failed email count
                EmailBatch::where('id', $this->batchId)->increment('failed_emails');
            }
        }
    }

    protected function updateBatchCompletionDate()
    {
        // Get the total emails in the batch
        $batch = EmailBatch::find($this->batchId);
        
        if ($batch) {
            // Check if all emails have been sent (successful + failed + spam)
            $totalEmailsSent = $batch->successful_emails + $batch->failed_emails + $batch->spam_emails;

            if ($totalEmailsSent >= $batch->total_emails) { // Make sure to have a total_emails column
                $batch->completed_at = now(); // Set to current date and time
                $batch->status = 'sent';
                $batch->save();
            }
        }
    }
}
