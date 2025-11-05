# Notifications

This project uses Laravel's `database` notification channel for customer notifications. Below is a short guide on the notification types available and when to trigger them.

Notification types

- `order_status` — updates when an order changes state (processing, shipped, delivered, cancelled).
- `payment_status` — status updates for payments (pending, processing, completed, failed, refunded).
- `payment_reminder` — reminders for pending payments or due invoices.
- `shipping_tracking` — shipping updates and tracking numbers.
- `promotion` — promotional offers and sales.
- `price_drop` — price drop alerts for products.
- `feedback_response` — when admin responds to customer feedback.
- `account_security` — security-related notices, e.g. login from a new device or password change.
- `back_in_stock` — product back-in-stock alerts for users who subscribed.
- `review_request` — request to review purchased products after delivery.
- `loyalty_points` — loyalty points awarded or updated.

Where to trigger notifications (examples)

- Order status changes: typically in `App\Http\Controllers\Admin\OrderController@updateStatus`.
  Example: `$order->user->notify(new \App\Notifications\OrderStatusNotification($order, 'shipped'));`

- Payment updates: on webhook or admin confirmation. Example in `Admin\OrderController@confirmPayment`.
  Example: `$order->user->notify(new \App\Notifications\PaymentStatusNotification($order, 'completed', $order->total));`

- Shipping/tracking: when carrier updates tracking status, call: `$order->user->notify(new \App\Notifications\ShippingTrackingNotification($order, $tracking, $carrier, 'in_transit'));`

- Back-in-stock: when a product is restocked, notify subscribers (implement a subscription table and call `BackInStockNotification`).

- Account security: on login from new IP/device or password change: `$user->notify(new \App\Notifications\AccountSecurityNotification('New login', ['ip' => $ip]));`

Frontend

- The storefront layout includes a notification bell and dropdown that fetches notifications via an AJAX endpoint (`notifications.get`). Ensure that the client-side helper functions for icons and payment badges in `resources/views/layouts/storefront.blade.php` are present.

Extending

- You can add mail and SMS channels by implementing `via()` in the notification classes and adding the respective `toMail()` or `toTwilio()` methods.

Testing

- Placeholders tests are available in `tests/Unit/NotificationsBasicTest.php` to confirm notifications are instantiable and use the `database` channel.

Security note

- Do not store plain-text sensitive information in notification payloads. Keep payloads safe and minimal and use IDs/links to the app for detailed views.
