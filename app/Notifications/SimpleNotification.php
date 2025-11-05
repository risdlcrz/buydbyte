<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SimpleNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $body;
    /** @var array<string,mixed> */
    public array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $body, array $data = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     * We'll store notifications in the database. Broadcast optional.
     *
     * @param  mixed  $notifiable
     * @return array<int,string>
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @param  mixed  $notifiable
     * @return array<string,mixed>
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }

    /**
     * Get the array representation for broadcasting.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
            'created_at' => now()->toDateTimeString(),
        ]);
    }
}
