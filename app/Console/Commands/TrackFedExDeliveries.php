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
    
        $orders = Order::where('delivery_status','Confirmed')
            ->with('order_tracking')
            ->get();
    
        $fedex = new FedExTrackingService();
        if($orders)
        {
            foreach ($orders as $order) {
                if (!$order->order_tracking || !$order->order_tracking->tracking_number) {
                    $this->warn("Order #{$order->id} has no tracking number.");
                    continue;
                }
            
                try {
                    $trackingData = $fedex->trackShipment($order->order_tracking->tracking_number);

                            // 🔍 Check for API error before proceeding
        if (isset($trackingData['error'])) {
            $this->error("FedEx API error for Order #{$order->id}: " . $trackingData['body']);
            continue;
        }

                    $this->line("FedEx Response for Order #{$order->id}:\n" . json_encode($trackingData, JSON_PRETTY_PRINT));
                    
                    $status = data_get($trackingData, 'completeTrackResults.0.trackResults.0.latestStatusDetail.description');
            
                    $this->info("Order #{$order->id} FedEx status: " . $status);
            
                    if (str_contains(strtolower($status), 'delivered')) {
                        $order->delivery_status = 'Delivered';
                        $order->save();
                        $this->info("Order #{$order->id} marked as delivered.");
                    }
                } catch (\Exception $e) {
                    $this->error("Failed to track order #{$order->id}: " . $e->getMessage());
                    $this->line("Tracking response: " . json_encode($trackingData ?? [], JSON_PRETTY_PRINT));
                }
            }
        }
        else
        {
            $this->info("No Fullfiled orders available.");
        }

    
        $this->info("Tracking completed.");
    }
}
