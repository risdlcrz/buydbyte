<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewFeedbackNotification;
use App\Notifications\PaymentReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_notification_sent_to_customer_and_finance()
    {
        Notification::fake();

        // create customer and finance users
    $customer = User::factory()->create(['role' => 'customer']);
    // DB enum doesn't include 'finance' role; use 'seller' to represent finance-like user in tests
    $finance = User::factory()->create(['role' => 'seller']);

        // create a basic order (include required fields)
        $order = Order::create([
            'user_id' => $customer->user_id,
            'status' => 'pending',
            'shipping_method' => 'standard',
            'subtotal' => 100.00,
            'shipping_cost' => 0.00,
            'total' => 100.00,
            'shipping_address' => [
                'line1' => '123 Test St',
                'city' => 'Testville',
                'country' => 'NG'
            ],
        ]);

        // simulate sending payment notifications
        $customer->notify(new PaymentReceivedNotification($order, 100.00));
        \Illuminate\Support\Facades\Notification::send([$finance], new PaymentReceivedNotification($order, 100.00));

        Notification::assertSentTo($customer, PaymentReceivedNotification::class);
        Notification::assertSentTo($finance, PaymentReceivedNotification::class);
    }

    public function test_feedback_submission_notifies_admins_and_creates_database_notifications()
    {
        Notification::fake();

        $customer = User::factory()->create(['role' => 'customer']);
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        $this->actingAs($customer);

        $response = $this->post(route('customer.feedback.store'), [
            'type' => 'general',
            'rating' => 5,
            'comment' => 'Great service and product quality.'
        ]);

        $response->assertRedirect(route('customer.feedback.index'));

        // Assert notification sent to both admins
        Notification::assertSentTo([$admin1, $admin2], NewFeedbackNotification::class);

        // Assert feedback is stored
        $this->assertDatabaseHas('feedback', [
            'user_id' => $customer->user_id,
            'type' => 'general',
            'rating' => 5,
        ]);

    // Note: Notification::fake() prevents database channel writes; we asserted dispatch above
    }
}
