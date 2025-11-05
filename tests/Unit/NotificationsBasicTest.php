<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Notifications\OrderStatusNotification;
use App\Notifications\PaymentStatusNotification;
use App\Notifications\BackInStockNotification;
use App\Notifications\ShippingTrackingNotification;
use App\Models\Order;

class NotificationsBasicTest extends TestCase
{
    public function test_order_status_notification_via_database()
    {
        $order = new Order();
        $order->order_id = 'test-order-1';

        $notif = new OrderStatusNotification($order, 'shipped');
        $this->assertContains('database', $notif->via(null));
    }

    public function test_payment_status_notification_via_database()
    {
        $order = new Order();
        $order->order_id = 'test-order-2';

        $notif = new PaymentStatusNotification($order, 'completed', 12.50, 'card');
        $this->assertContains('database', $notif->via(null));
    }

    public function test_back_in_stock_notification_via_database()
    {
        $product = new \stdClass();
        $product->id = 1;
        $product->name = 'Test Product';
        $product->slug = 'test-product';

        $notif = new BackInStockNotification($product);
        $this->assertContains('database', $notif->via(null));
    }

    public function test_shipping_tracking_notification_via_database()
    {
        $order = new Order();
        $order->order_id = 'test-order-3';
        $notif = new ShippingTrackingNotification($order, 'TRK123', 'UPS', 'in_transit');
        $this->assertContains('database', $notif->via(null));
    }
}
