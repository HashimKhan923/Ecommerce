<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\CampaignSummary;
use App\Models\TrackingEvent;

class RollupOldCampaigns extends Command
{
    protected $signature = 'campaigns:rollup';
    protected $description = 'Summarize old campaigns and clean up recipients & tracking';

    public function handle()
    {
        $this->info('Running campaign rollup...');

        // Example: roll up campaigns older than 90 days
        $campaigns = Campaign::where('created_at', '<', now()->subDays(90))
            ->whereDoesntHave('summary')
            ->get();

        foreach ($campaigns as $campaign) {
            $totalSent = $campaign->recipient()->count();
            $totalOpened = $campaign->trackingEvents()->where('event_type','open')->count();
            $totalClicked = $campaign->trackingEvents()->where('event_type', 'click')->count();
            $totalUnsubscribed = $campaign->recipient()->where('unsubscribed', true)->count();

            $openRate = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0;
            $clickRate = $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0;

            CampaignSummary::create([
                'campaign_id' => $campaign->id,
                'total_sent' => $totalSent,
                'total_opened' => $totalOpened,
                'total_clicked' => $totalClicked,
                'total_unsubscribed' => $totalUnsubscribed,
                'open_rate' => $openRate,
                'click_rate' => $clickRate,
            ]);

            // Delete recipients & tracking
            $campaign->recipients()->delete();
            TrackingEvent::where('campaign_id', $campaign->id)->delete();

            $this->info("✅ Rolled up campaign #{$campaign->id}");
        }

        $this->info('🎉 Rollup complete!');
    }
}
