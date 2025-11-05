<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderReceivedNotification extends Notification
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
        $isCustomer = isset($notifiable->user_id) && $notifiable->user_id === $this->order->user_id;
        
        return [
            'title' => 'Order Received',
            'message' => $isCustomer
                ? "You have confirmed receiving order #{$this->order->order_number}. Thank you!"
                : "Customer has confirmed receiving order #{$this->order->order_number}.",
            'order_id' => $this->order->order_id,
            'order_number' => $this->order->order_number,
            'customer_name' => isset($this->order->user->name) ? $this->order->user->name : $this->order->user->email,
            'link' => $isCustomer
                ? route('customer.orders.show', $this->order->order_id)
                : '#',
            'type' => 'order_received'
        ];
    }
}
