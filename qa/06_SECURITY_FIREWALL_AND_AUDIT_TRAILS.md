# 🛡️ QA Specification: Security Firewall, Audit Trails & Invariants

> **Scope**: Web Application Firewall, Suspicious Activity Detection, Two-Factor Authentication, Session Isolation, and Immutable Audit Trails.  
> **Target Environment**: `http://localhost/techscout.n`  
> **Route**: `/admin/security`, `/admin/settings`  
> **Auth Session**: `?qa_auth=admin`  

---

## 📋 Security Architecture Matrix

| Security Layer | Module / Route | Invariants & Protection Scope |
|---|---|---|
| **WAF & IP Rules** | `GET /admin/security` | Blacklist/whitelist IP addresses, CIDR blocks, block malicious user agents |
| **Suspicious Activity Engine** | Automatic Trigger | Auto-flags accounts creating >10 comments/min or multiple posts/sec |
| **Session Isolation** | `GET /account/settings?tab=devices` | SHA-256 session token hashing, IP change detection, remote kill |
| **Two-Factor Auth (2FA)** | `POST /account/2fa/toggle` | TOTP authenticator app integration, recovery codes |
| **Immutable Audit Trail** | `GET /admin/users` (Drawer) | Structured JSON event logging for administrative and security actions |
| **CSRF & Input Sanitization** | Universal Middleware | Cryptographic token validation on all state-mutating POST requests |

---

## 🔍 Detailed Security Test Cases

### 1. Web Application Firewall (WAF) & Perimeter Defense (`/admin/firewall`)
1. **Four-Tab Navigation System**:
   - **Tab 1: Active Perimeter Enforcement Rules (`rules`)**:
     - IP and User-Agent blacklist/whitelist entries.
     - Target value, rule action (`block`/`allow`), reason, hit counters, creation date, and unblock button.
   - **Tab 2: Real-Time Intrusion & Threat Detection Stream (`threats`)**:
     - Live WAF attack log: Timestamp, Attacker IP, Threat Classification (SQLi, XSS, Path Traversal, LFI, RCE), Requested URI, User Agent signature, and Action Taken.
   - **Tab 3: 15-Minute IP Lockouts (`lockouts`)**:
     - Automated brute-force defense entries: Locked IP, failed attempts counter, remaining lock time countdown badge, and manual instant unlock button.
   - **Tab 4: Live Security Anomalies Telemetry Feed (`anomalies`)**:
     - Behavioral anomalies: rapid requests velocity, comment flood spam, login spikes, and one-click resolve action.
2. **Tab Table Pagination & Search**:
   - Every tab features dedicated client-side pagination (10 entries per page).
   - Dynamic page number pills with active glow indicator, Prev/Next navigation buttons.
   - Live search input dynamically filters rows and recalculates pagination counts in real-time.
3. **Dedicated System Audit Telemetry**:
   - General system audit logs are cleanly separated and accessible via the dedicated button navigating to `/admin/audit-logs`, keeping the firewall perimeter view focused solely on cyber defense.


### 2. Suspicious Activity Automated Detection
1. **Comment Flood Detection**:
   - Simulate 12 rapid comments within 30 seconds.
   - Verify account is automatically tagged `is_flagged_suspicious = 1` with reason *"Rapid comment submission velocity"*.
   - Account appears in `/admin/users` with high-visibility amber warning badge `⚠️ Suspicious Activity Flagged`.
2. **Editorial Verification**:
   - Admin can view flagged accounts, inspect the audit trail drawer, and clear flag or issue a strike.

### 3. Session Token Isolation & Remote Kill
1. **Token Invariant**:
   - Session tokens are generated using `random_bytes(32)` and hashed before database storage.
   - When a user changes their password, all secondary sessions are automatically invalidated.
2. **Remote Revocation**:
   - Open `/account/settings?tab=devices` from a second browser/device.
   - On the primary device, click *"Revoke Session ✕"*.
   - On the second browser, refresh page &rarr; Session is immediately destroyed and redirected to `/auth/login`.

### 4. Immutable Double-Entry Ledger Security
1. **Negative Balance Protection**:
   - Attempting to deduct more balance than an author possesses fails with:
     `Cannot deduct $X USD. Creator only has $Y USD available.` unless explicitly overridden with `allow_overdraft`.
2. **Transaction Uniqueness**:
   - Every transaction enforces a globally unique `reference_no` (`TXN-YYYY-XXXXXXXX`).
   - Payout approval requires administrative confirmation and status transition logging.

---

### 5. Dedicated System Audit Telemetry Hub (`/admin/audit-logs`)
1. **Universal Client-Side Pagination & Faceted Search**:
   - The `/admin/audit-logs` station displays chronological system logs with interactive pagination.
   - Configurable page size: `10`, `25`, `50`, `100` items per view.
   - Debounced search filter matches: Action name (e.g. `payout_approved`, `user_ban`), Actor username/ID, Target resource, or IP address.
   - Dynamic page pill counter with active state highlighting.
2. **Structured JSON Payload Inspector**:
   - Each audit log row includes a *"View Payload"* action button.
   - Opens a slide-out modal or drawer rendering the formatted JSON metadata (parameters, changed fields, previous vs. new values).
3. **Database Performance Indexing**:
   - The `audit_logs` table features composite indexes on `(created_at DESC)` and `(action, created_at)` to guarantee sub-10ms query execution across 100,000+ entries.

---

### 6. Platform Speed Optimizations & Caching Invariants
1. **Apache HTTP Server Acceleration (`.htaccess`)**:
   - **Gzip / Deflate Compression (`mod_deflate`)**:
     - All text-based assets (`text/html`, `text/css`, `application/javascript`, `application/json`, `image/svg+xml`) are compressed on the wire.
     - Confirmed via `Content-Encoding: gzip` in HTTP response headers.
   - **Browser Caching Policy (`mod_expires`)**:
     - Static assets (CSS, JS, Fonts, WebP images) specify `Cache-Control: public, max-age=2592000` (30 days) to eliminate redundant server requests.
2. **High-Frequency Database Indexing Architecture**:
   - Database columns frequently accessed in WHERE clauses and ORDER BY operations are explicitly indexed:
     - `contents`: `(status, published_at)`, `(view_count DESC)`, `(author_id)`
     - `authors`: `(user_id)`, `(follower_count DESC)`, `(total_views DESC)`
     - `users`: `(username)`, `(email)`, `(status)`
     - `payout_requests`: `(author_id)`, `(status, created_at)`
     - `challenge_submissions`: `(campaign_id, status)`

