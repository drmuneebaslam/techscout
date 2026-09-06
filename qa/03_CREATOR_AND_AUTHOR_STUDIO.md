# ✍️ QA Specification: Creator & Author Studio Screens

> **Scope**: Author studio dashboard, markdown publication editor, advanced content telemetry, external traffic sources, device breakdown, rewards balance, withdrawal requests, and ambassador referrals.  
> **Target Environment**: `http://localhost/techscout.n`  
> **Auth Session**: `?qa_auth=author` or login as `alexvance`  

---

## 📋 Author Studio Screen Matrix

| ID | Screen Title | Route Path | Core Modules Tested |
|---|---|---|---|
| 3.0 | Author Navigation Order | Desktop Dropdown & Mobile Nav | Prioritized links: New Post, Studio, Rewards, Analytics ... Sign Out |
| 3.1 | Creator Studio Dashboard | `GET /author/dashboard` | 30-day views, earnings, drafts, live articles, quick action buttons |
| 3.2 | Technical Article Editor | `GET /author/editor`, `/author/editor/{id}` | Markdown editor, live preview, tags, category, featured image upload, SEO |
| 3.3 | Content Performance Analytics | `GET /author/analytics/content/{id}` | Read retention, external inbound referrers, device breakdown, sentiment |
| 3.4 | Creator Telemetry Hub | `GET /author/analytics` | Aggregate author views, top performing articles, audience discovery |
| 3.5 | Rewards & Balance Ledger | `GET /author/rewards` | Current promo credits, payout withdrawal claim, banking/crypto methods |
| 3.6 | Partner Monetization Hub | `GET /author/rewards#monetization` | Readership criteria checklist, application submission, partner badge |
| 3.7 | Ambassador Referral Program | `GET /author/rewards#referrals` | Custom referral code/link, invited authors roster, 5% revenue commission |

---

## 🔍 Detailed Functional & Visual Test Cases

### Screen 3.0: Prioritized Author Dropdown Structure
1. **Verification of Navigation Order**:
   - Log in as an author (`?qa_auth=author`).
   - Click user profile avatar dropdown.
   - Verify requested order:
     1. `New Technical Post` (highlighted cyan `#38bdf8`)
     2. `Creator Studio`
     3. `Rewards & Balance`
     4. `Telemetry & Views`
     5. `Saved Bookmarks 🔖`
     6. `Reading History 🕒`
     7. `Communications & Inbox 📥`
     8. `Notifications Center`
     9. `Support & Helpdesk`
     10. `Personalize Feed & Topics 🌟`
     11. `Edit Creator Profile ⚙️`
     12. `Account Settings & Devices 🛡️`
     13. `Sign Out` (at bottom)

### Screen 3.2: Technical Publication Editor Studio (`/author/editor`)
1. **Markdown Authoring**:
   - Type article title and body using Markdown (code blocks, headings, callouts).
   - Click preview toggle &rarr; Verify real-time HTML formatting with syntax highlighting.
2. **Universal Image Uploading**:
   - In the featured image section, click *"Upload Image File"*.
   - Select an image file. Verify image is uploaded to `/public/uploads/` and URL automatically populated.
3. **Publishing & Drafts**:
   - Click *"Save Draft"* &rarr; Saves draft status.
   - Click *"Submit for Editorial Review"* &rarr; Transitions state to `pending_review` and routes to admin moderation station.

### Screen 3.3: Content Telemetry & External Traffic Discovery (`/author/analytics/content/{id}`)
1. **Audience Roster Isolation Invariant (🔒 Admin-Isolated Telemetry)**:
   - **Critical Rule**: The *"Audience & Readers Roster"* and *"Real-Time Active Readers Telemetry"* panels are strictly restricted to the Administrator Command Center (`/admin/analytics` and `/admin/engagement-studio`).
   - In the Creator Studio (`/author/analytics/content/{id}`), authors **must never** have access to unmasked platform subscriber identities or administrative telemetry feeds.
   - Authors view aggregated, privacy-compliant metrics:
     - Total Verified Reads and 30-Day Trendline.
     - Average Read Completion Percentage.
     - Inbound Traffic Sources Breakdown.
     - Device Form Factor Distribution.
     - Reader Sentiment and Qualitative Reaction Summary.
2. **External Inbound Traffic Sources (🌐 Reach Tab)**:
   - Click tab *"Reach & Discovery"*.
   - Verify table displays clean channel taxonomy:
     - `google.com` &bull; Organic Search (`#38bdf8`)
     - `github.com` &bull; Developer Repos (`#818cf8`)
     - `x.com` &bull; Social Radar (`#06b6d4`)
     - `news.ycombinator.com` &bull; Tech Aggregator (`#fb923c`)
     - `direct` &bull; Direct / Bookmarks (`#34d399`)
   - Verify visual progress bars and percentage share badges (e.g. `44%`, `22%`).
   - Confirm empty state fallback ensures creators always see rich inbound metrics.
3. **Device Form Factors Breakdown (📱)**:
   - Verify donut chart renders Desktop, Mobile, and Tablet slices.
   - Verify the 3 stat metric chips beneath the chart:
     - Desktop: view count and % share
     - Mobile: view count and % share
     - Tablet: view count and % share
4. **10-Point Scroll Retention Curve**:
   - Verify drop-off graph shows reader retention at 25%, 50%, 75%, and 100% completion.

### Screen 3.4: Creator Telemetry Hub, Pure Numerical Followers & Profile List Privacy (`/author/analytics`)
1. **Follower Count Display Integrity & Public Counters**:
   - Creators view their verified follower counter on their dashboard and public profile.
   - **Zero User Account Pollution Invariant**: Follower counts reflect authentic follows combined with verified administrative simulation boosts (`authors.follower_count`).
   - No fake or ghost accounts are ever added to the creator's follower roster, maintaining personal account database hygiene.
2. **Strict Profile List Privacy Invariant (🔒 Admin-Only Audit Access)**:
   - **Rule**: While follower and following totals are publicly visible as numerical badges for social proof, the actual **lists of follower/following user profiles are strictly hidden from the public and regular creators**.
   - Public visitors and regular authors cannot view follower tabs or inspect member profiles.
   - Only platform administrators (`\App\Core\Auth::isAdmin()`) can audit raw follower profiles.
   - API endpoints `GET /api/authors/{id}/followers` and `GET /api/authors/{id}/following` return `403 Forbidden` for all non-admin requests.

### Screen 3.5: Creator Rewards, Tax Deductions & Payout Ledger (`/author/rewards`)
1. **Author Profile Modal Payout Separation**:
   - Payout destinations and banking credentials are **excluded from the Author Profile Edit Modal**.
   - Banking, PayPal, Crypto, and custom withdrawal destinations are managed exclusively within the **Royalties & Ledger** tab (`/author/dashboard?tab=payouts`).
2. **Balance & Withdrawal Claim Workflow**:
   - Verify Promotional Credits balance in USD.
   - Click *"Request Withdrawal 💳"*.
   - Opens withdrawal modal displaying real-time fee calculation breakdown.
3. **Dynamic Multi-Tier Platform & Bank Fee Deductions**:
   - The platform dynamically applies fee percentages configured by the administrator (`FinancialLedger::getPayoutFeeSettings()`):
     - **Platform Commission**: Dynamic percentage (default **5.00%**) deducted from gross withdrawal amount.
     - **Bank Remittance Fee**: Dynamic percentage (default **2.50%**) applicable to bank transfers and methods with bank fee policy.
     - **Bank Wire Surcharge**: Dynamic flat fee (default **$5.00 USD**) processing surcharge for SWIFT/wire transfers.
   - **Formula**:
     $$\text{Platform Fee} = \text{Amount} \times \left(\frac{\text{Platform Fee \%}}{100}\right)$$
     $$\text{Bank Fee} = \text{Amount} \times \left(\frac{\text{Bank Fee \%}}{100}\right) + \text{Wire Surcharge} \quad (\text{if Bank Wire chosen})$$
     $$\text{Net Payout} = \text{Amount} - \text{Platform Fee} - \text{Bank Fee}$$
4. **Dynamic & Custom Payout Methods Support**:
   - The withdrawal claim modal and link destination modal dynamically render all payout methods enabled by the administrator (Bank Wire, PayPal, Crypto USDC, Stripe, Wise, and custom methods such as Revolut or Payoneer).
   - Each method adheres to its configured fee policy (`standard`, `bank`, or `none`).
4. **Historical Payout Ledger**:
   - Every submitted payout is rendered in the creator's historical payouts table:
     - Date Requested, Payout Method & Destination Account, Gross Amount, Itemized Taxes Deducted (`tax_deducted` + `bank_tax_deducted`), Net Amount, Status badge (`pending`, `approved`, `rejected`), Proof Document download link (if approved).
   - Table supports client-side pagination with debounced search and page size selection.
