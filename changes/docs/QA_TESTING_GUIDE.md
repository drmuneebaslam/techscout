# TechScout Comprehensive QA & Testing Plan

## 1. Authentication & Security (Login & Signup)

### 1.1 User Registration (Signup)
- **TC_SIGNUP_001:** Verify successful registration with valid email, valid password, and matching confirm password.
- **TC_SIGNUP_002:** Verify registration fails if email format is invalid (e.g., `user@domain`, `user.domain.com`).
- **TC_SIGNUP_003:** Verify registration fails if the email is already registered in the database.
- **TC_SIGNUP_004:** Verify registration fails if password is too short (less than 8 characters).
- **TC_SIGNUP_005:** Verify registration fails if password does not contain required complexity (uppercase, lowercase, number, special character).
- **TC_SIGNUP_006:** Verify registration fails if "Password" and "Confirm Password" do not match.
- **TC_SIGNUP_007:** Verify SQL Injection payload in the `username` or `email` field is sanitized and does not break the query.
- **TC_SIGNUP_008:** Verify XSS payload in `name` field is sanitized and displayed safely on the dashboard.
- **TC_SIGNUP_009:** Verify that successful signup automatically redirects the user to the `Verify Email` page or sends an OTP.
- **TC_SIGNUP_010:** Verify that CSRF token is present and validated on signup form submission.

### 1.2 User Authentication (Login)
- **TC_LOGIN_001:** Verify successful login with correct email and password.
- **TC_LOGIN_002:** Verify login fails with correct email but incorrect password. Shows generic error ("Invalid credentials").
- **TC_LOGIN_003:** Verify login fails with unregistered email. Shows generic error.
- **TC_LOGIN_004:** Verify "Remember Me" functionality securely stores a persistent session cookie.
- **TC_LOGIN_005:** Verify closing and reopening the browser with "Remember Me" unchecked requires re-login.
- **TC_LOGIN_006:** Verify account lockout mechanism after 5 consecutive failed login attempts (Brute Force Protection).
- **TC_LOGIN_007:** Verify SQL Injection payload in the `email` or `password` field on login is rejected.
- **TC_LOGIN_008:** Verify users with unverified emails are prompted to verify their email before accessing restricted areas.
- **TC_LOGIN_009:** Verify banned/suspended users are prevented from logging in.
- **TC_LOGIN_010:** Verify CSRF token is validated on login form submission.

### 1.3 Session & Access Control
- **TC_AUTH_001:** Verify authenticated user accessing `/auth/login` is redirected to `/dashboard`.
- **TC_AUTH_002:** Verify unauthenticated user accessing `/admin/dashboard` is redirected to login.
- **TC_AUTH_003:** Verify Author user accessing `/admin/dashboard` is denied access (403 Forbidden).
- **TC_AUTH_004:** Verify successful logout clears the session securely and invalidates cookies.
- **TC_AUTH_005:** Verify session expiration after defined inactivity period.

## 2. Creator Studio & Author Dashboard

### 2.1 Feedback System (Newly Implemented)
- **TC_FEEDBACK_001:** Verify Author can successfully submit a Feedback form (Subject and Message).
- **TC_FEEDBACK_002:** Verify Author receives a success flash message and form resets.
- **TC_FEEDBACK_003:** Verify Admin can view the submitted feedback in the Admin panel under `Community & CRM -> Author Feedbacks`.
- **TC_FEEDBACK_004:** Verify Admin can update the feedback status (Pending -> Reviewed -> Resolved).
- **TC_FEEDBACK_005:** Verify Admin can attach an internal note to the feedback.

### 2.2 Dashboard Navigation
- **TC_DASHBOARD_001:** Verify all tabs in Author Dashboard switch content via `?tab=...` query param parsing without full page reload.
- **TC_DASHBOARD_002:** Verify clicking a Notification for a Payout navigates the author to `/author/dashboard?tab=payouts` successfully.
- **TC_DASHBOARD_003:** Verify clicking a Notification for a Tip navigates the author to `/author/dashboard?tab=tipjar` successfully.
- **TC_DASHBOARD_004:** Verify Author Rewards page is removed and fully integrated into the Dashboard.

## 3. UI/UX & Dynamic Feed (Homepage)

### 3.1 Algorithmic Feed
- **TC_HOME_001:** Verify Homepage article feed uses algorithmic sorting by default (Views, Reactions, Hourly random seed).
- **TC_HOME_002:** Verify the feed rotates dynamically over time without manual updates.
- **TC_HOME_003:** Verify pinned articles always appear at the top of the feed regardless of algorithm.
- **TC_HOME_004:** Verify switching to "Trending" or "Latest" updates the feed appropriately.

### 3.2 Layout Constraints
- **TC_LAYOUT_001:** Verify Header Navbar is 100% full-width across standard viewports (Mobile, Tablet, Desktop).
- **TC_LAYOUT_002:** Verify CSS Cache invalidation properly updates Cloudflare/CDN on iterative deployments.
