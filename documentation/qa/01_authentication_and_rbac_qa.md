# QA Test Guide 01: Authentication & Role-Based Access Control (RBAC)

This document specifies the complete, step-by-step Enterprise QA testing suite for the Authentication subsystem (`app/Core/Auth.php`, `app/Controllers/AuthController.php`) and Role-Based Access Control (`app/Models/RBAC.php`) within TechScout.

---

## Pre-Test Setup & Environment Requirements

| Requirement | Value / Condition |
| :--- | :--- |
| **Base URL** | `http://localhost/techscout.n/` |
| **Database State** | `database/seeds.sql` loaded with default Admin (`admin@techscout.com`), Author (`author@techscout.com`), and Member (`member@techscout.com`). |
| **Mail Server** | SMTP test server running or local debug mail log active (`app/Core/Mailer.php`). |
| **Browser Contexts** | Chrome (Incognito), Firefox, and Safari for session isolation testing. |

---

## Test Suite 1.0: User Registration Workflow

### Test Case 1.1: Standard Member Registration with Valid Data
- **Objective**: Verify that a new user can register successfully with valid credentials and receive a verification email.
- **Preconditions**: User is not logged in. Navigate to `http://localhost/techscout.n/register`.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Form Input Entry**
   - 1.1: Enter Full Name: `Jane Doe` in `#name`.
   - 1.2: Enter Username: `janedoe` in `#username`.
   - 1.3: Enter Email: `janedoe@example.com` in `#email`.
   - 1.4: Enter Password: `SecurePassword123!` in `#password`.
   - 1.5: Enter Confirm Password: `SecurePassword123!` in `#confirm_password`.
   - 1.6: Check "I agree to Terms & Privacy Policy" checkbox (`#terms_agree`).

2. **Step 2: Form Submission**
   - 2.1: Click the "Create Account" button (`#btn_register`).
   - 2.2: Verify HTTP `POST /register` is sent with proper CSRF token.

3. **Step 3: Database & State Verification**
   - 3.1: Query MySQL table `users` for `email = 'janedoe@example.com'`.
   - 3.2: Confirm `role` column equals `'member'`.
   - 3.3: Confirm `status` column equals `'pending'` or `'active'` (based on email verification config).
   - 3.4: Verify `password` column is a valid bcrypt/Argon2 hash (must NOT be plaintext).
   - 3.5: Confirm token record created in `email_verifications` table.

4. **Step 4: User Experience Verification**
   - 4.1: Confirm browser redirects to `/verify-email` or `/onboarding`.
   - 4.2: Verify success toast/alert message displays: *"Account created successfully! Please verify your email."*

---

### Test Case 1.2: Registration Validation & Edge Cases
- **Objective**: Ensure registration fails gracefully when invalid or duplicate data is supplied.

#### Test Matrix:
| Sub-Step | Scenario | Input Data | Expected Result / Error Message |
| :--- | :--- | :--- | :--- |
| **1.2.1** | Duplicate Email | `email = 'admin@techscout.com'` | Validation error: *"Email address is already registered."* |
| **1.2.2** | Duplicate Username | `username = 'admin'` | Validation error: *"Username is already taken."* |
| **1.2.3** | Weak Password | `password = '12345'` | Validation error: *"Password must be at least 8 characters with numbers and symbols."* |
| **1.2.4** | Mismatched Password | `password = 'Pass123!'`, `confirm = 'Pass456!'` | Validation error: *"Password confirmation does not match."* |
| **1.2.5** | Invalid Email Format | `email = 'invalid-email-string'` | Validation error: *"Please enter a valid email address."* |
| **1.2.6** | Missing Terms Check | `#terms_agree` unchecked | Validation error: *"You must accept the Terms of Service to proceed."* |

---

## Test Suite 2.0: User Authentication & Login Workflow

### Test Case 2.1: Valid Login Authentication
- **Objective**: Verify user authentication, session initiation, and cookie handling.
- **Preconditions**: Account `author@techscout.com` exists with password `password123`.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Input Credentials**
   - 1.1: Navigate to `http://localhost/techscout.n/login`.
   - 1.2: Enter Email/Username: `author@techscout.com`.
   - 1.3: Enter Password: `password123`.
   - 1.4: Check "Remember Me" checkbox (`#remember_me`).

2. **Step 2: Process Authentication**
   - 2.1: Click "Sign In" button.
   - 2.2: Verify server evaluates `Auth::login($credentials)`.

3. **Step 3: Session & Security Checks**
   - 3.1: Verify PHP session contains `user_id`, `user_role = 'author'`, and `authenticated = true`.
   - 3.2: Verify session ID regeneration via `session_regenerate_id(true)` to prevent Session Fixation attacks.
   - 3.3: Verify `remember_token` cookie is created with `HttpOnly`, `SameSite=Lax` or `Strict` flags.
   - 3.4: Verify entry logged in `audit_logs` table (`action = 'USER_LOGIN_SUCCESS'`).

4. **Step 4: Redirection & UI Verification**
   - 4.1: Confirm user is redirected to `/author/dashboard`.
   - 4.2: Verify header navbar updates from "Login/Register" to "Author Studio" avatar dropdown menu.

---

### Test Case 2.2: Brute Force Defense & Invalid Credentials
- **Objective**: Verify security controls lock out brute force attempts after consecutive failures.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Invalid Attempts**
   - 1.1: Attempt login with `email = 'admin@techscout.com'` and password `wrongpass1`.
   - 1.2: Repeat login attempt 5 consecutive times within 60 seconds.

2. **Step 2: Throttle Lockout Verification**
   - 2.1: On 6th attempt, verify server returns HTTP 429 Too Many Requests or lock error.
   - 2.2: Confirm error message displays: *"Too many failed login attempts. Please try again in 15 minutes."*
   - 2.3: Verify `firewall_logs` or `audit_logs` records IP throttle trigger.

---

## Test Suite 3.0: Two-Factor Authentication (2FA)

### Test Case 3.1: 2FA Setup & Verification Flow
- **Objective**: Verify TOTP 2FA secret generation, QR code rendering, and OTP validation.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Enable 2FA in Settings**
   - 1.1: Log in as `member@techscout.com`.
   - 1.2: Navigate to `/account/settings#security`.
   - 1.3: Click "Enable Two-Factor Authentication".

2. **Step 2: QR Code & Secret Check**
   - 2.1: Verify QR Code SVG or Image renders dynamically (`app/Core/SvgHelper.php`).
   - 2.2: Verify 16-character base32 TOTP secret displays.
   - 2.3: Scan QR code with Google Authenticator or Authy.

3. **Step 3: OTP Verification**
   - 3.1: Enter 6-digit code generated by app into `#totp_code`.
   - 3.2: Click "Verify & Enable".
   - 3.3: Verify database table `users` updates `two_factor_enabled = 1` and `two_factor_secret = '<encrypted_secret>'`.
   - 3.4: Confirm 8 emergency backup codes are rendered for printing/saving.

4. **Step 4: Login with 2FA Challenge**
   - 4.1: Log out and log back in as `member@techscout.com`.
   - 4.2: Verify system redirects to `/login/two-factor` prompt.
   - 4.3: Enter valid 6-digit code -> verify successful login to dashboard.
   - 4.4: Enter invalid 6-digit code -> verify error message: *"Invalid authentication code."*

---

## Test Suite 4.0: Role-Based Access Control (RBAC) Enforcement

### Test Case 4.1: Authorization Matrix Enforcement
- **Objective**: Verify that users cannot access routes beyond their role permissions.

#### Permission Verification Matrix:
| Route URL | Guest | Member | Author | Admin | Expected Enforcement Behavior |
| :--- | :---: | :---: | :---: | :---: | :--- |
| `/` (Homepage) | ALLOW | ALLOW | ALLOW | ALLOW | Public access allowed for all. |
| `/directory/tools` | ALLOW | ALLOW | ALLOW | ALLOW | Public directory visible to all. |
| `/account/settings` | DENY (302) | ALLOW | ALLOW | ALLOW | Guests redirected to `/login`. |
| `/author/editor` | DENY (403) | DENY (403) | ALLOW | ALLOW | Members blocked with 403 Forbidden. |
| `/admin/dashboard` | DENY (403) | DENY (403) | DENY (403) | ALLOW | Authors & Members blocked with 403. |
| `/admin/firewall` | DENY (403) | DENY (403) | DENY (403) | ALLOW | Restricted strictly to Super Admin. |

#### Sub-Step Verification Checklist:
- [ ] Attempt direct URL access to `/admin/dashboard` as `member@techscout.com`.
- [ ] Confirm HTTP status 403 Forbidden response page renders (`views/errors/403.php`).
- [ ] Confirm security incident recorded in `audit_logs` (`action = 'UNAUTHORIZED_ACCESS_ATTEMPT'`).
- [ ] Attempt API POST to `/api/v1/admin/users/delete` without admin session -> Verify JSON response `{"status": 403, "error": "Unauthorized"}`.

---

## Pass / Fail Criteria & Sign-off Checklist

- [ ] All 12 sub-step test cases executed cleanly.
- [ ] Zero unhandled PHP exceptions or raw SQL errors exposed.
- [ ] Session security cookies verified with `HttpOnly` and `SameSite` flags.
- [ ] Password hashes verified to use modern bcrypt/Argon2 algorithm.
- [ ] Brute-force throttling verified active.
- [ ] Sign-off Auditor: TechScout Lead QA Engineer.
