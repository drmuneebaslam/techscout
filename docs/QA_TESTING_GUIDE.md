# TechScout Comprehensive QA Testing Guide

This manual QA testing guide is formulated from the actual platform architecture, database structure, and Git logs of the TechScout project. It provides specific, manual execution scenarios.

## 1. Authentication & Security
### Test Case #1: Login with MySQL 8 Strict Dates
* **Objective:** Ensure the platform handles legacy users where `last_login` might be `0000-00-00 00:00:00`.
* **Execution:** Access `views/pages/qa_testing.php` -> Click "Login: Standard View". Use a legacy test account.
* **Expected:** User logs in successfully without throwing `1525 Incorrect DATETIME value`.

### Test Case #2: Re-entrancy Debounce Guard on Theme Toggle
* **Objective:** Ensure rapid clicking of the Theme Toggle does not desync the UI.
* **Execution:** Click the Moon/Sun icon in the navbar 10 times rapidly.
* **Expected:** The theme switches only once or twice based on the debounce timer, avoiding visual "flicker" and preventing JavaScript call stack errors.

### Test Case #3: OTP Post-Signup Verification
* **Objective:** Ensure the `user.registered` event trigger fires the correct Mail Template.
* **Execution:** Register a new user with a valid email. Check the database `mail_logs` table for successful dispatch. Enter the OTP on the verification screen.
* **Expected:** Account status changes to active.

## 2. Directory & Taxonomies
### Test Case #4: Strict Entity Separation (Categories vs Subcategories)
* **Objective:** Validate that assigning a Tool to a Subcategory does not incorrectly bind it to the Root Category ID.
* **Execution:** Go to Admin -> Categories. Create Root "AI". Create Sub "Text". Assign a dummy tool exclusively to "Text".
* **Expected:** Tool does not appear in unrelated root categories; breadcrumbs render `Home > AI > Text`.

### Test Case #5: Dual-Theme Admin CSS Inheritance
* **Objective:** Ensure PJAX transitions do not break CSS dual-theme scoping.
* **Execution:** Switch to Dark Mode. Click Admin Hub. Click Database Manager.
* **Expected:** Backgrounds remain `#111827`, text `#f9fafb`. No flash of white light during the page transition.

### Test Case #6: Image Prompts Visual Grid Rendering
* **Objective:** Verify pure visual masonry layout handles mismatched aspect ratios.
* **Execution:** Upload 3 image prompts (16:9, 1:1, 4:3).
* **Expected:** Grid cascades fluidly without stretching images. Missing `p.aspect_ratio` column error (SQL 1054) does not appear.

## 3. Database Manager & Error Forensics
### Test Case #7: Zero-Crash FTP Backup Exporter
* **Objective:** Verify the SQLite/SQL dump export works without memory limit crashes.
* **Execution:** Navigate to Admin -> Database Manager. Click Export.
* **Expected:** Browser downloads a `.sql` file completely. Server memory does not spike above 128MB.

### Test Case #8: Silent Telemetry Insertion
* **Objective:** Verify that `executeSilent()` successfully traps primary key collisions without throwing 500 errors.
* **Execution:** Refresh a specific Article page 5 times rapidly.
* **Expected:** Only 1 view is recorded per session/IP timeframe. The user sees no database exception on the frontend.

## 4. Mail Studio & Automations
### Test Case #9: Mail Envelope & Variables Rendering
* **Objective:** Ensure `{{user.name}}` parses accurately inside the iframe preview.
* **Execution:** Open Admin -> Mail Studio -> Template Editor. Enter `Hello {{user.name}}`.
* **Expected:** The live IFrame updates to show "Hello John Doe" (mock data). The envelope header and footer automatically wrap the content.

### Test Case #10: Webhook Delivery & Retry
* **Objective:** Verify the outbound webhook retry mechanism.
* **Execution:** Configure a webhook to an invalid domain (e.g., `http://localhost:9999/null`). Trigger an event. Go to Webhooks logs. Click "Retry Delivery".
* **Expected:** The system logs a timeout or connection refused, and increments the retry count.

## 5. Monetization & Ads
### Test Case #11: Fixed Crypto Wallet Tipping
* **Objective:** Verify Author Studio tip configuration.
* **Execution:** Login as Author. Go to Creator Studio -> Tip Wallets. Enter a valid BTC address.
* **Expected:** The public profile now displays the "Tip Creator" button. Submitting a tip shows the admin-configured instructions and saves the TXID.

### Test Case #12: Ad Popunder Triggers
* **Objective:** Verify ad popunders trigger exactly once per session.
* **Execution:** Enable popunder ads in Admin. View a Prompt Blueprint. Click "Copy Prompt".
* **Expected:** Ad opens in a new under-tab. Subsequent clicks do not trigger ads until the session resets.

## 6. Miscellaneous 
### Test Case #13: QA Dashboard Removal Validation
* **Objective:** Ensure normal users cannot access QA features visually.
* **Execution:** View the site as a guest or standard member. Look at the navbar.
* **Expected:** The "🧪 QA Testing" button is completely absent.

### Test Case #14: 404 Telemetry Logging
* **Objective:** Ensure broken links are caught.
* **Execution:** Visit `https://yourdomain.com/this-does-not-exist-12345`.
* **Expected:** 404 page is rendered. The Admin -> Error Forensics dashboard logs the requested path and IP.

---
*(Analysis compiled from comprehensive system review and TechScout Git history logs)*
