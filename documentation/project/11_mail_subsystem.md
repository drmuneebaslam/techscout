# 11. Multi-Transport Mail Subsystem (`Mailer.php` & `SmtpClient.php`)

## File Locations
- [`app/Core/Mailer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Mailer.php)
- [`app/Core/SmtpClient.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SmtpClient.php)

## Key Features
- **20+ Built-In HTML Mail Templates**: Supports emails for account registration, email verification OTP, password resets, article approvals, editorial revisions, payout claims, tip notifications, and broadcast newsletters.
- **Dual Transport Mechanisms**:
  1. Socket-based native SMTP client (`SmtpClient.php`) connecting via TLS/SSL socket streams.
  2. Local Python mail proxy fallback.
- **Email Tracking Analytics**: Embeds open tracking pixels (`/mail/track/open`) and rewrites links for click analytics (`/mail/track/click`).
