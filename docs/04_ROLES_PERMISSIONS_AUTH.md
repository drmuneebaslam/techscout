# TechScout — Roles, Permissions & Security Architecture

> **Tasks Covered**: 261–290 (Phase 8 — User Accounts & Members), 331–360 (Phase 11 — CMS/Admin Permissions), 641–670 (Phase 24 — Platform Security)  
> **Security Baseline**: OWASP Top 10 compliance, Argon2id/Bcrypt hashing, Strict HttpOnly/SameSite session guards, CSRF tokens, and Role-Based Access Control (RBAC).

---

## 1. Role-Based Access Control (RBAC) Matrix (Tasks 261–268 & 334–338)

TechScout enforces strict capability segregation across 8 user roles:

| Capability / Action | Guest | Member | Author | Editor | Moderator | Advertiser | Admin | Super Admin |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| **Read public articles, tools & prompts** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Search & filter content** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **React (Like, Clap, Rocket)** | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Bookmark articles & copy prompts** | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Post public comments** | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Follow authors & customize feeds** | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Submit draft articles, tools & prompts** | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **Edit own drafts & published content** | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **View author creator studio & analytics** | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **Request monetization & non-cash perks** | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **Approve / reject draft submissions** | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **Edit any published article or taxonomy**| ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **Pin comments & moderate discussion queues**| ❌ | ❌ | ❌ | ✅ | ✅ | ❌ | ✅ | ✅ |
| **Manage user strikes, warnings & bans** | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ | ✅ |
| **Purchase & manage ad campaigns** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ |
| **Manage site navigation, SEO & redirects**| ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **Approve payouts & review revenue logs** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| **System config, DB backups & audit logs**| ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |

---

## 2. Authentication & Account Lifecycle (Tasks 270–290)

### Task 270 & 271: Registration & Email Verification
1. User submits username, email, and password via `/auth/register`.
2. Password validated against strength criteria (min 8 chars, 1 uppercase, 1 number, 1 symbol).
3. Record inserted into `users` table with status `unverified`.
4. Cryptographically secure 64-character token generated and dispatched via SMTP welcome email.
5. User clicks activation link -> status transitions to `active`.

### Tasks 272 & 273: Password Reset & Account Recovery
1. User requests reset at `/auth/forgot-password`.
2. One-time expiring token (valid for 60 minutes) stored in `password_reset_token` and `password_reset_expires_at`.
3. Reset page validates token, hashes new password via `password_hash($pwd, PASSWORD_ARGON2ID)`, and wipes all active remember-me sessions.

### Tasks 274 & 275: Two-Factor Authentication (2FA)
- Implemented using TOTP (Time-Based One-Time Password / RFC 6238) compatible with Google Authenticator, 1Password, and Authy.
- Secret stored securely in `two_factor_secret` column.
- Required for all `editor`, `admin`, and `super_admin` accounts.

---

## 3. Platform Security Implementation (Tasks 641–670)

```
                       INCOMING HTTP REQUEST
                                 │
                                 ▼
                     [1. HTTPS & HSTS Guard]
                                 │
                                 ▼
                 [2. Security Headers & CSP Engine]
                                 │
                                 ▼
               [3. Sliding Window IP Rate Limiter]
                                 │
                                 ▼
                [4. CSRF Token Validation Guard]
                                 │
                                 ▼
               [5. Session Guard & RBAC Middleware]
                                 │
                                 ▼
                [6. Input Sanitizer (DOMPurify/PDO)]
                                 │
                                 ▼
                    APPLICATION CONTROLLER / API
```

### Task 643: CSRF Protection Engine
Every state-changing form (POST, PUT, DELETE) requires a cryptographically generated token:
```php
// PHP CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// CSRF Verification Middleware
function verifyCsrfToken($submittedToken) {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken ?? '')) {
        http_response_code(403);
        die(json_encode(['error' => 'Invalid or expired CSRF token']));
    }
}
```

### Tasks 644 & 645: XSS & SQL Injection Defense
- **SQL Injection**: 100% of database interactions execute through PHP PDO prepared statements with parameterized inputs. Zero raw string interpolation.
- **XSS Defense**:
  - Server-side output encoding using `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')`.
  - Client-side rich HTML sanitization using `DOMPurify.sanitize(userInput)` prior to rendering.

### Task 664 & 665: Security Headers & Content Security Policy (CSP)
```http
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';
```

### Task 648 & 649: Rate Limiting & Account Lockout
- **Login Rate Limiter**: Maximum 5 failed attempts per IP per 15 minutes.
- **Account Lockout**: After 5 consecutive failed logins, the account is temporarily locked for 15 minutes with an alert email dispatched to the user.
