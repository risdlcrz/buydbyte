<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Order;

class PaymentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $status;
    protected $amount;
    protected $paymentMethod;

    public function __construct(Order $order, string $status, float $amount, string $paymentMethod = null)
    {
        $this->order = $order;
        $this->status = $status;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->getStatusMessage(),
            'order_id' => $this->order->id,
            'status' => $this->status,
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod,
            'link' => route('customer.orders.show', $this->order->id),
            'type' => 'payment_status'
        ];
    }

    private function getStatusMessage()
    {
        $amount = number_format($this->amount, 2);
        
        return match ($this->status) {
            'pending' => "Payment pending for Order #{$this->order->id}. Amount: ${$amount}",
            'processing' => "Payment is being processed for Order #{$this->order->id}. Amount: ${$amount}",
            'completed' => "Payment received for Order #{$this->order->id}. Amount: ${$amount}",
            'failed' => "Payment failed for Order #{$this->order->id}. Please try again.",
            'refunded' => "Refund processed for Order #{$this->order->id}. Amount: ${$amount}",
            'partially_refunded' => "Partial refund processed for Order #{$this->order->id}. Amount: ${$amount}",
            'due' => "Payment due for Order #{$this->order->id}. Amount: ${$amount}",
            'overdue' => "Payment overdue for Order #{$this->order->id}. Amount: ${$amount}",
            default => "Payment status updated for Order #{$this->order->id}"
        };
    }
}