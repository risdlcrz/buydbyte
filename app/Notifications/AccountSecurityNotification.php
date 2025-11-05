<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountSecurityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $meta;

    public function __construct(string $message, array $meta = [])
    {
        $this->message = $message;
        $this->meta = $meta;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'meta' => $this->meta,
            'type' => 'account_security',
            'link' => $this->meta['link'] ?? route('account')
        ];
    }
}
