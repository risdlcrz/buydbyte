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
            'title' => 'Payment Status Update',
            'message' => $this->getStatusMessage(),
            'order_id' => $this->order->order_id,
            'order_number' => isset($this->order->order_number) ? $this->order->order_number : null,
            'status' => $this->status,
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod,
            'link' => route('customer.orders.show', $this->order->order_id),
            'type' => 'payment_status'
        ];
    }

    private function getStatusMessage()
    {
        $amount = number_format($this->amount, 2);
        $orderNumber = isset($this->order->order_number) ? $this->order->order_number : $this->order->order_id;
        
        return match ($this->status) {
            'pending' => "Payment pending for Order #{$orderNumber}. Amount: ₱{$amount}",
            'processing' => "Payment is being processed for Order #{$orderNumber}. Amount: ₱{$amount}",
            'paid' => "Payment received for Order #{$orderNumber}. Amount: ₱{$amount}",
            'completed' => "Payment received for Order #{$orderNumber}. Amount: ₱{$amount}",
            'failed' => "Payment failed for Order #{$orderNumber}. Please try again.",
            'refunded' => "Refund processed for Order #{$orderNumber}. Amount: ₱{$amount}",
            'partially_refunded' => "Partial refund processed for Order #{$orderNumber}. Amount: ₱{$amount}",
            'due' => "Payment due for Order #{$orderNumber}. Amount: ₱{$amount}",
            'overdue' => "Payment overdue for Order #{$orderNumber}. Amount: ₱{$amount}",
            default => "Payment status updated for Order #{$orderNumber}"
        };
    }
}