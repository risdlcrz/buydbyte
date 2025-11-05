<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RetryFailedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 600]; // Retry after 1 minute, 5 minutes, then 10 minutes

    protected $notification;
    protected $notifiable;

    public function __construct($notifiable, $notification)
    {
        $this->notification = $notification;
        $this->notifiable = $notifiable;
    }

    public function handle()
    {
        try {
            $this->notifiable->notify($this->notification);
        } catch (\Exception $e) {
            Log::error("Failed to send notification (attempt {$this->attempts()}): " . $e->getMessage());
            $this->release($this->backoff[$this->attempts() - 1] ?? 600);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("All retry attempts failed for notification: " . $exception->getMessage());
        
        // Create a failed notification record
        DB::table('failed_notifications')->insert([
            'notifiable_type' => get_class($this->notifiable),
            'notifiable_id' => $this->notifiable->id,
            'notification_class' => get_class($this->notification),
            'error_message' => $exception->getMessage(),
            'created_at' => now()
        ]);
    }
}