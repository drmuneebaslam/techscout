# 60. End-to-End Core System Workflows

## Overview
This document provides an exhaustive, step-by-step operational breakdown of all major end-to-end user workflows in TechScout. Each workflow maps the full lifecycle across HTTP Controllers, Core Engines, Data Models, Database Schemas, and Quality Assurance Verification steps.

---

## Workflow 1: Complete Author Article Submission, Review & Monetization Lifecycle

```
Author Article Lifecycle
┌─────────────────────────────────────────────────────────┐
│ 1. Creator Opens Creator Studio Editor                  │
│    GET /author/studio/editor                            │
├─────────────────────────────────────────────────────────┤
│ 2. Uploads Word .docx or Composes WYSIWYG Content       │
│    DocxParser::parseToHtml() -> Generates Formatted HTML│
├─────────────────────────────────────────────────────────┤
│ 3. Automated Plagiarism Scan Execution                  │
│    PlagiarismEngine::scanArticleContent()               │
│    - Evaluates n-gram token similarity                      │
│    - If Similarity > 30%: Flags for editorial alert     │
├─────────────────────────────────────────────────────────┤
│ 4. Article Saved as Pending Submission                  │
│    POST /author/studio/editor -> status = 'pending'     │
├─────────────────────────────────────────────────────────┤
│ 5. Staff Editor Reviews in Admin Content Queue          │
│    GET /admin/content-queue                             │
├─────────────────────────────────────────────────────────┤
│ 6. Editor Approves Article                              │
│    POST /admin/content-queue/review (action=approve)    │
│    - Status updated: status = 'published'               │
│    - Author Reputation Score: +50 Points                │
│    - AuthorTierEngine::evaluateAuthorTier() Executed    │
│    - Author Tier & CPM Multiplier Updated               │
├─────────────────────────────────────────────────────────┤
│ 7. Readers Read Article & Pageviews Recorded            │
│    GET /blog/{slug} -> Analytics::recordPageView()      │
├─────────────────────────────────────────────────────────┤
│ 8. Financial Ledger Calculates Creator Earnings         │
│    FinancialLedger::recordEntry() -> Credit Balance     │
└─────────────────────────────────────────────────────────┘
```

### Detailed Component Mapping:
1. **Frontend Editor**: `views/author/editor.php` utilizes Quill/TinyMCE WYSIWYG editor.
2. **Word Upload Component**: Handled by `DocxParser.php`, unzipping `.docx` binary XML archives and mapping `<w:p>` and `<w:r>` XML tags to standard HTML `<p>`, `<strong>`, `<h2>`, and `<ul>` elements.
3. **Plagiarism Check**: `PlagiarismEngine.php` tokenizes input text into 4-gram sequences, comparing hash collisions against published articles in `contents`.
4. **Tier & Earnings Calculation**: When pageviews accumulate, `Analytics.php` updates views, and `FinancialLedger.php` multiplies base CPM ($2.00 per 1,000 views) by the author's `cpm_multiplier` (e.g. 1.40x for Gold League), crediting the net earnings to `authors.withdrawable_balance`.

---

## Workflow 2: Member Patronage Creator Tipping Workflow

```
Creator Tipping Flow
┌─────────────────────────────────────────────────────────┐
│ 1. Reader clicks "Tip Author ($5.00)" on Article        │
│    GET /blog/{slug}                                     │
├─────────────────────────────────────────────────────────┤
│ 2. Tipping Modal Opens & Amount Selected                │
│    POST /api/v1/tipping/send                            │
├─────────────────────────────────────────────────────────┤
│ 3. FinancialLedger Executes Transaction                 │
│    FinancialLedger::recordEntry()                       │
│    - Gross Amount: $5.00                                │
│    - Platform Fee (5%): $0.25                           │
│    - Net Amount to Author: $4.75                        │
├─────────────────────────────────────────────────────────┤
│ 4. Author Balance Updated                               │
│    UPDATE authors SET withdrawable_balance += $4.75     │
├─────────────────────────────────────────────────────────┤
│ 5. Notification & Email Sent to Creator                 │
│    NotificationHelper::send() & Mailer::send()          │
└─────────────────────────────────────────────────────────┘
```

---

## Workflow 3: Disciplinary 3-Strike Escalation Workflow

```
3-Strike Escalation Flow
┌─────────────────────────────────────────────────────────┐
│ 1. Admin Selects User & Submits Violation Strike       │
│    POST /admin/users/strike                             │
├─────────────────────────────────────────────────────────┤
│ 2. Database Record Saved in `author_strikes`            │
│    INSERT INTO author_strikes                           │
├─────────────────────────────────────────────────────────┤
│ 3. Strike Count Evaluated                               │
│    SELECT COUNT(*) FROM author_strikes WHERE status=active│
├─────────────────────────────────────────────────────────┤
│ 4. Penalty Action Dispatched                            │
│    - Strike 1: Warning Email                               │
│    - Strike 2: 14-Day Suspension + Session Termination  │
│    - Strike 3: Permanent Ban + Session Termination      │
└─────────────────────────────────────────────────────────┘
```

---

## Workflow 4: Creator Payout Claim & Withdrawal Disbursement Workflow

```
Payout Withdrawal Flow
┌─────────────────────────────────────────────────────────┐
│ 1. Author Reaches Minimum Threshold ($10.00+)           │
│    GET /author/rewards                                  │
├─────────────────────────────────────────────────────────┤
│ 2. Author Requests Payout Claim                         │
│    POST /author/rewards/withdraw                        │
│    - FinancialLedger::requestPayout()                   │
│    - Immature Debit Entry Recorded in Ledger             │
│    - Status: 'pending' in `payout_claims`               │
├─────────────────────────────────────────────────────────┤
│ 3. Super Admin Reviews in Payout Management Panel       │
│    GET /admin/revenue                                   │
├─────────────────────────────────────────────────────────┤
│ 4. Admin Approves Payout & Inputs Reference ID          │
│    POST /admin/revenue/payout/process (action=approve)  │
│    - Status updated: 'paid' in `payout_claims`          │
│    - Confirmation Email Sent to Creator                 │
└─────────────────────────────────────────────────────────┘
```

---

## Complete QA Verification Matrix Across All Workflows

| QA Test ID | Workflow Target | Trigger Route | Expected Result | Automated Verification Command |
| :--- | :--- | :--- | :--- | :--- |
| `TC-WF-001` | Article Submission | `POST /author/studio/editor` | Article status `'pending'`. | `curl -b "TECHSCOUT_SESSION=author" -X POST ...` |
| `TC-WF-002` | Editorial Approval | `POST /admin/content-queue/review` | Status `'published'`, author rep +50. | `curl -b "TECHSCOUT_SESSION=admin" -X POST ...` |
| `TC-WF-003` | Patronage Tipping | `POST /api/v1/tipping/send` | $4.75 net credited to author balance. | `curl -b "TECHSCOUT_SESSION=member" -X POST ...` |
| `TC-WF-004` | 3-Strike Penalty | `POST /admin/users/strike` | User status `'suspended'`, session revoked. | `curl -b "TECHSCOUT_SESSION=admin" -X POST ...` |
| `TC-WF-005` | Payout Claim | `POST /admin/revenue/payout/process` | Claim status `'paid'`, net balance deducted. | `curl -b "TECHSCOUT_SESSION=admin" -X POST ...` |
