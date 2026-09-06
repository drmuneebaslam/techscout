# QA Test Suite 01: Authentication, User Lifecycle & RBAC

## 1. Test Suite Metadata
* **Module:** Authentication, Member Profiles, Role-Based Access Control (RBAC)
* **Target Routes:** `/auth/login`, `/auth/register`, `/auth/logout`, `/auth/forgot-password`, `/profile/{slug}`, `/admin/users`

---

## 2. Detailed Test Cases

### TC-AUTH-01: User Registration with Validation
* **Preconditions:** Unauthenticated visitor.
* **Test Steps:**
  1. Navigate to `/auth/register`.
  2. Attempt submission with an invalid email (e.g. `user@`).
  3. Attempt submission with a password under 6 characters.
  4. Submit valid credentials (`qa_tester_01@example.com`, Password: `SecurePass#2026`).
* **Expected Result:**
  - Client and server validations trigger informative error alerts on invalid inputs.
  - Valid submission successfully creates user in `users` table with Argon2id/Bcrypt hashed password and logs user into session.

### TC-AUTH-02: Role-Based Route Protection (RBAC)
* **Preconditions:** Logged in as standard member (`role = 'user'`).
* **Test Steps:**
  1. Attempt to navigate directly to `/admin` or `/admin/dashboard`.
  2. Attempt to navigate to `/author/editor`.
* **Expected Result:**
  - System intercepts request with `Auth::requireAdmin()` / `Auth::requireAuthor()` middleware and redirects to `/auth/login` with flash error or 403 Forbidden.

### TC-AUTH-03: Session Destruction on Sign Out
* **Preconditions:** Logged in user.
* **Test Steps:**
  1. Click avatar dropdown in top header &rarr; click "Sign Out" (`/auth/logout`).
  2. Press browser "Back" button and attempt to submit an author draft or admin setting.
* **Expected Result:**
  - Session cookie is invalidated; unauthorized actions are blocked.

### TC-AUTH-04: Admin User CRUD Operations
* **Preconditions:** Logged in as `admin`.
* **Test Steps:**
  1. Navigate to `/admin/users`.
  2. Search for user by email or handle.
  3. Edit user role from `user` to `author` or `admin`.
  4. Suspend user account and verify suspended user cannot log in.
* **Expected Result:**
  - Role modification persists immediately in MySQL; suspended user receives account locked message upon login attempt.
