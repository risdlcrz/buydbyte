<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class FeedbackResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $feedback;
    protected $response;

    public function __construct($feedback, $response)
    {
        $this->feedback = $feedback;
        $this->response = $response;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Admin has responded to your feedback",
            'feedback_id' => $this->feedback->id,
            'response' => $this->response,
            'link' => route('customer.feedback.show', $this->feedback->id),
            'type' => 'feedback_response'
        ];
    }
}