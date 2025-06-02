<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use App\Models\User;
use Mail;
use App\Mail\CampaignMail;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $campaignId, $userId;

    public function __construct(int $campaignId, int $userId)
    {
        $this->campaignId = $campaignId;
        $this->userId = $userId;
    }

    public function handle()
    {
        $campaign = Campaign::find($this->campaignId);
        $user = User::find($this->userId);

        // If the user has unsubscribed or campaign is inactive, skip
        if (!$campaign || !$user || $campaign->status !== 'scheduled') return;
        if ($campaign->recipients()->where('user_id', $user->id)->first()->pivot->unsubscribed) {
            return;
        }

        // Send the email
        Mail::to($user->email)->send(new CampaignMail($campaign, $user));
        
        // Update status if this was the last email (optional)
        // e.g., mark campaign as 'sent' if all emails dispatched
    }
}
