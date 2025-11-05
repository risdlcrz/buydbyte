<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Order;

class ReviewRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $productIds;

    public function __construct(Order $order, array $productIds = [])
    {
        $this->order = $order;
        $this->productIds = $productIds;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "How was your order #{$this->order->id}? Leave a review and help others.",
            'order_id' => $this->order->id,
            'product_ids' => $this->productIds,
            'link' => route('customer.orders.show', $this->order->id),
            'type' => 'review_request'
        ];
    }
}
