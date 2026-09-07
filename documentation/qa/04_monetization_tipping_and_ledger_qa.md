# QA Test Guide 04: Monetization, Tipping & Financial Ledger QA

This document specifies the step-by-step Enterprise QA testing suite for Creator Tipping (`app/Models/CreatorTipping.php`), Financial Ledger (`app/Models/FinancialLedger.php`), Author Tiers (`app/Models/AuthorTierEngine.php`), Creator Referral Rewards (`app/Models/CreatorReferral.php`), and Payout Processing.

---

## Pre-Test Setup & Requirements

| Requirement | Value / Condition |
| :--- | :--- |
| **Base URL** | `http://localhost/techscout.n/` |
| **Database State** | Ledger tables (`financial_ledger`, `creator_tips`, `author_rewards`, `payout_requests`) reset/initialized. |
| **Test Entities** | Fan/Supporter Member (`supporter@techscout.com`), Creator Author (`author@techscout.com`), Admin (`admin@techscout.com`). |

---

## Test Suite 1.0: Creator Tipping Workflow (`app/Models/CreatorTipping.php`)

### Test Case 1.1: Direct Tip Submission & Financial Ledger Recording
- **Objective**: Verify tip transaction processing, platform commission calculation, and ledger entries.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Supporter Direct Tip**
   - 1.1: Log in as `supporter@techscout.com`.
   - 1.2: Navigate to author public profile `/author/john-doe`.
   - 1.3: Click "Tip Creator" button (`#btn_open_tip_modal`).
   - 1.4: Select Tip Amount: `$25.00` in tip modal.
   - 1.5: Enter Support Note: `Thank you for your excellent PHP 8.3 guides!`.
   - 1.6: Click "Process Tip Payment".

2. **Step 2: Payment Gateway Mock Execution**
   - 2.1: Verify request POST to `/api/v1/tipping/process`.
   - 2.2: Confirm Stripe/PayPal Sandbox payment intent succeeds (`transaction_id = 'tx_mock_9928341'`).

3. **Step 3: Double-Entry Financial Ledger Checks (`app/Models/FinancialLedger.php`)**
   - 3.1: Query `financial_ledger` table for `transaction_id = 'tx_mock_9928341'`.
   - 3.2: Verify Gross Tip Amount: `$25.00` (`entry_type = 'TIP_GROSS'`).
   - 3.3: Verify Platform Fee (10% Commission): `$2.50` (`entry_type = 'PLATFORM_COMMISSION'`).
   - 3.4: Verify Net Creator Credit: `$22.50` (`entry_type = 'CREATOR_CREDIT'`).
   - 3.5: Confirm mathematical balance: `Gross ($25.00) = Platform Fee ($2.50) + Net Creator ($22.50)`.

4. **Step 4: Author Balance & Notification Verification**
   - 4.1: Log in as `author@techscout.com`.
   - 4.2: Open `/author/rewards` dashboard.
   - 4.3: Verify "Available Balance" increases by `$22.50`.
   - 4.4: Verify notification alert: *"You received a $25.00 tip from Supporter!"*.

---

## Test Suite 2.0: Author Tier Engine Rewards (`app/Models/AuthorTierEngine.php`)

### Test Case 2.1: Dynamic Author Tier Upgrade & Revenue Share Increase
- **Objective**: Verify that reaching view/publishing milestones automatically elevates author tier and bonus multiplier.

#### Tier Progression Matrix:
| Tier Level | Requirement Threshold | Revenue Share | Bonus Multiplier |
| :--- | :--- | :---: | :---: |
| **Bronze Author** | 0 - 5,000 Article Views | 70% | 1.0x |
| **Silver Author** | 5,001 - 25,000 Article Views | 75% | 1.1x |
| **Gold Author** | 25,001 - 100,000 Article Views | 80% | 1.25x |
| **Platinum Author**| 100,001+ Views | 85% | 1.5x |

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Simulate Traffic Milestone**
   - 1.1: Execute query to increment total article views for `author@techscout.com` to `26,000`.
   - 1.2: Trigger tier calculation worker `AuthorTierEngine::recalculateTier($author_id)`.

2. **Step 2: Tier Upgrade Verification**
   - 2.1: Verify database table `authors` updates `tier = 'gold'`.
   - 2.2: Confirm badge rendered in Author Studio changes to Gold Shield.
   - 2.3: Verify subsequent ledger calculations apply `80%` revenue share rate.

---

## Test Suite 3.0: Payout Request & Admin Settlement

### Test Case 3.1: Author Payout Request & Settlement
- **Objective**: Verify payout request validation, balance holding, and admin disbursement completion.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Submit Payout Request**
   - 1.1: Logged in as Author (Available balance: `$450.00`), navigate to `/author/rewards`.
   - 1.2: Enter Withdrawal Amount: `$300.00` in `#payout_amount`.
   - 1.3: Select Method: `PayPal` (`payout_email = 'author@paypal.com'`).
   - 1.4: Click "Request Payout".
   - 1.5: Verify available balance immediately decreases to `$150.00`, and pending balance shows `$300.00`.

2. **Step 2: Admin Approval & Financial Settlement (`views/admin/revenue.php`)**
   - 2.1: Log in as Admin (`admin@techscout.com`).
   - 2.2: Open `/admin/revenue#payouts`.
   - 2.3: Select pending payout request `#PAY-8823` ($300.00).
   - 2.4: Click "Approve & Mark Disbursed".
   - 2.5: Verify ledger entry created (`entry_type = 'PAYOUT_DISBURSED'`).
   - 2.6: Verify author pending balance clears to `$0.00`.

---

## Pass / Fail Criteria Checklist

- [ ] Double-entry ledger debits and credits match 100% to the cent ($0.00 variance).
- [ ] Platform commission is correctly deducted before creator payout allocation.
- [ ] Tier progression triggers automated threshold upgrades.
- [ ] Author payout requests lock funds to prevent double withdrawal attacks.
- [ ] QA Inspector: TechScout Lead Financial QA.
