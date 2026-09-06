# QA Test Suite 04: Creator Monetization, Bounties & Payouts Engine

## 1. Test Suite Metadata
* **Module:** Creator Monetization, Viral View Milestones, Editorial Grants, Author Payout Approvals
* **Target Routes:** `/author/rewards`, `/admin/revenue`, `/admin/revenue/grant-reward`, `/admin/revenue/process-payout`, `/admin/revenue/save-settings`, `/admin/revenue/export`

---

## 2. Detailed Test Cases

### TC-REVENUE-01: Granting Creator Rewards & Editorial Bonuses
* **Preconditions:** Logged in as Admin on `/admin/revenue`.
* **Test Steps:**
  1. Navigate to the "Grant Reward / Bounty" tab (`#revtab_grant`).
  2. Select an author from the dropdown roster.
  3. Choose reward category (e.g. `Editorial Excellence Grant ($)` or `Viral View Milestone Bounty ($)`).
  4. Enter amount `$150.00` and description "Award for Outstanding Distributed Systems Blueprint".
  5. Submit form.
* **Expected Result:**
  - Author's promo credit balance immediately increases by `$150.00`.
  - Reward grant is permanently recorded in the `revenue_records` table and listed on the reward history ledger.

### TC-REVENUE-02: Author Payout Request & Credit Deduction
* **Preconditions:** Logged in as verified author with > $50 balance on `/author/rewards`.
* **Test Steps:**
  1. Submit a withdrawal request for `$75.00` via PayPal to `author@engineer.io`.
  2. Verify that `$75.00` credits are immediately deducted from the author's available balance.
  3. Log in as Admin and visit `/admin/revenue`.
* **Expected Result:**
  - New pending payout request appears in the Admin Payouts table with status `PENDING`.

### TC-REVENUE-03: Admin Payout Approval & Disbursement Workflow
* **Preconditions:** Pending payout record in `/admin/revenue`.
* **Test Steps:**
  1. Click "⚡ Process Payout" on the pending request.
  2. Enter Transaction Wire Reference (e.g. `PP-TX-849201948`) and change status to `PAID`.
  3. Click "Confirm Payout Status".
* **Expected Result:**
  - Payout status transitions to `PAID` with green badge, transaction reference is saved, and `processed_at` timestamp is recorded.

### TC-REVENUE-04: Payout Rejection & Automatic Credit Refund
* **Preconditions:** Pending payout record.
* **Test Steps:**
  1. Open Process Payout modal and select `REJECT & REFUND CREDITS`.
  2. Submit form.
* **Expected Result:**
  - Payout record marked as `REJECTED`, and the deducted credits are automatically refunded back to the author's account balance.
