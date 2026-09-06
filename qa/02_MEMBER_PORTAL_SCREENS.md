# 👤 QA Specification: Authenticated Member Portal Screens

> **Scope**: Member profile settings, avatar management, device isolation, security credentials, reading history, bookmarks, and author application.  
> **Target Environment**: `http://localhost/techscout.n`  
> **Auth Session**: `?qa_auth=member` or login as `asdf`  

---

## 📋 Member Screen & Sub-Feature Matrix

| ID | Screen / Tab Title | Route / Selector | Key Features Tested |
|---|---|---|---|
| 2.0 | Prioritized Dropdown Menu | Top Right Avatar Menu | Ordered from most to least used; Sign Out at bottom |
| 2.1 | Personal Info & Bio | `GET /account/settings?tab=profile` | Avatar photo upload, remote URL preview, display name, handle, headline, bio |
| 2.2 | Password & Account Security | `GET /account/settings?tab=security` | Current password check, new password validation, 2FA toggle |
| 2.3 | Active Device Isolation | `GET /account/settings?tab=devices` | Current session indicator, IP address, user agent, remote session kill |
| 2.4 | Community Standing & Strikes | `GET /account/settings?tab=strikes` | Strike counter (0/3 strikes), suspension policy, appeal portal |
| 2.5 | Saved Bookmarks | `GET /bookmarks` | Bookmarked articles, category tags, remove bookmark |
| 2.6 | Reading History | `GET /history` | Read progress retention %, last read timestamps, resume reading |
| 2.7 | Communications & Inbox | `GET /inbox` | Member-to-creator messaging, unread counts |
| 2.8 | Notifications Center | `GET /notifications` | Mentions, editorial alerts, bounty updates, mark all as read |
| 2.9 | Support & Helpdesk | `GET /support` | Open tickets, reply to staff, attachment uploads |
| 2.10 | Author Application Workflow | `GET /author/apply` | Portfolio links, technical writing sample, topics, submission review state |

---

## 🔍 Detailed Functional & Visual Test Cases

### Screen 2.0: Prioritized Navigation Dropdown Order
1. **Verification of User Dropdown Structure**:
   - Log in as a regular member (`?qa_auth=member`).
   - Click the top right profile avatar button.
   - Verify the exact requested logical order:
     1. `Apply to Become an Author ✨` (or Review / Revisions alert)
     2. `Saved Bookmarks 🔖`
     3. `Reading History 🕒`
     4. `Communications & Inbox 📥`
     5. `Notifications Center`
     6. `Support & Helpdesk`
     7. `Personalize Feed & Topics 🌟`
     8. `My Profile 👤`
     9. `Account Settings & Devices 🛡️`
     10. `Sign Out` (at the very bottom with distinct red styling)
   - Open mobile drawer navigation and confirm identical ordered structure.

### Screen 2.1: Member Personal Information & Avatar (`/account/settings?tab=profile`)
1. **PHP 8.2+ Undefined Array Key Prevention**:
   - Verify zero PHP warnings on page load: lines 9 and 107 in `settings.php` use safe fallback `$currentAvatar = $user['author_avatar'] ?? $user['avatar_url'] ?? ''`.
   - Both non-author members and authors load clean UI with initials or existing avatar.
2. **File Upload & Live Preview**:
   - Choose a PNG or JPG avatar photo using the file input.
   - Verify instant live preview in `#avatar_live_preview` without page reload.
   - Test alternative remote URL input: paste `https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150`. Verify image preview updates live on input.
3. **Saving Changes**:
   - Edit Display Name and Headline.
   - Click *"Save Profile Changes ✓"*.
   - Verify success toast and persistent update across the header and profile.

### Screen 2.2: Password & Account Security (`/account/settings?tab=security`)
1. **Password Change**:
   - Input current password, new password, and confirmation password.
   - Test password strength validator.
   - Click *"Update Password"*. Verify flash notification.

### Screen 2.3: Active Device Isolation & Session Kill (`/account/settings?tab=devices`)
1. **Current Session Recognition**:
   - Verify the current active session is marked with a glowing green badge `● This Device (Current Active Session)`.
   - Verify IP address and user agent browser information are correctly parsed.
2. **Remote Session Revocation**:
   - Click *"Revoke Session ✕"* on any secondary device.
   - Verify confirmation modal and immediate termination of remote session token.

### Screen 2.4: Community Standing & Strike History (`/account/settings?tab=strikes`)
1. **Standing Counter**:
   - Verify gauge displays current strike status (e.g. `0 / 3 Strikes — Good Standing`).
   - Verify detailed violation history table or empty state notice *"No community guidelines infractions recorded"*.

### Screen 2.6: Genuine Member Reading History (`/history`)
1. **Pristine Reading Roster (No Simulator Pollution)**:
   - Verify reading history only reflects articles the member has personally opened and engaged with.
   - Zero synthetic simulation rows: Engagement Studio boosts never inject fake reading entries into the member's personal history.
2. **Table Pagination & Progress Bar**:
   - History stream utilizes the universal table paginator with page size selection (10, 15, 25 rows) and search filter.
   - Displays read progress retention percentage bar, dwell time spent, and direct "Resume Reading ↗" link.

### Screen 2.1 Character Limit Validation:
1. **Full Name & Username Constraints**:
   - Name must be 2–60 characters. Submitting a single character or >60 characters returns an error badge.
   - Username must be 3–30 characters matching alphanumeric and underscore format. Spaces and special characters are rejected.
