<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Order;

class ShippingTrackingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $trackingNumber;
    protected $carrier;
    protected $status;

    public function __construct(Order $order, string $trackingNumber = null, string $carrier = null, string $status = 'shipped')
    {
        $this->order = $order;
        $this->trackingNumber = $trackingNumber;
        $this->carrier = $carrier;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $message = match ($this->status) {
            'shipped' => "Your order #{$this->order->id} has shipped",
            'in_transit' => "Your order #{$this->order->id} is in transit",
            'out_for_delivery' => "Your order #{$this->order->id} is out for delivery",
            'delivered' => "Your order #{$this->order->id} has been delivered",
            default => "Shipping update for order #{$this->order->id}"
        };

        return [
            'message' => $message,
            'order_id' => $this->order->id,
            'tracking_number' => $this->trackingNumber,
            'carrier' => $this->carrier,
            'status' => $this->status,
            'link' => route('customer.orders.show', $this->order->id),
            'type' => 'shipping_tracking'
        ];
    }
}
