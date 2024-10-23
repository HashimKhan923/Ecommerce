<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\SendEmailJob;
use App\Models\Subscriber;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Throwable;

class SendEmailBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userIds;
    protected $details;
    protected $batchId;

    public function __construct($userIds, $details, $batchId)
    {
        $this->userIds = $userIds;
        $this->details = $details;
        $this->batchId = $batchId;
    }

    public function handle()
    {
        // Fetch users again in case there were any changes
        $users = Subscriber::whereIn('id', $this->userIds)->get();
        
        // Prepare batch jobs for email sending
        $jobs = [];
        foreach ($users as $user) {
            $jobs[] = new SendEmailJob($user, $this->details, $this->batchId);
        }

        // Dispatch batch
        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) {
                // Mark the batch as completed
                DB::table('email_batches')->where('id', $this->batchId)->update(['completed_at' => now()]);
            })
            ->catch(function (Batch $batch, Throwable $e) {
                // Handle failure
            })
            ->dispatch();
    }
}
