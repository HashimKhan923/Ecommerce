<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Jobs\SendCampaignEmail;
use App\Models\Campaign;


class SendScheduledCampaigns extends Command
{
    protected $signature = 'campaigns:send';
    protected $description = 'Dispatch emails for campaigns due to send';

    public function handle()
    {
        $now = Carbon::now();

        $campaigns = Campaign::where('status', 'scheduled')
                             ->where('send_time', '<=', $now)
                             ->get();

        foreach ($campaigns as $campaign) {

            // 1. Dispatch emails to all user recipients
            foreach ($campaign->userRecipients as $user) {
                SendCampaignEmail::dispatch($campaign->id, $user->id, null);
            }

            // 2. Dispatch emails to all subscriber recipients
            foreach ($campaign->subscriberRecipients as $subscriber) {
                SendCampaignEmail::dispatch($campaign->id, null, $subscriber->id);
            }

            // 3. Mark campaign as sent
            $campaign->update(['status' => 'sent']);
        }

        return 0;
    }
}

