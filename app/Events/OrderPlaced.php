<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderPlaced
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $order, $shopProducts, $productsByShop, $request, $vendor, $customer, $shopTotalShipment;

    public function __construct($order, $shopProducts, $productsByShop, $request, $vendor, $customer, $shopTotalShipment)
    {
        $this->order = $order;
        $this->shopProducts = $shopProducts;
        $this->productsByShop = $productsByShop;
        $this->request = $request;
        $this->vendor = $vendor;
        $this->customer = $customer;
        $this->shopTotalShipment = $shopTotalShipment;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
