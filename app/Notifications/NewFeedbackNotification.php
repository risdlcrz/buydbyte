<?php

namespace App\Notifications;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewFeedbackNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $type = ucfirst($this->feedback->type);
        $rating = str_repeat('★', $this->feedback->rating) . str_repeat('☆', 5 - $this->feedback->rating);

        return [
            'title' => "New {$type} Feedback",
            'message' => "Rating: {$rating}\nFrom: {$this->feedback->user->full_name}",
            'link' => route('admin.feedback.show', $this->feedback->feedback_id),
            'type' => 'new_feedback'
        ];
    }
}