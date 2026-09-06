# QA Test Suite 06: Legal Pages, Help Center, Editorial Standards & Mobile Responsiveness

## 1. Test Suite Metadata
* **Module:** Legal Suite (9 pages), Help Center (14 categories), Author Guidelines (11 sections), Editorial Standards (8 sections), About Page (8 sections), Mobile 2-Column Footer Layout
* **Target Routes:** `/privacy`, `/terms`, `/cookies`, `/community-guidelines`, `/copyright`, `/accessibility`, `/acceptable-use`, `/security`, `/disclosure`, `/help`, `/write/guidelines`, `/editorial`, `/about`

---

## 2. Detailed Test Cases

### TC-STATIC-01: Full Legal Suite Availability (9 Complete Pages)
* **Preconditions:** Public browser.
* **Test Steps:**
  1. Visit each of the 9 legal endpoints:
     - `/privacy` (Privacy Policy)
     - `/terms` (Terms of Service)
     - `/cookies` (Cookie Policy)
     - `/community-guidelines` (Community Code of Conduct)
     - `/copyright` (DMCA & Copyright Policy)
     - `/accessibility` (WCAG 2.1 AA Statement)
     - `/acceptable-use` (Acceptable Use Policy)
     - `/security` (Security Policy & Vulnerability Disclosure)
     - `/disclosure` (Affiliate & FTC Disclosure)
* **Expected Result:**
  - All 9 pages return HTTP 200 with complete, professional legal provisions, styled hero headers, and dynamic updated dates.

### TC-STATIC-02: Interactive Help Center (14 Categories & Live Search)
* **Preconditions:** Visiting `/help`.
* **Test Steps:**
  1. Verify presence of all 14 categories (Getting started, Account, Reading, Writing, Publishing, Authors, Tools, AI tools, Prompts, Membership, Payments, Privacy, Security, Reporting).
  2. Type "monetization" or "markdown" in the search box.
  3. Verify instant client-side card filtering.
* **Expected Result:**
  - Search immediately filters matching help cards without latency or page reload.

### TC-STATIC-03: Author Guidelines (`/write/guidelines`) & Editorial Standards (`/editorial`)
* **Preconditions:** Visiting `/write/guidelines` and `/editorial`.
* **Test Steps:**
  1. Verify all 11 publishing guideline sections (Who can publish, Content requirements, Quality standards, Formatting, Images, Citations, AI policy, Copyright, Prohibited content, Review process, Publishing process).
  2. Verify all 8 editorial standard sections (Editorial standards, Content guidelines, Fact-checking, Corrections policy, AI content policy, Review policy, Sponsored content policy, Disclosure policy).
* **Expected Result:**
  - Detailed prose renders with clean formatting, responsive typography, and navigation links.

### TC-STATIC-04: About TechScout (`/about`) & Metrics HUD
* **Preconditions:** Visiting `/about`.
* **Test Steps:**
  1. Verify all 8 corporate sections (What is TechScout, Mission, Vision, Values, Story, Team, Editorial principles, Contact).
  2. Verify live metrics counter cards (Articles, Verified Engineers, Curated AI Tools, Monthly Readers).
* **Expected Result:**
  - Dynamic counts query MySQL correctly; contact links and email addresses are clickable.

### TC-STATIC-05: Mobile Phone 2-Column Footer Layout Verification
* **Preconditions:** Device emulator or mobile screen width (375px &ndash; 768px).
* **Test Steps:**
  1. Open Chrome DevTools Device Mode (iPhone 14 / Pixel 7 / iPad Mini).
  2. Scroll to the footer on `/`, `/blog`, `/topics`, or `/about`.
  3. Verify the column grid layout.
* **Expected Result:**
  - The brand/social column spans full width across the top (`grid-column: 1 / -1;`), while the navigation columns (Intelligence, Creators, Platform) arrange in an elegant **2-column grid** (`grid-template-columns: 1fr 1fr;`) rather than an awkward single-column vertical stack.
