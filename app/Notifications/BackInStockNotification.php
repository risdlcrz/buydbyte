<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class BackInStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Good news — {$this->product->name} is back in stock!",
            'product_id' => $this->product->id,
            'link' => route('storefront.product', $this->product->slug),
            'type' => 'back_in_stock'
        ];
    }
}
