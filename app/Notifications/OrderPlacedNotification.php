<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
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
        
        // Get customer name safely
        $customerName = 'Customer';
        if (isset($this->order->user)) {
            if (isset($this->order->user->name)) {
                $customerName = $this->order->user->name;
            } elseif (isset($this->order->user->email)) {
                $customerName = $this->order->user->email;
            }
        }
        
        $message = $isCustomer 
            ? "Your order #{$this->order->order_number} has been placed successfully."
            : "Order #{$this->order->order_number} has been placed by {$customerName}.";
        
        return [
            'title' => 'Order Placed',
            'message' => $message,
            'order_id' => $this->order->order_id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->total,
            'customer_name' => $customerName,
            'link' => $isCustomer 
                ? route('customer.orders.show', $this->order->order_id)
                : '#'
        ];
    }
}