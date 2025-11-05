<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PromotionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $promotion;

    public function __construct($promotion)
    {
        $this->promotion = $promotion;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "New promotion: {$this->promotion['title']}! {$this->promotion['description']}",
            'promotion_id' => $this->promotion['id'],
            'link' => route('storefront.products', ['promotion' => $this->promotion['id']]),
            'type' => 'promotion'
        ];
    }
}