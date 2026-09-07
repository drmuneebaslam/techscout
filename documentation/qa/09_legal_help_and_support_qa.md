# QA Test Guide 09: Legal Pages, Help Center & Support Ticket Suite QA

This document specifies the step-by-step Enterprise QA testing suite for Support Tickets (`app/Models/SupportEngine.php`), Help Center Articles (`views/admin/help_articles.php`), Legal Page Editors (`views/admin/legal_page_editor.php`), and Contact Submissions (`app/Controllers/SupportController.php`).

---

## Pre-Test Setup & Requirements

| Requirement | Value / Condition |
| :--- | :--- |
| **Base URL** | `http://localhost/techscout.n/` |
| **User Roles** | Customer Member (`member@techscout.com`), Support Staff / Admin (`admin@techscout.com`). |
| **Subsystems** | Support Tickets, Help Knowledge Base, Legal Terms Editor. |

---

## Test Suite 1.0: Support Ticket Workflow (`app/Models/SupportEngine.php`)

### Test Case 1.1: Ticket Submission, Escalation & Resolution
- **Objective**: Verify customer ticket creation, file attachment, staff response, and status resolution.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Member Submit Support Ticket**
   - 1.1: Log in as `member@techscout.com` -> Open `/support/new`.
   - 1.2: Select Category: `Billing & Rewards`, Priority: `High`.
   - 1.3: Subject: `Payout request #PAY-8823 processing delay`.
   - 1.4: Message Body: `My payout request has been pending for over 48 hours. Please verify.`.
   - 1.5: Attach Screenshot: `payout_screenshot.png`.
   - 1.6: Click "Submit Ticket".
   - 1.7: Confirm ticket created with status `open` and code `#TCK-9912`.

2. **Step 2: Admin Support Desk Response (`views/admin/tickets.php`)**
   - 2.1: Log in as Admin -> Open `/admin/tickets`.
   - 2.2: Locate ticket `#TCK-9912`.
   - 2.3: Enter Reply: `Hello! Your payout request has been processed and disbursed. Thank you for your patience.`.
   - 2.4: Change Status: `Resolved`.
   - 2.5: Click "Send Response".

3. **Step 3: Verification & Customer Feedback**
   - 3.1: Log in as Member -> Open `/support/ticket?code=TCK-9912`.
   - 3.2: Verify staff response renders with timestamp and staff badge.
   - 3.3: Verify ticket status displays as "Resolved".

---

## Test Suite 2.0: Legal Page & Policy Versioning (`views/admin/legal_page_editor.php`)

### Test Case 2.1: Legal Terms Revision & Revision History
- **Objective**: Verify editing Privacy Policy or Terms of Service updates live public page and maintains version audit trail.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Admin Edit Privacy Policy**
   - 1.1: Open `/admin/legal-page-editor?page=privacy`.
   - 1.2: Update section: `Section 4: Data Retention Policy updated for 2026 GDPR compliance.`.
   - 1.3: Click "Publish Revisions".

2. **Step 2: Public Page Verification**
   - 2.1: Open public URL `/privacy`.
   - 2.2: Confirm updated section displays immediately.
   - 2.3: Confirm footer metadata reflects *"Last updated: September 8, 2026"*.

---

## Pass / Fail Criteria Checklist

- [ ] Support ticket life cycle transitions smoothly from Open -> Answered -> Resolved.
- [ ] Ticket file attachments validate image/PDF extensions strictly.
- [ ] Legal page revisions update public policy text immediately.
- [ ] QA Inspector: TechScout Lead Customer Support QA.
