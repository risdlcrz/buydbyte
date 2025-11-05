<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCompletedNotification extends Notification
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
            'title' => 'Order Completed',
            'message' => $isCustomer
                ? "Order #{$this->order->order_number} has been completed! Thank you for your feedback."
                : "Order #{$this->order->order_number} has been completed by customer.",
            'order_id' => $this->order->order_id,
            'order_number' => $this->order->order_number,
            'customer_name' => isset($this->order->user->name) ? $this->order->user->name : $this->order->user->email,
            'link' => $isCustomer
                ? route('customer.orders.show', $this->order->order_id)
                : '#',
            'type' => 'order_completed'
        ];
    }
}
