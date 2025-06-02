<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = ['campaign_id','user_id','event_type','url','created_at'];

    public function campaign() {
        return $this->belongsTo(Campaign::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}
