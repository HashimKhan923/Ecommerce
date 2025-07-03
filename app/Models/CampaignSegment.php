<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignSegment extends Model
{
    use HasFactory;

    protected $fillable = ['campaign_id','segment_id']; 

    public function campaing() {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function segment() {
        return $this->belongsTo(Segment::class, 'segment_id');
    }


}
