<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Order;

class PaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $dueDate;
    protected $amount;

    public function __construct(Order $order, $dueDate, float $amount)
    {
        $this->order = $order;
        $this->dueDate = $dueDate;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $formattedAmount = number_format($this->amount, 2);
        $dueDate = $this->dueDate->format('M d, Y');

        return [
            'message' => "Payment reminder: ${$formattedAmount} due by {$dueDate} for Order #{$this->order->id}",
            'order_id' => $this->order->id,
            'due_date' => $this->dueDate,
            'amount' => $this->amount,
            'link' => route('customer.orders.show', $this->order->id),
            'type' => 'payment_reminder'
        ];
    }
}