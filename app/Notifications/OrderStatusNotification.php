<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Order;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $status;
    protected $message;

    public function __construct(Order $order, string $status)
    {
        $this->order = $order;
        $this->status = $status;
        $this->message = $this->getStatusMessage($status);
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'order_id' => $this->order->id,
            'status' => $this->status,
            'link' => route('customer.orders.show', $this->order->id),
            'type' => 'order_status'
        ];
    }

    private function getStatusMessage($status)
    {
        return match ($status) {
            'processing' => "Your order #{$this->order->id} is being processed",
            'shipped' => "Good news! Your order #{$this->order->id} has been shipped",
            'delivered' => "Your order #{$this->order->id} has been delivered",
            'cancelled' => "Your order #{$this->order->id} has been cancelled",
            default => "Your order #{$this->order->id} status has been updated to {$status}"
        };
    }
}