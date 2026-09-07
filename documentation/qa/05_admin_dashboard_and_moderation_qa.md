# QA Test Guide 05: Admin Dashboard & System Moderation QA

This document specifies the step-by-step Enterprise QA testing suite for the Admin Management Portal, User Management (`views/admin/users.php`), Author Application Approvals (`app/Controllers/AuthorApplicationController.php`), Navigation Builder, System Analytics, and Site Settings (`app/Controllers/AdminController.php`).

---

## Pre-Test Setup & Requirements

| Requirement | Value / Condition |
| :--- | :--- |
| **Base URL** | `http://localhost/techscout.n/` |
| **User Role** | Super Admin (`admin@techscout.com` / `password123`). |
| **Test Subsystems** | User Management, Author Applications, Navigation Editor, System Metrics. |

---

## Test Suite 1.0: User Account Management & Moderation

### Test Case 1.1: User Status Modification & Ban Enforcement
- **Objective**: Verify admin ability to ban, suspend, reactivate, or alter roles of user accounts.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Locate Target User**
   - 1.1: Log in as Admin and open `/admin/users`.
   - 1.2: In search input `#user_search`, type `badactor@example.com`.
   - 1.3: Confirm user row displays current status `active`.

2. **Step 2: Execute Account Ban Action**
   - 2.1: Click "Actions" dropdown menu on target user row -> Select "Ban Account".
   - 2.2: Modal opens: Enter Ban Reason: `Repeated spam comments and policy violation.`.
   - 2.3: Click "Confirm Ban".
   - 2.4: Verify database update: `users.status = 'banned'`, `users.ban_reason = '...'`.

3. **Step 3: Banned User Login Attempt**
   - 3.1: Open Incognito browser window.
   - 3.2: Attempt login as `badactor@example.com`.
   - 3.3: Verify login fails with error message: *"Your account has been suspended: Repeated spam comments and policy violation."*

---

## Test Suite 2.0: Author Application Processing (`app/Controllers/AuthorApplicationController.php`)

### Test Case 2.1: Reviewing & Approving Author Applications
- **Objective**: Verify candidate author application workflow, portfolio evaluation, and role promotion.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Candidate Application Submission**
   - 1.1: Log in as standard member `applicant@techscout.com`.
   - 1.2: Navigate to `/author/apply`.
   - 1.3: Fill Application Form: Bio, Portfolio Link (`https://github.com/applicant`), Sample Article.
   - 1.4: Click "Submit Application". Confirm status `pending`.

2. **Step 2: Admin Evaluation & Approval (`views/admin/author_applications.php`)**
   - 2.1: Log in as Admin. Open `/admin/author-applications`.
   - 2.2: Select application `#APP-104` for `applicant@techscout.com`.
   - 2.3: Review portfolio URL and sample text preview.
   - 2.4: Click "Approve Application".
   - 2.5: Select Initial Tier: `Bronze Author`.
   - 2.6: Click "Confirm Promotion".

3. **Step 3: Database & Permissions Check**
   - 3.1: Confirm `users.role` updated from `'member'` to `'author'`.
   - 3.2: Confirm record created in `authors` table with `tier = 'bronze'`.
   - 3.3: Verify automated welcome email dispatched to candidate via Mailer engine.

---

## Test Suite 3.0: Navigation Menu Builder (`views/admin/navigation.php`)

### Test Case 3.1: Dynamic Header & Footer Menu Ordering
- **Objective**: Verify drag-and-drop reordering, adding custom links, and instant menu cache invalidation.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Add New Menu Item**
   - 1.1: Open `/admin/navigation`.
   - 1.2: In "Add Link Item" form, enter Label: `AI Tools Guide`, URL: `/directory/tools`.
   - 1.3: Select Target Location: `Header Navigation`.
   - 1.4: Click "Add to Menu".

2. **Step 2: Reorder & Save**
   - 2.1: Drag newly added link to 2nd position in list.
   - 2.2: Click "Save Menu Structure".
   - 2.3: Verify database table `navigation_menus` updates `sort_order`.

3. **Step 3: Frontend Menu Rendering**
   - 3.1: Open public homepage `/`.
   - 3.2: Confirm top header navbar displays `AI Tools Guide` as the 2nd navigation item.

---

## Pass / Fail Criteria Checklist

- [ ] Account bans lock out sessions instantly across all active devices.
- [ ] Author applications promote user roles accurately in DB.
- [ ] Navigation builder updates header/footer layout without breaking HTML.
- [ ] QA Inspector: TechScout Lead Admin QA.
