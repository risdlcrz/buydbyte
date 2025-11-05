<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderDeliveredNotification extends Notification
{
    use Queueable;

    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $message = $notifiable->role === 'customer'
            ? "Your order #{$this->order->order_number} has been delivered."
            : "Order #{$this->order->order_number} has been delivered to customer.";

        return [
            'title' => 'Order Delivered',
            'message' => $message,
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'delivered_at' => $this->order->delivered_at,
            'customer_name' => $this->order->user->full_name,
            'link' => route($notifiable->role === 'customer' ? 'orders.show' : 'admin.orders.show', $this->order->id)
        ];
    }
}