# 💰 QA Specification: Financial Revenue Distribution & Universal Ledger

> **Scope**: Creator Royalty Pool Distribution Engine, Searchable Balance Adjustment & Clawbacks, Double-Entry Financial Ledger, Payout Approvals, and Partner Monetization.  
> **Target Environment**: `http://localhost/techscout.n`  
> **Route**: `/admin/revenue`  
> **Auth Session**: `?qa_auth=admin`  

---

## 📋 Revenue & Financial Engine Screen Matrix

| Tab Key | Module Title | Primary Functionality Tested |
|---|---|---|
| `payouts` | Payout Requests & Queue | Review withdrawal claims, adjust fee, attach proof PDF/PNG, approve or decline |
| `pool` | **Royalty Pool Distribution Engine** | **Gross earnings, maintenance cut %, $0.80 base CPM, view-based calculations, reserve retention, editable preview table** |
| `grant` | **Creator Balance Adjustment & Clawback** | **Searchable recipient by name/slug/email/ID, Credit (+) vs Deduct (-), double-entry ledger** |
| `ledger` | Universal Financial Ledger | Immutable double-entry ledger records, balance tracking, transaction filters |
| `monetization` | Creator Monetization Partners | Partner applications review, approve, reject with feedback modal |
| `referrals` | Ambassador Growth Referrals | 5% commission ledger on referred creators' revenues |
| `settings` | Monetization Parameters | Global platform fee %, minimum withdrawal thresholds, auto-approval rules |
| `export` | CSV Ledger Export | Direct CSV download of full financial ledger |

---

## 🔍 Deep-Dive Test Cases: Royalty Pool Distribution (`/admin/revenue?tab=pool`)

### 1. Mathematical Logic & Platform Safeguard Invariants
- **Platform Gross Earnings ($G)**: Input by admin (e.g. `$1,000.00 USD`).
- **Platform Maintenance Cut ($M)**: Configured as a percentage (e.g. `25%` = `$250.00 USD`), retained for hosting, infrastructure, and team operations.
- **Available Creator Pool ($P)**: `$G - $M = $750.00 USD`.
- **Base CPM ($0.80 USD)**: Every verified 1,000 reads guarantees `$0.80 USD` base royalty payout.
- **The Core Anti-Drain Safeguard**:
  - *Problem*: If platform earnings are $1,000, but only 1 eligible author exists with 2,500 reads, paying them the entire remaining $750 would result in an absurd $300 CPM and deplete platform reserves!
  - *Solution*: Author payout is strictly bounded by their verified readership:
    $$\text{Base Payout} = \frac{\text{Reads}}{1,000} \times \text{Base CPM}$$
    A dynamic pool bonus is applied when surplus is available, but capped by the configurable **Max CPM Dynamic Cap** (e.g. `$2.50 CPM`).
  - *Result*: The author receives $2.50 \times 2.5 = \$6.25 \text{ USD}$.
  - The remaining $\$743.75 \text{ USD}$ is marked as **Platform Reserve Retained / Treasury**!

### 2. Interactive Telemetry Display Ribbon
Verify that changing Gross Revenue, Platform Cut %, or Base CPM instantly updates:
1. `disp_gross_rev` &rarr; `$1,000.00`
2. `disp_maintenance_cut` &rarr; `$250.00 (25%)`
3. `disp_creator_pool` &rarr; `$750.00`
4. `disp_total_disburse` &rarr; Dynamic sum of all checked author allocations
5. `disp_reserve_retained` &rarr; `Creator Pool - Total Disbursed` (safe surplus)
6. `disp_effective_cpm` &rarr; Dynamic effective CPM across active reads

### 3. Pre-Disbursal Review Table with Editable Fields
1. **Row Elements**:
   - Checkbox: Check or uncheck to include/exclude specific creators from the batch.
   - Creator identification: Avatar, Display Name, Handle, Email, Author ID.
   - Monetization Status badge.
   - Verified Reads count.
   - Base Payout ($0.80 CPM) & Pool Bonus.
   - **Editable Amount Input (`.pool-editable-amount`)**: Admin can type ANY custom dollar amount (e.g. change `$3.50` to `$10.00`).
   - **Custom Memo Input (`input[name="payouts[ID][memo]"]`)**: Description stamped on the author's ledger entry.
2. **Interactive Edit Sync**:
   - Manually modify any author's amount.
   - Verify `updateManualDisbursalTotal()` triggers immediately, updating *"Total to Disburse"* and *"Platform Reserve Retained"*.
3. **Execution & Ledger Verification**:
   - Click *"Execute & Disburse Royalty Pool ⚡"*.
   - Confirm browser confirmation dialog summarizing total payout, author count, and retained reserve.
   - Verify:
     1. Each author's `promo_credits` balance increases by the exact allocated amount.
     2. A corresponding record is created in `financial_ledger` with category `pool_distribution`.
     3. An in-app notification is sent to each creator.
     4. Admin is redirected to `/admin/revenue?tab=ledger` with a success banner.

---

## 🔍 Deep-Dive Test Cases: Searchable Balance Adjustment (`/admin/revenue?tab=grant`)

### 1. Multi-Identifier Live Search Input
- In the balance adjustment form under *"Target Creator Recipient"*:
- Type in `#adj_author_search`:
  1. **By Name**: Type `Alex` &rarr; Filters list to show Alex Vance.
  2. **By Handle**: Type `@janedoe` &rarr; Filters list to show Jane Doe.
  3. **By Author ID**: Type `#1` &rarr; Filters list to author #1.
  4. **By Email**: Type `author@` &rarr; Matches creator's associated email address.
- Click the matching `<option>`. Verify selector updates selection and `#creator_match_count` reports exact match count.

### 2. Credit (+) vs Deduct (-) Operation
- Select **🟢 Credit Balance (+)**:
  - Input Amount: `$50.00`.
  - Category: `editorial_grant`.
  - Reason: `Q3 Architecture Deep-Dive Editorial Grant`.
  - Preview card `#adj_calc_card` displays glowing green injection preview: `+$50.00 USD`.
- Select **🔴 Deduct / Clawback Balance (-)**:
  - Input Amount: `$25.00`.
  - Category: `clawback_violation`.
  - Reason: `Correction of duplicate balance allocation`.
  - Preview card displays red deduction preview: `-$25.00 USD`.
- Submit form &rarr; Verify balance is updated in `authors` table and logged in `financial_ledger`.

---

## 🔍 Deep-Dive Test Cases: Admin Payout Management & Dynamic Fee Engine (`/admin/revenue?tab=settings`)

### 1. Dynamic Fee Percentages & Fixed Surcharges
Admin can configure platform and bank transaction fees directly from the Revenue Console:
1. **Platform Fee Percentage**:
   - Configurable percentage (e.g. `5.0%`) levied on all gross creator withdrawals.
2. **Bank Transaction & Remittance Fee Percentage**:
   - Configurable percentage (e.g. `2.5%`) levied on bank wires, SEPA, and methods marked with bank fee policy.
3. **Fixed Bank Wire Surcharge**:
   - Flat fee (e.g. `$5.00 USD`) assessed on SWIFT / international wires.
4. **Live Interactive Calculation Preview**:
   - As inputs change, the interactive calculation card immediately recalculates gross claim, platform fee, bank fee, total deductions, and net creator payout.

### 2. Payout Methods & Custom Disbursement Engine
Admin can enable, disable, and add custom payout methods linked by creators:
1. **Allowed Methods Matrix**:
   - Checkboxes to toggle standard system methods: PayPal, Bank Wire / SEPA, USDC Crypto, Stripe Express, Wise.
   - Shows badge for fee policy: `Platform Commission Only (5%)`, `Platform (5%) + Bank (2.5% + $5)`, or `Zero Commission (0%)`.
2. **Custom Payout Method Provisioning**:
   - Admin can register custom disbursement methods (e.g. `revolut`, `payoneer`, `monzo`).
   - Fields: Method Key (slug), Display Name, Method Icon / Emoji, Fee Policy, and Instructions.
   - Automatically becomes available in the Creator Dashboard for authors to link and request payouts.

---

## 🔍 Deep-Dive Test Cases: Payout Requests & Multi-Tier Tax Deductions (`/admin/revenue?tab=payouts`)

### 1. Multi-Tier Tax Deduction Policy & Calculation
All creator withdrawal requests are audited through a transparent 3-tier tax and fee structure:
1. **Base Platform Fee (Dynamic %)**:
   - Platform operations fee deducted from gross requested payout based on admin settings.
2. **Intermediary Bank Withholding Tax (Dynamic %)**:
   - When payment destination is `Bank Wire / SWIFT` or international cross-border transfer, banking withholding fee is assessed.
3. **Fixed Wire Transfer Surcharge ($5.00 USD)**:
   - Fixed processing surcharge deducted for SWIFT/wire transfers.
4. **Calculated Payout Summary**:
   - Gross Amount: `$200.00 USD`
   - Platform Commission: `-$10.00 USD (5%)`
   - Bank Withholding Tax: `-$5.00 USD (2.5%)`
   - Fixed Wire Surcharge: `-$5.00 USD`
   - **Net Payout to Creator**: `=$180.00 USD`

### 2. Admin Payout Review & Approval Modal
- **Trigger**: Click *"Review Claim"* in the pending payouts table.
- **Modal Display**:
  - Displays Creator details, requested gross amount, and selected destination (Bank / PayPal / Crypto / Custom).
  - Itemized deduction breakdown showing Base Tax, Bank Tax, and calculated Net Payout.
  - Admin can adjust or override fee fields if banking agreements dictate alternative withholding rates.
  - File upload input for official bank transfer receipt or transaction proof (PDF, PNG, JPG).
  - Internal administrative note field.
- **Approval Execution (`POST /admin/revenue/payout/approve`)**:
  - Updates `author_payouts.status = 'approved'`.
  - Saves `tax_deducted`, `bank_tax_deducted`, and `net_amount` into `author_payouts` and records corresponding double-entry row in `financial_ledger`.
  - Dispatches email and in-app notification to creator with payout breakdown.

### 3. Universal Client-Side Table Pagination on Financial Modules
- **Payouts Queue & Historical Ledger Tables**:
  - Both tables feature interactive pagination (`.admin-table` standard).
  - Configurable page sizing: `10`, `25`, `50`, `100` records.
  - Debounced search filter allowing instant search by creator name, reference number, transaction ID, or status.
  - Real-time entry counter: `"Showing 1 to 10 of 48 entries"`.

### 4. Engagement Simulation & Analytics Verification
- Simulated read counts (e.g. setting an article to 200,000 reads) are accurately reflected across all public screens:
  - Article reading page (`/article/{slug}`): Shows `200,000 reads` badge.
  - Homepage featured and recent cards: Displays `200,000 reads`.
  - Blog editorial index (`/blog`): Displays `200,000 reads`.
  - Author profile page (`/author/{slug}`): Total reads stat telemetry card and article cards show `200,000 reads`.
  - Zero fake member accounts are generated; real user tables remain completely pristine.
