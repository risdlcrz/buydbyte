<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class ProcessBatchNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notifications;
    protected $notifiables;

    public function __construct(Collection $notifiables, Notification $notification)
    {
        $this->notifications = $notification;
        $this->notifiables = $notifiables;
    }

    public function handle()
    {
        foreach ($this->notifiables as $notifiable) {
            try {
                $notifiable->notify($this->notifications);
            } catch (\Exception $e) {
                // Log the error but continue processing other notifications
                \Log::error("Failed to send notification to {$notifiable->id}: " . $e->getMessage());
                continue;
            }
        }
    }

    public function failed(\Throwable $exception)
    {
        \Log::error("Batch notification job failed: " . $exception->getMessage());
    }
}