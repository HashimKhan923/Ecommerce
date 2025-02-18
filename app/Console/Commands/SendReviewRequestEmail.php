<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderDetail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\ReviewRequestMail;

class SendReviewRequestEmail extends Command
{
    protected $signature = 'send:review-request';
    protected $description = 'Send review request email 7 days after order is placed';

    public function handle()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7)->startOfDay();

        $orders = OrderDetail::whereDate('created_at', '=', $sevenDaysAgo)->get();

        foreach ($orders as $order) {
            $customer = User::find($order->customer_id);
            Mail::to($customer->email)->send(new ReviewRequestMail($order));
            $this->info("Review request email sent to {$customer->email}");
        }

        return 0;
    }
}
