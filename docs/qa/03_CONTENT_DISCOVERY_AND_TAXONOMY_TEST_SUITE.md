# QA Test Suite 03: Content Discovery, 100+ Topics & Tool Directories

## 1. Test Suite Metadata
* **Module:** Public Discovery, 100+ Categories Taxonomy, AI Tools Radar, Prompts Library, Custom GPTs, Challenges, Jobs Board
* **Target Routes:** `/`, `/blog`, `/topics`, `/topic/{slug}`, `/tools`, `/prompts`, `/gpts`, `/campaigns`, `/jobs`

---

## 2. Detailed Test Cases

### TC-DISCOVERY-01: Topics Hub Light & Dark Theme Adaptability
* **Preconditions:** Public user visiting `/topics`.
* **Test Steps:**
  1. Load `/topics` in Dark Mode. Inspect top hero card and subcategory badge pills.
  2. Click the Moon/Sun theme toggle to switch to Light Mode.
  3. Verify text contrast, search input background, card backgrounds, and category pill borders.
* **Expected Result:**
  - Hero card adapts to `var(--ts-bg-surface)` without black `#090d16` background artifacts; all typography is crisp and legible in both Light and Dark themes.

### TC-DISCOVERY-02: Instant Live Topic & Category Filter
* **Preconditions:** Viewing `/topics`.
* **Test Steps:**
  1. Type `Kubernetes` or `Prompt` in the search box.
  2. Click on quick filter pills (e.g. `System Architecture`, `Artificial Intelligence`).
* **Expected Result:**
  - Topic grid instantly filters matching parent domains and subcategories in real time with 0 page reloads.

### TC-DISCOVERY-03: Header Navigation Link Active State Verification
* **Preconditions:** Viewing any page (e.g. `/`, `/blog`, `/tools`).
* **Test Steps:**
  1. Observe the "Topics" navigation link in the top desktop header, mobile drawer, and user profile dropdown.
  2. Verify that "Topics" does NOT appear permanently highlighted in blue/indigo when on the Home or Articles page.
  3. Navigate to `/topics` or `/topic/architecture`.
* **Expected Result:**
  - Nav link behaves consistently with all other links and only highlights when the active route matches `/topics` or `/topic/*`.

### TC-DISCOVERY-04: AI Tools & Custom GPT Listings
* **Preconditions:** Viewing `/tools` and `/gpts`.
* **Test Steps:**
  1. Filter tools by pricing type (Free, Freemium, Paid).
  2. Click external product link and verify outbound redirection.
  3. Copy prompt blueprint in `/prompts` and verify clipboard copy confirmation toast.
* **Expected Result:**
  - Fast filtering, prompt clipboard copying works seamlessly across all devices.
