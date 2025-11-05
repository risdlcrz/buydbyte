<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoyaltyPointsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $points;
    protected $reason;

    public function __construct(int $points, string $reason = null)
    {
        $this->points = $points;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "You earned {$this->points} loyalty points" . ($this->reason ? ": {$this->reason}" : ''),
            'points' => $this->points,
            'reason' => $this->reason,
            'link' => route('account'),
            'type' => 'loyalty_points'
        ];
    }
}
