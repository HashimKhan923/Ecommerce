<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

        protected $fillable = [
        'seller_id','name','subject','preview_text','content','send_time','status'
    ];
    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }
    public function recipients() {
        return $this->belongsToMany(User::class, 'campaign_recipients')
                    ->withPivot('unsubscribed')->withTimestamps();
    }
    public function trackingEvents() {
        return $this->hasMany(TrackingEvent::class);
    }
    public function segments() {
        return $this->hasMany(CampaignSegment::class);
    }
    // convenience relations
    public function opens() {
        return $this->trackingEvents()->where('event_type', 'open');
    }
    public function clicks() {
        return $this->trackingEvents()->where('event_type', 'click');
    }

    public function stats()
    {
        $sent = $this->recipients()->count();
        $opens = $this->opens()->count();
        $clicks = $this->clicks()->count();
        // $unsubs = $this->trackingEvents()->where('event_type','unsubscribe')->count();
        $linkClicks = $this->trackingEvents()->where('event_type','click')->pluck('url');
        $cvr = 0; // Would compute purchases from clicks if connected to orders

        return [
            'sent' => $sent,
            'opens' => $opens,
            'clicks' => $clicks,
            // 'unsubscribes' => $unsubs,
            'open_rate' => $sent ? $opens/$sent*100 : 0,
            'click_rate' => $sent ? $clicks/$sent*100 : 0,
            // additional stats...
        ];
    }

}
