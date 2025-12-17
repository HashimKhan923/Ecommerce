<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Subscriber;
use App\Models\User;
use Mail;
use App\Mail\CampaignMail;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $campaignId, $userId, $subscriberId;

    public function __construct(int $campaignId, int $userId = null, int $subscriberId = null)
    {
        $this->campaignId = $campaignId;
        $this->userId = $userId;
        $this->subscriberId = $subscriberId;
    }


    public function handle()
    {
        $campaign = Campaign::find($this->campaignId);

        if (!$campaign || $campaign->status !== 'scheduled') {
            return;
        }

        // Load recipient (user OR subscriber)
        $recipient = $this->userId 
            ? User::find($this->userId)
            : Subscriber::find($this->subscriberId);

        if (!$recipient) return;

        // Check unsubscribed
        $row = CampaignRecipient::where('campaign_id', $campaign->id)
                                ->where(function ($q) {
                                    $q->where('user_id', $this->userId)
                                    ->orWhere('subscriber_id', $this->subscriberId);
                                })
                                ->first();

        if ($row && $row->unsubscribed) {
            return;
        }

        // Send Email
        Mail::mailer('no_reply')->to($recipient->email)
            ->send(new CampaignMail($campaign, $recipient));

        // Optional: After sending, update counters
    }
}
