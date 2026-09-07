# QA Test Guide 03: Directory & AI Tools Suite QA

This document specifies the complete, step-by-step Enterprise QA testing suite for the TechScout Directory subsystem: AI Tools (`views/directory/tools.php`), Custom Prompts (`views/directory/prompts.php`), Custom GPTs (`views/directory/gpts.php`), Categories, Search & Filter Engines (`app/Controllers/DirectoryController.php`, `app/Controllers/SearchController.php`).

---

## Pre-Test Setup & Requirements

| Requirement | Value / Condition |
| :--- | :--- |
| **Base URL** | `http://localhost/techscout.n/` |
| **Database State** | Seeded with 50+ AI Tools, 30+ Prompts, 20+ Custom GPTs across 10 categories. |
| **Session Contexts** | Anonymous Visitor (Guest), Authenticated Member (`member@techscout.com`). |

---

## Test Suite 1.0: AI Tools Directory & Search Filters

### Test Case 1.1: Multi-Filter Search & Pagination
- **Objective**: Verify live filtering by Category, Pricing Model, Ratings, and Keyword Query.
- **Preconditions**: Navigate to `http://localhost/techscout.n/directory/tools`.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Category Filter Selection**
   - 1.1: Click category pill "Code Assistance" (`[data-category="code-assistance"]`).
   - 1.2: Verify URL updates to `/directory/tools?category=code-assistance`.
   - 1.3: Verify displayed cards only include tools under Code Assistance.

2. **Step 2: Pricing Model Filter**
   - 2.1: Check Pricing checkbox: `Free Trial` and `Open Source`.
   - 2.2: Verify cards filter dynamically via AJAX/Fetch or URL query parameters.
   - 2.3: Confirm result count text updates: *"Showing 12 AI Tools matching filters"*.

3. **Step 3: Live Keyword Search Bar**
   - 3.1: In search bar (`#tool_search_input`), type `GitHub Copilot`.
   - 3.2: Verify live debounce delay (300ms) triggers filter update.
   - 3.3: Confirm target tool card renders at top position.

4. **Step 4: Pagination Control**
   - 4.1: Scroll to bottom of directory grid.
   - 4.2: Click Page 2 (`.pagination-link[data-page="2"]`).
   - 4.3: Confirm smooth scroll to top of grid and new set of 12 items rendered.

---

## Test Suite 2.0: Single Tool Detail View & Reviews System

### Test Case 2.1: Single Tool Page & Ratings Submission (`app/Models/Comment.php`)
- **Objective**: Verify metadata rendering, outbound link tracking, and member rating submissions.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Outbound Website Link Click Tracking**
   - 1.1: Open single tool page: `/directory/tools/chatgpt`.
   - 1.2: Click "Visit Official Site" button (`#btn_outbound_visit`).
   - 1.3: Verify system records click event in `analytics` table (`event_type = 'OUTBOUND_TOOL_CLICK'`).
   - 1.4: Confirm external site opens in a new tab (`target="_blank" rel="noopener"`).

2. **Step 2: Submit Rating & Review**
   - 2.1: Log in as `member@techscout.com`.
   - 2.2: Scroll down to "Write a Review" section on `/directory/tools/chatgpt`.
   - 2.3: Click 5th Star icon (`#star_rating_5`).
   - 2.4: Enter Review Text: `Indispensable tool for daily software architecture and refactoring tasks.` in `#review_body`.
   - 2.5: Click "Submit Review".

3. **Step 3: Verification & Average Rating Calculation**
   - 2.1: Verify record saved in `comments` / `reviews` table (`rating = 5`, `status = 'approved'`).
   - 2.2: Verify tool overall rating score updates dynamically (e.g. from `4.8` to `4.9`).
   - 2.3: Confirm user review appears at top of customer reviews feed.

---

## Test Suite 3.0: Prompt Directory & Copy Code Engine

### Test Case 3.1: Prompt Copying & Usage Counter
- **Objective**: Verify prompt template rendering, variable substitution, and copy-to-clipboard tracking.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: View Prompt Card**
   - 1.1: Navigate to `/directory/prompts`.
   - 1.2: Select prompt: `Senior Python Refactoring & Unit Test Generator`.
   - 1.3: Verify prompt text box displays variable placeholders (e.g. `[INSERT_CODE_HERE]`).

2. **Step 2: Variable Input & Copy Action**
   - 2.1: Fill input field `#var_insert_code` with `def add(a, b): return a + b`.
   - 2.2: Click "Copy Filled Prompt" button (`#btn_copy_prompt`).
   - 2.3: Verify button label changes to *"Copied to Clipboard!"* with toast popup.
   - 2.4: Paste into text editor and verify `[INSERT_CODE_HERE]` was replaced with user input.
   - 2.5: Verify `prompt_copies_count` increments by 1 in `prompts` table.

---

## Test Suite 4.0: Bookmark & History Tracking

### Test Case 4.1: Member Bookmarking Workflow
- **Objective**: Verify saving tools/prompts to user account bookmarks (`views/account/bookmarks.php`).

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Toggle Bookmark Icon**
   - 1.1: Log in as `member@techscout.com`.
   - 1.2: On tool card `Claude 3.5 Sonnet`, click bookmark bookmark icon (`#bookmark_toggle_12`).
   - 1.3: Verify icon state toggles to solid active state (`.is-bookmarked`).
   - 1.4: Confirm AJAX request `POST /api/v1/bookmarks/toggle` returns `{ "status": "added" }`.

2. **Step 2: Account Bookmarks Page Verification**
   - 1.1: Navigate to `/account/bookmarks`.
   - 1.2: Confirm `Claude 3.5 Sonnet` card is present in saved list.
   - 1.3: Click "Remove" -> Confirm item disappears from list and database row is removed from `user_bookmarks`.

---

## Pass / Fail Criteria Checklist

- [ ] Multi-filter combinations yield exact database query results without SQL syntax errors.
- [ ] Outbound referral links trigger click tracking events.
- [ ] Rating updates recalculate average score accurately.
- [ ] Prompt copy button substitutes dynamic variables cleanly.
- [ ] Bookmarks persist across user login/logout sessions.
- [ ] QA Inspector: TechScout Lead Frontend QA.
