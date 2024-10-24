<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailBatch extends Model
{
    use HasFactory;

    protected $fillable = ['batch_id','total_emails','successful_emails','failed_emails','spam_emails','from_id','to_id','status','start_at','completed_at'];

}
