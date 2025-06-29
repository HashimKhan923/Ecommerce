<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Payout;

class FinancialController extends Controller
{
    public function getSellerFinancialDashboard($sellerId)
    {
        // Orders related to the seller
        $orders = Order::where('sellers_id', $sellerId)->get();

        $totalSales = $orders->sum('amount');
        $totalOrders = $orders->count();
        $completedOrders = $orders->where('delivery_status', 'delivered')->count();
        $pendingOrders = $orders->whereIn('delivery_status', ['pending', 'processing'])->count();
        $cancelledOrders = $orders->whereIn('delivery_status', ['cancelled', 'refunded'])->count();

        // Payouts
        $payouts = Payout::where('seller_id', $sellerId)->get();
        $commissionPaid = $payouts->sum('commission');
        $totalPayout = $payouts->where('payment_status', 'paid')->sum('amount');
        $withdrawableBalance = $totalSales - $commissionPaid - $totalPayout;

        $lastPayout = $payouts->where('payment_status', 'paid')->sortByDesc('date')->first();

        // Transactions (based on orders)
        $transactions = $orders->map(function ($order) use ($payouts) {
            $payout = $payouts->where('order_id', $order->id)->first();

            return [
                'order_id' => $order->order_code,
                'customer' => optional($order->customer)->name ?? 'N/A', // add `customer()` relation if needed
                'order_total' => (float) $order->amount,
                'commission' => (float) ($payout->commission ?? 0),
                'net' => (float) ($order->amount - ($payout->commission ?? 0)),
                'status' => $order->payment_status,
                'date' => $order->created_at->format('Y-m-d')
            ];
        });

        return response()->json([
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders,
            'cancelled_orders' => $cancelledOrders,
            'commission_paid' => $commissionPaid,
            'net_earnings' => $totalSales - $commissionPaid,
            'withdrawable_balance' => $withdrawableBalance,
            'last_payout' => $lastPayout ? [
                'amount' => $lastPayout->amount,
                'date' => $lastPayout->date
            ] : null,
            'transactions' => $transactions
        ]);
    }
}
