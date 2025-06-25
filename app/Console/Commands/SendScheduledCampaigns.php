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
        $campaigns = Campaign::where('status','scheduled')
                             ->where('send_time','<=',$now->toDateTimeString())
                             ->get();
        foreach ($campaigns as $campaign) {
            // Dispatch emails to all recipients
            foreach ($campaign->recipients as $user) {
                SendCampaignEmail::dispatch($campaign->id, $user->id);
            }
            $campaign->update(['status' => 'sending']);
        }
    }
}
