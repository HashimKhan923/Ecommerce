<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkStat extends Model
{
    use HasFactory;

    protected $fillable = ['campaign_id','url','clicks','unique_clicks'];

    public function campaign() {
        return $this->belongsTo(Campaign::class);
    }
}
