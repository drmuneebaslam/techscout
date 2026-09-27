# TechScout Comprehensive Testing Checklist: All Devices & Dual Themes

## 1. Overview & Quality Mandate
Every UI component, modal dialog, and content presentation engine across TechScout must look and feel exceptionally premium and behave flawlessly across all screen viewports and both system color themes (Dark Mode and Light Mode).

---

## 2. Device Breakpoints Verification Matrix

| Device Category | Target Viewport | Key Focus Areas | Test Method |
| :--- | :--- | :--- | :--- |
| **Ultra-Wide Desktop** | 2560 x 1440 | Max-width constraints, gallery aspect ratios, admin topbar layout | Chrome DevTools Responsive / Full Screen |
| **Standard Desktop** | 1920 x 1080 | Two-column split layouts, sticky sidebars, live API sandbox drawer | Native 1080p display |
| **Laptop / Notebook** | 1366 x 768 / 1440 x 900 | Modal scroll bounds, header breadcrumb wrapping, code block wrapping | Laptop display / DevTools |
| **Tablet Portrait** | 768 x 1024 | Admin navigation drawer collapse, card grid reorganization (2 cols) | DevTools iPad mini / Air |
| **Mobile Large** | 414 x 896 (iPhone 11/XR) | Prompt unlock action bar, gallery thumbnail scrolling, modal touch targets | DevTools iPhone XR |
| **Mobile Standard** | 375 x 812 (iPhone 13/14) | Variable input fields, code block copy buttons, breadcrumb trail overflow | DevTools iPhone 12/13/14 |
| **Mobile Compact** | 360 x 640 (Android Low-End) | Typography scaling, horizontal table overflow scroll wrappers | DevTools Galaxy S8 |

---

## 3. Dual Theme (Dark & Light) Visual Audit Checklist

### Prompt Single Page (`views/directory/prompt_single.php`)
- [ ] **Dark Mode (Default)**:
  - Hero gallery background uses obsidian glass `#090d16`.
  - Main caption text is sharp `#f8fafc` with subtle obsidian drop shadow.
  - Variable inputs have elevated surface contrast and `#38bdf8` active borders.
  - Unlock action bar uses vibrant gradient with high contrast text.
- [ ] **Light Mode (`[data-theme="light"]` / `.theme-light`)**:
  - Hero gallery background switches cleanly to `#f1f5f9` frosted slate.
  - Main caption text switches to `#0f172a` deep midnight slate with white frosted background.
  - No harsh pitch-black backgrounds on code blocks or card headers.
  - Variable inputs use clean border lines and light surface backgrounds.

### Admin Ingestion Webhooks (`views/admin/incoming_webhooks.php`)
- [ ] **Modal: New Webhook Ingestion**:
  - Backdrop blur active in both themes.
  - Input labels legible in dark mode (`#f8fafc`) and light mode (`#0f172a`).
  - Table select dropdown reflects system colors.
- [ ] **Modal: Edit Webhook & Live Field Mapper**:
  - 4 tabs (General, Captured Payloads, Field Mapping, Simulator) responsive across desktop & mobile.
  - Dynamic field mapping rows do not wrap awkwardly on tablet/mobile.
- [ ] **Modal: All Tables Sample Payload Generator**:
  - JSON code preview wraps cleanly without horizontal overflow breaks.
  - 1-click copy buttons provide instant visual toast / feedback.
- [ ] **Modal: Comprehensive Ingestion Tutorial**:
  - Code snippets formatted with legible contrast in both dark and light modes.

### Custom API Gateway & Introspector (`views/admin/custom_api_endpoints.php`)
- [ ] **Dynamic Table Introspection Panel**:
  - Columns table has horizontal scroll wrap (`overflow-x: auto`) for mobile viewports.
  - Badges for data types, nullable indicators, and primary keys legible in light mode.
- [ ] **Modal: Relational Multi-Table Query Builder**:
  - Checkboxes for `contents`, `categories`, `users` easy to toggle on touchscreen devices.
  - Endpoint preview URL truncates safely with copy helper.
- [ ] **Modal: Create & Edit Custom API Endpoint**:
  - Dynamic fields for `table_data` vs `table_schema` vs `custom_json` toggle without jumping.

---

## 4. Monetization & Interactive UX Test Flow

### Prompt Single Page Unlock & Ad Trigger Flow:
1. Navigate to any prompt single page (e.g. `/prompt/b-w-cinematic-reflective-sunglasses-ai-prompt`).
2. Verify `.ts-code-copy-btn` is **NOT present** in the top-right corner on initial page load.
3. Verify the prompt box is clipped to ~140px with a gradient blurred fade overlay.
4. Click the large **"Unlock Full Blueprint & Copy Prompt 🚀"** button (`#btn-copy-prompt`).
5. **Expected Behavior**:
   - The configured ad URL (`https://omg10.com/4/11737440`) opens in a new background browser tab.
   - The current prompt single page stays active.
   - The preview container expands smoothly to show the full prompt blueprint.
   - The prompt text is automatically copied to the clipboard.
   - Button text switches to **"✓ Prompt Copied to Clipboard! 🎉"**.
   - Top-right copy button (`#prompt-box-top-copy-btn`) appears in the prompt box for subsequent 1-click copies.

---

## 5. Performance & Regression Checklist

- [ ] **No SQL Syntax Errors**: MariaDB PDO queries with `LIMIT` must pass integer casts without quotes.
- [ ] **No Missing Columns**: `categories.updated_at`, `author_tips.supporter_user_id`, and `ai_tools.slug` must resolve without exceptions.
- [ ] **CloudFront Edge Headers**: Guest GET requests must return `Cache-Control: public, max-age=60, s-maxage=300`.
- [ ] **Keyset Cursor Pagination**: Comment feeds load with `before_id` cursor in under 2ms.
