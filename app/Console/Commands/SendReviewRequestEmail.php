<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
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
        $sevenDaysAgo = Carbon::now()->subDays(10)->startOfDay();

        $orders = Order::where('delivery_status','Delivered')->get();

        foreach ($orders as $order) {

            $order_detail = OrderDetail::where('order_id',$order->id)->get();
            foreach($order_detail as $detail)
            {
                $customer = User::find($order->customer_id);
                Mail::to($customer->email)->send(new ReviewRequestMail($detail));
                $this->info("Review request email sent to {$customer->email}");
            }


        }

        return 0;
    }
}
