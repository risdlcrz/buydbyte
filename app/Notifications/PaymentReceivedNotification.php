<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    protected Order $order;
    protected float $amount;

    public function __construct(Order $order, float $amount)
    {
        $this->order = $order;
        $this->amount = $amount;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $formattedAmount = number_format($this->amount, 2);
        $message = $notifiable->role === 'customer'
            ? "Payment of ₦{$formattedAmount} received for order #{$this->order->order_number}."
            : "Payment of ₦{$formattedAmount} received for order #{$this->order->order_number} from {$this->order->user->full_name}.";

        return [
            'title' => 'Payment Received',
            'message' => $message,
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->amount,
            'customer_name' => $this->order->user->full_name,
            'link' => route($notifiable->role === 'customer' ? 'orders.show' : 'admin.orders.show', $this->order->id)
        ];
    }
}