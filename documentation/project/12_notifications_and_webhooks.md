# 12. In-App Notifications & Outbound Webhooks

## File Locations
- [`app/Core/NotificationHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/NotificationHelper.php)
- [`app/Core/WebhookEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/WebhookEngine.php)

## Key Features
- **Real-Time Notification Badges**: Dispatches targeted in-app notifications stored in the `notifications` table, updating unread badge counters.
- **Outbound Webhooks**: Asynchronously dispatches event webhooks (`article.published`, `user.registered`, `payout.processed`) to partner API endpoints with HMAC SHA-256 payload verification signatures.
