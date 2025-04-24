<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\FedExTrackingService;

class TrackFedExDeliveries extends Command
{
    protected $signature = 'orders:track-fedex';

    protected $description = 'Track FedEx orders and update delivery status if delivered.';

    public function handle()
    {
        $this->info("Tracking FedEx shipments...");
    
        $orders = Order::where('delivery_status', '!=', 'Delivered')
            ->with('order_tracking')
            ->get();
    
        $fedex = new FedExTrackingService();
    
        foreach ($orders as $order) {
            if (!$order->order_tracking || !$order->order_tracking->tracking_number) {
                $this->warn("Order #{$order->id} has no tracking number.");
                continue;
            }
    
            try {
                $trackingData = $fedex->trackShipment($order->order_tracking->tracking_number);
                $status = data_get($trackingData, 'output.completeTrackResults.0.trackResults.0.latestStatusDetail.statusByLocale');
    
                if (strtolower($status) === 'delivered') {
                    $order->delivery_status = 'Delivered';
                    $order->save();
                    $this->info("Order #{$order->id} marked as delivered.");
                }
            } catch (\Exception $e) {
                $this->error("Failed to track order #{$order->id}: " . $e->getMessage());
            }
        }
    
        $this->info("Tracking completed.");
    }
}
