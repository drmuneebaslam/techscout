# 07. Session Security & CSRF Defense (`Session.php`)

## File Location
[`app/Core/Session.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Session.php)

## Key Features
- **Secure Cookie Flags**: Enforces `HttpOnly` and `SameSite=Lax` cookies.
- **CSRF Token Validation**: Generates cryptographic CSRF tokens stored in session and verifies incoming POST submission tokens (`verifyCsrfToken()`).
- **Flash Messages**: Short-lived session notifications that clear automatically upon retrieval.
