# QA Test Suite: Authentication, Sessions & RBAC Security (`QA-AUTH-01`)

## Test Cases & Specifications

### TC-AUTH-001: Standard User Authentication & Argon2id Verification
- **Preconditions:** Registered user exists in `users` table (`username: testuser`, `password: SecretPassword123!`).
- **Action:** Send `POST /login` with `username_or_email=testuser` and `password=SecretPassword123!`.
- **Expected Result:**
  - Status 302 Redirect to `/dashboard`.
  - Session cookie initialized with `HttpOnly`, `SameSite=Lax`, and `Secure` attributes.
  - User's `last_login_at` timestamp in `users` updated to current GMT.
  - Audit log recorded in `audit_logs` (`action: user.login`).

### TC-AUTH-002: Invalid Password Rate Limiting & Brute-Force Guard
- **Preconditions:** User exists.
- **Action:** Send 6 consecutive `POST /login` requests with invalid passwords from IP `103.151.42.15`.
- **Expected Result:**
  - First 5 attempts return `HTTP 401 Unauthorized` or flash error "Invalid credentials".
  - 6th attempt returns `HTTP 429 Too Many Requests` with `Retry-After: 60`.
  - No database state mutation on user record.

### TC-AUTH-003: CSRF Token Integrity on State-Modifying POST Requests
- **Preconditions:** Authenticated user session.
- **Action:** Send `POST /admin/settings/save` with missing or altered `_token` / `X-CSRF-Token`.
- **Expected Result:**
  - Request rejected immediately with `HTTP 403 Forbidden`.
  - Error message: "CSRF token mismatch or expired".
  - No settings updated in `site_settings`.

### TC-AUTH-004: Role-Based Access Control (RBAC) Admin Guard
- **Preconditions:** Logged in user with role `subscriber`.
- **Action:** Attempt to visit `GET /admin/dashboard`, `GET /admin/incoming-webhooks`, or `GET /admin/custom-api`.
- **Expected Result:**
  - `HTTP 403 Forbidden` or redirect to `/` with flash message: "Unauthorized access: Administrator role required".
  - Non-admin cannot view system statistics, webhook tokens, or database schemas.

### TC-AUTH-005: Dual-Theme Preference Persistence on User Session
- **Preconditions:** User logged in.
- **Action:** Click theme toggle button (`#theme-toggle`) on header to switch to `light`.
- **Expected Result:**
  - Cookie `ts_theme=light` set with `max-age=31536000`.
  - `document.documentElement` receives `data-theme="light"` and class `.theme-light`.
  - Subsequent page navigations load in light mode without flash of unstyled content (FOUC).
