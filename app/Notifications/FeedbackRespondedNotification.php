<?php

namespace App\Notifications;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FeedbackRespondedNotification extends Notification
{
    use Queueable;

    protected Feedback $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Feedback Response',
            'message' => $this->feedback->admin_response,
            'feedback_id' => $this->feedback->feedback_id,
            'status' => $this->feedback->status,
            'link' => route('customer.feedback.show', $this->feedback->feedback_id),
        ];
    }
}
