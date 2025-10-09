<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\UPSTrackingService;

class TrackUPSDeliveries extends Command
{
    protected $signature = 'orders:track-ups';
    protected $description = 'Track UPS orders and update delivery status if delivered.';

    public function handle()
    {
        $this->info("Tracking UPS shipments...");

        $orders = Order::where('delivery_status', 'Confirmed')
            ->whereHas('order_tracking', function ($q) {
                $q->where('courier_name', 'UPS');
            })
            ->with('order_tracking')
            ->get();

        if ($orders->isEmpty()) {
            $this->info("No confirmed UPS orders found.");
            return;
        }

        $ups = new UPSTrackingService();

        foreach ($orders as $order) {
            $trackingNumber = optional($order->order_tracking)->tracking_number;

            if (!$trackingNumber) {
                $this->warn("Order #{$order->id} has no tracking number.");
                continue;
            }

            try {
                $trackingData = $ups->trackShipment($trackingNumber);
                $status = trim(strtoupper(data_get($trackingData, 'trackResponse.shipment.0.package.0.activity.0.status.description', 'UNKNOWN')));

                $this->line("Order #{$order->id} - UPS Status: {$status}");

                if ($status === 'DELIVERED') {
                    $order->delivery_status = 'Delivered';
                    $order->save();

                    $this->info("✅ Order #{$order->id} marked as delivered.");
                }
            } catch (\Exception $e) {
                $this->error("❌ Failed to track order #{$order->id}: " . $e->getMessage());
            }
        }

        $this->info("UPS tracking completed.".$status);
    }
}
