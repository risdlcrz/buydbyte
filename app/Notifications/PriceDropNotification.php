<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PriceDropNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;
    protected $oldPrice;
    protected $newPrice;

    public function __construct($product, $oldPrice, $newPrice)
    {
        $this->product = $product;
        $this->oldPrice = $oldPrice;
        $this->newPrice = $newPrice;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $discount = round((($this->oldPrice - $this->newPrice) / $this->oldPrice) * 100);
        
        return [
            'message' => "Price drop alert! {$this->product->name} is now {$discount}% off",
            'product_id' => $this->product->id,
            'link' => route('storefront.product', $this->product->slug),
            'type' => 'price_drop',
            'old_price' => $this->oldPrice,
            'new_price' => $this->newPrice
        ];
    }
}