# 🌐 TechScout Enterprise Mail System & Master Operational Blueprint

> **Complete End-to-End Field Guide: Step 1, Sub-Step 1 to Production**  
> *Zero Auto-Routing • Pure Manual Credential Assignment • Gmail SMTP with Google App Passwords • ImprovMX Free Custom Domain (25 Aliases) • 28 Enterprise HTML Templates • 100% Stateless Architecture (Zero Credentials Stored Remotely)*

---

## 📑 Master Table of Contents
1. [Core Architectural Principles](#1-core-architectural-principles)
2. [Master Configuration Matrix: What to Enter Where](#2-master-configuration-matrix-what-to-enter-where)
3. [STEP 1: Google Account Setup & 16-Character App Password](#step-1-google-account-setup--16-character-app-password)
4. [STEP 2: ImprovMX Setup for 25 Free Custom Domain Addresses](#step-2-improvmx-setup-for-25-free-custom-domain-addresses)
5. [STEP 3: Gmail "Send Mail As" Configuration](#step-3-gmail-send-mail-as-configuration)
6. [STEP 4: Adding Mail Credentials in TechScout (`/admin/mail`)](#step-4-adding-mail-credentials-in-techscout-adminmail)
7. [STEP 5: Manual Credential Assignment (Zero Auto-Routing)](#step-5-manual-credential-assignment-zero-auto-routing)
8. [STEP 6: Complete Directory of All 28 Production Email Templates](#step-6-complete-directory-of-all-28-production-email-templates)
9. [STEP 7: Creator Rewards & Payouts Lifecycle Templates](#step-7-creator-rewards--payouts-lifecycle-templates)
10. [STEP 8: PythonAnywhere Stateless Webhook / REST Dispatcher (Optional)](#step-8-pythonanywhere-stateless-webhook--rest-dispatcher-optional)
11. [STEP 9: Live Testing, Verification & SMTP Diagnostics Runbook](#step-9-live-testing-verification--smtp-diagnostics-runbook)

---

## 1. Core Architectural Principles

### 1. Pure Manual Credential Assignment (No Auto-Routing)
All previous heuristic category guessing or automated sender routing has been **permanently eliminated**.
- **Every Template Card** in `/admin/mail?tab=templates` provides a direct 1-click manual dropdown to designate which mail credential dispatches it.
- **The Compose Studio** (`/admin/mail`) gives the administrator an explicit selection dropdown (`mail_credential_id`) to hand-pick the exact outbound email account for any newsletter or batch dispatch.
- **Clear Priority Order**:
  1. Explicit override passed in send request (`overrideCredentialId`).
  2. Template's manually mapped credential (`mail_templates.assigned_credential_id`).
  3. Platform system default credential (`mail_credentials.is_default = 1`).
  4. First active registered credential.

### 2. No Proxy Server Required
Because Gmail SMTP operates over direct TLS sockets (`smtp.gmail.com:587`), **no intermediate forward proxy server is required**. TechScout's built-in socket client (`App\Core\SmtpClient`) connects directly to Gmail over secure TLS/SSL, handles `AUTH LOGIN` authentication, injects MIME headers, and sends emails instantly.

### 3. ImprovMX + Gmail Synergy (25 Free Professional Addresses)
By combining **ImprovMX.com** (which routes up to 25 custom domain addresses like `support@yourdomain.com`, `billing@yourdomain.com` directly to your Gmail inbox for free) with **Gmail SMTP App Passwords**, you get complete corporate-grade custom domain email sending and receiving without paying for Google Workspace or third-party servers.

### 4. 100% Stateless External Relays (Zero Stored Credentials)
If you deploy the Python dispatch service to PythonAnywhere or a VPS, **no credentials, passwords, or API keys are ever stored on the remote server**. TechScout signs every request with HMAC-SHA256 and transmits the credentials dynamically inside the JSON payload for immediate one-time execution.

```
+-----------------------------------------------------------------------------------------+
|                                    TECHSCOUNT CORE (PHP)                                |
|  - Encrypted Database Credentials Store (`mail_credentials` table)                      |
|  - Manual Template Credential Mapping (`mail_templates` table)                          |
|  - Native SmtpClient Socket Engine (TLS / SSL / AUTH LOGIN)                             |
|  - 28 Enterprise Responsive Dark-Mode HTML Templates                                    |
+-----------------------------------------------------------------------------------------+
                     |                                       |
    [OPTION A: DIRECT NATIVE DISPATCH]     [OPTION B: STATELESS PYTHON / API DISPATCH]
                     |                                       |
                     v                                       v
      +-----------------------------+         +-----------------------------+
      |      GMAIL SMTP RELAY       |         |   PYTHONANYWHERE (STATELESS)|
      |     (smtp.gmail.com:587)    |         |  - Zero credentials stored  |
      |   16-char App Password TLS  |         |  - Dynamic payload execution|
      +-----------------------------+         +-----------------------------+
                     |                                       |
                     +-------------------+-------------------+
                                         |
                                         v
                         +-------------------------------+
                         |    IMPROVMX CUSTOM DOMAIN     |
                         |   (SPF / DKIM / 25 Aliases)   |
                         +-------------------------------+
                                         |
                                         v
                                  RECIPIENT INBOX
```

---

## 2. Master Configuration Matrix: What to Enter Where

| System / Screen | Field Name | Description | What Value to Enter |
| :--- | :--- | :--- | :--- |
| **Google Account** | 2-Step Verification | Mandatory prerequisite for App Passwords | Turn **ON** in Google Account Security |
| **Google Account** | App Password | 16-character dedicated mail authorization key | Generate under App Passwords, select "Mail" &rarr; "Other (TechScout)" |
| **ImprovMX.com** | MX Records | Primary DNS records for email routing | Priority 10: `mx1.improvmx.com`, Priority 20: `mx2.improvmx.com` |
| **ImprovMX.com** | TXT Record (SPF) | Authorizes ImprovMX & Gmail to send for your domain | `v=spf1 include:spf.improvmx.com include:_spf.google.com ~all` |
| **Gmail Settings** | Send mail as | Custom domain alias address | Add `support@yourdomain.com`, SMTP: `smtp.gmail.com`, Port: `587` |
| **TechScout Admin** | Profile Name | Identifier for your team | e.g. `Gmail SMTP - Official Support` |
| **TechScout Admin** | Provider | Connection protocol | Select `Standard SMTP Server (smtp)` |
| **TechScout Admin** | SMTP Host | Gmail server address | `smtp.gmail.com` |
| **TechScout Admin** | SMTP Port | TLS communication port | `587` (or `465` for SSL) |
| **TechScout Admin** | Encryption | Socket encryption layer | `tls` |
| **TechScout Admin** | SMTP Username | Your actual Google email address | `youraccount@gmail.com` |
| **TechScout Admin** | SMTP Password | 16-character Google App Password | e.g. `abcd efgh ijkl mnop` (spaces are handled automatically) |
| **TechScout Admin** | From Email | Sender email displayed to recipients | Your ImprovMX alias: `support@yourdomain.com` or Gmail address |
| **TechScout Admin** | From Name | Sender name displayed in inbox | `TechScout Support` or `TechScout Treasury` |

---

## STEP 1: Google Account Setup & 16-Character App Password

Google requires all automated SMTP applications to authenticate with an **Application-Specific Password**. Your normal Google account login password will be rejected with `534-5.7.9 Application-specific password required`.

### Sub-step 1.1: Enable 2-Step Verification
1. Open your browser and navigate to **[myaccount.google.com/security](https://myaccount.google.com/security)**.
2. Sign in to the Google account you wish to use as your outbound mail engine.
3. Under the **"How you sign in to Google"** section, verify that **2-Step Verification** is set to **Turned on**. If it is off, click it and follow Google's prompts to enable SMS or Google Authenticator verification.

### Sub-step 1.2: Generate the 16-Character App Password
1. In the search bar at the top of Google Account, type **"App passwords"** (or go directly to **[myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)**).
2. Under **"App name"**, type:
   ```text
   TechScout Production Mailer
   ```
3. Click the **Create** button.
4. A modal will appear showing a 16-character password highlighted in yellow (e.g. `abcd efgh ijkl mnop`).
5. **Copy this 16-character string immediately** and save it to a safe notepad. (Google only displays it once).

> [!TIP]
> TechScout's `SmtpClient` automatically strips whitespace from passwords, so you can paste `abcd efgh ijkl mnop` or `abcdefghijklmnop` directly without error.

---

## STEP 2: ImprovMX Setup for 25 Free Custom Domain Addresses

ImprovMX allows you to route up to **25 custom domain addresses** (e.g., `support@yourdomain.com`, `treasury@yourdomain.com`, `news@yourdomain.com`) straight into your Gmail inbox for free.

### Sub-step 2.1: Register Your Domain on ImprovMX
1. Go to **[improvmx.com](https://improvmx.com)** and create a free account.
2. In the dashboard, click **"Add Domain"** and enter your root domain (e.g., `techscout.online`).

### Sub-step 2.2: Configure DNS Records with Your Domain Registrar
Log in to your DNS provider (Cloudflare, Namecheap, GoDaddy, Porkbun, etc.) and add the following records:

1. **MX Record 1**:
   - **Type**: `MX`
   - **Host / Name**: `@` (or leave blank depending on registrar)
   - **Priority**: `10`
   - **Value / Target**: `mx1.improvmx.com`

2. **MX Record 2**:
   - **Type**: `MX`
   - **Host / Name**: `@`
   - **Priority**: `20`
   - **Value / Target**: `mx2.improvmx.com`

3. **TXT Record (SPF Verification)**:
   - **Type**: `TXT`
   - **Host / Name**: `@`
   - **Value**:
     ```text
     v=spf1 include:spf.improvmx.com include:_spf.google.com ~all
     ```
     *(This authorizes both ImprovMX and Gmail to dispatch valid emails on behalf of your domain without getting marked as spam).*

### Sub-step 2.3: Create Up to 25 Email Forward Aliases
In your ImprovMX dashboard under your domain, configure your alias forwarding rules:

| Alias | Forwards To | Purpose |
| :--- | :--- | :--- |
| `notifications` | `yourgmail@gmail.com` | Account verification, OTP PINs, login alerts |
| `treasury` | `yourgmail@gmail.com` | Creator payouts, withdrawal receipts, tip alerts |
| `editorial` | `yourgmail@gmail.com` | Article approvals, author badge awards, revisions |
| `support` | `yourgmail@gmail.com` | Helpdesk ticket replies, member contact desk |
| `newsletter` | `yourgmail@gmail.com` | Weekly curated digests, AI radar alerts, hackathons |
| `*` (Catch-all) | `yourgmail@gmail.com` | Any other email sent to `@yourdomain.com` |

Click **"Check DNS Settings"** in ImprovMX until the domain badge turns **Green: Email forwarding active**.

---

## STEP 3: Gmail "Send Mail As" Configuration

To allow Gmail's SMTP server to dispatch emails using your custom domain addresses (`@yourdomain.com`) without a "sent on behalf of" warning:

1. Open Gmail in your browser and click the **Gear icon** (top right) &rarr; **See all settings**.
2. Click the **"Accounts and Import"** tab.
3. In the section **"Send mail as:"**, click **"Add another email address"**.
4. A popup window will appear:
   - **Name**: `TechScout Intelligence` (or `TechScout Support`)
   - **Email address**: Enter your custom alias (e.g., `support@yourdomain.com`)
   - Check **"Treat as an alias"**
   - Click **"Next Step"**
5. Enter the outgoing SMTP server information:
   - **SMTP Server**: `smtp.gmail.com`
   - **Port**: `587`
   - **Username**: Your actual Gmail address (`yourgmail@gmail.com`)
   - **Password**: Your **16-character Google App Password** generated in Step 1
   - **Secured connection using**: `TLS (recommended)`
   - Click **"Add Account"**
6. Gmail will send a confirmation email with a verification link to `support@yourdomain.com`. Because ImprovMX forwards this to your Gmail, you will receive the confirmation code in your inbox immediately!
7. Click the link or enter the verification code in the popup.

Your custom domain address is now fully authorized to send through Gmail SMTP!

---

## STEP 4: Adding Mail Credentials in TechScout (`/admin/mail`)

1. Log in to your TechScout Admin Hub and navigate to **`/admin/mail`**.
2. Click on the **"🔑 Mail Credentials & Profiles"** tab.
3. Click the green button: **"Add Standard SMTP +"**.
4. Fill in the modal fields:
   - **Credential Profile Name**: `Gmail SMTP - Primary Production`
   - **Provider**: `smtp`
   - **Sender Display Name**: `TechScout Intelligence`
   - **Sender From Email**: `support@yourdomain.com` (or your Gmail address)
   - **Reply-To Email**: `support@yourdomain.com`
   - **SMTP Host**: `smtp.gmail.com`
   - **SMTP Port**: `587`
   - **Encryption**: `tls`
   - **SMTP Username**: `yourgmail@gmail.com`
   - **SMTP Password**: Your 16-character Google App Password
   - Check **"Set as Platform Default Provider"**
   - **Status**: `Active`
5. Click **"Save Mail Credential 💾"**.
6. On the newly created card in the table, click the **"⚡ Test"** button, enter a test recipient email address, and verify that the green success badge appears.

---

## STEP 5: Manual Credential Assignment (Zero Auto-Routing)

### How to Assign Credentials to Email Templates
1. In `/admin/mail`, click on the **"📑 HTML Template Studio"** tab.
2. Browse through any of the **28 production templates**.
3. On each template card, locate the **"Assigned Account"** box:
   - Select your added mail credential from the dropdown (e.g. `Gmail SMTP - Primary Production`).
   - The dropdown automatically saves via AJAX / POST to `/admin/mail/template/assign-credential`.
   - The template card updates with a cyan badge: **"MANUAL MAPPED"**.

### How to Manually Select a Credential in the Compose Studio
1. In `/admin/mail`, click on the **"✉️ Compose & Live Studio"** tab.
2. Locate the **"Outbound Mail Account / Credential (Manual Selection)"** dropdown.
3. Choose exactly which credential account will send this communication.
4. Pick your recipients, adjust your dynamic tags, preview the rendering live on the right split screen, and click **"Dispatch Email"**.

---

## STEP 6: Complete Directory of All 28 Production Email Templates

TechScout features **28 pre-built enterprise-grade responsive HTML templates**. Every template includes mobile-ready dark mode styling, responsive typography, and dynamic tags.

### Authentication & Account Security Stream
| Template Key | Display Name | Purpose | Dynamic Tokens Available |
| :--- | :--- | :--- | :--- |
| `welcome` | 🚀 Welcome to TechScout Network | Sent upon initial user registration | `{{name}}`, `{{username}}`, `{{action_url}}`, `{{login_url}}` |
| `email_verification` | 📧 Sign Up Verification PIN | Sends 6-digit confirmation code | `{{name}}`, `{{verification_code}}`, `{{expires_in}}`, `{{action_url}}` |
| `account_activated` | ✅ Account Verified & Active | Sent once email verification succeeds | `{{name}}`, `{{username}}`, `{{action_url}}` |
| `login_alert` | 🔐 New Sign-In Security Alert | Security perimeter alert for new sessions | `{{name}}`, `{{ip_address}}`, `{{browser}}`, `{{timestamp}}`, `{{action_url}}` |
| `password_reset` | 🔒 Password Reset & Security Code | One-time recovery PIN code | `{{name}}`, `{{reset_code}}`, `{{action_url}}` |
| `security_alert` | 🛡️ Security Parameter Update | Sent when password or profile details change | `{{name}}`, `{{change_time}}`, `{{action_url}}` |
| `security_two_factor_otp` | 🔑 2FA Security Passcode | Sent for sensitive settings authorization | `{{name}}`, `{{two_factor_otp}}`, `{{expires_in}}`, `{{ip_address}}` |

### Creator Monetization & Treasury Stream
| Template Key | Display Name | Purpose | Dynamic Tokens Available |
| :--- | :--- | :--- | :--- |
| `payout_requested` | 💸 Withdrawal Claim Received | Sent when author claims royalty funds | `{{name}}`, `{{amount}}`, `{{net_amount}}`, `{{fee_deducted}}`, `{{payout_id}}`, `{{ref_no}}`, `{{method_type}}`, `{{destination}}`, `{{action_url}}` |
| `payout_approved` | 💰 Payout Approved & Disbursed | Sent when treasury desk transfers funds | `{{name}}`, `{{amount}}`, `{{net_amount}}`, `{{payout_id}}`, `{{transaction_ref}}`, `{{destination}}`, `{{action_url}}` |
| `payout_rejected` | ⚠️ Payout Action Required | Sent if compliance rejects claim (100% refund) | `{{name}}`, `{{amount}}`, `{{payout_id}}`, `{{admin_notes}}`, `{{action_url}}` |
| `tip_received` | 🎁 Reader Patronage Tip Alert | Sent when a reader tips an article | `{{name}}`, `{{tipper_name}}`, `{{amount}}`, `{{net_amount}}`, `{{article_title}}`, `{{action_url}}` |
| `monthly_creator_earnings_statement` | 📊 Monthly Royalty Statement | Monthly digest of views, tips, and royalties | `{{name}}`, `{{month}}`, `{{amount}}`, `{{total_reads}}`, `{{tips_count}}`, `{{action_url}}` |

### Editorial Desk & Author Publishing Stream
| Template Key | Display Name | Purpose | Dynamic Tokens Available |
| :--- | :--- | :--- | :--- |
| `author_application_received` | 📝 Creator Application Received | Informs applicant that review is underway | `{{name}}`, `{{username}}`, `{{action_url}}` |
| `author_approved` | 🌟 Creator Publishing Approved | Unlocks author studio & revenue tools | `{{name}}`, `{{username}}`, `{{action_url}}` |
| `article_submitted` | 📑 Article Queued for Review | Confirmation that author submitted draft | `{{name}}`, `{{article_title}}`, `{{action_url}}` |
| `article_approved` | 🎉 Article Approved & Published | Informs author their post is live | `{{name}}`, `{{article_title}}`, `{{action_url}}` |
| `article_rejected` | ✍️ Draft Review Feedback | Constructive feedback on unaccepted post | `{{name}}`, `{{article_title}}`, `{{editorial_notes}}`, `{{action_url}}` |
| `editorial_revisions` | 📋 Revisions Requested | Peer review notes requiring edits | `{{name}}`, `{{article_title}}`, `{{editorial_notes}}`, `{{action_url}}` |
| `author_milestone_badge` | 🎖️ Creator Milestone Awarded | Readership milestones (e.g. 10k views) | `{{name}}`, `{{milestone_title}}`, `{{action_url}}` |

### Community Engagement & Discussion Stream
| Template Key | Display Name | Purpose | Dynamic Tokens Available |
| :--- | :--- | :--- | :--- |
| `comment_reply_alert` | 💬 Discussion Thread Reply | Informs user of reply to their comment | `{{name}}`, `{{commenter_name}}`, `{{article_title}}`, `{{comment_snippet}}`, `{{action_url}}` |
| `bookmark_collection_shared` | 📑 Blueprint Stack Shared | When a colleague shares a bookmark stack | `{{name}}`, `{{collection_title}}`, `{{sender_name}}`, `{{action_url}}` |
| `hackathon_campaign_invite` | 🏆 AI Hackathon Invitation | Invitations to developer sprint challenges | `{{name}}`, `{{campaign_title}}`, `{{prize_pool}}`, `{{deadline}}`, `{{action_url}}` |
| `newsletter_breaking_ai_radar` | 🚨 Flash AI Radar Alert | Urgent announcements or model releases | `{{name}}`, `{{headline}}`, `{{message}}`, `{{action_url}}` |
| `community_broadcast` | 📢 Newsletter & Broadcast | General community broadcast newsletter | `{{name}}`, `{{headline}}`, `{{message}}`, `{{action_url}}` |

### Helpdesk & Platform Governance Stream
| Template Key | Display Name | Purpose | Dynamic Tokens Available |
| :--- | :--- | :--- | :--- |
| `ticket_reply` | 🎫 Support Helpdesk Response | Staff updates on support tickets | `{{name}}`, `{{ticket_number}}`, `{{reply_message}}`, `{{action_url}}` |
| `contact_reply` | 💬 Contact Desk Response | Response to public contact inquiries | `{{name}}`, `{{inquiry_subject}}`, `{{reply_message}}`, `{{action_url}}` |
| `user_account_suspended` | ⚠️ Account Suspension Notice | Policy violation governance warning | `{{name}}`, `{{suspension_reason}}`, `{{appeal_url}}` |
| `user_account_reactivated` | 🟢 Account Access Reinstated | Suspension lifted confirmation | `{{name}}`, `{{action_url}}` |

---

## STEP 7: Creator Rewards & Payouts Lifecycle Templates

TechScout includes financial safeguards and structured communication for author monetization:

```
[Author Requests Payout]
          │
          ▼
   payout_requested  ──► Author receives confirmation with #payout_id, gross, fee, net amount
          │
          ├───────────────────────────────┐
          │ (Approved by Treasury Desk)    │ (Rejected / Needs Revision)
          ▼                               ▼
   payout_approved                 payout_rejected
- Funds transferred             - Credits 100% restored to balance
- Transaction ref attached      - Treasury notes explained
- Direct link to ledger         - Action button to update details
```

---

## STEP 8: PythonAnywhere Stateless Webhook / REST Dispatcher (Optional)

If you wish to use your PythonAnywhere account as an external REST API microservice relay:

1. In PythonAnywhere, upload the `python_mail_service` directory to `/home/<your-username>/techscout_mail_service`.
2. Configure your WSGI configuration file:
   ```python
   import sys
   path = '/home/<your-username>/techscout_mail_service'
   if path not in sys.path:
       sys.path.insert(0, path)

   from app import app as application
   ```
3. **Important**: **Do not** write any credentials into `.env` or `config.py` on PythonAnywhere!
4. In TechScout Admin (`/admin/mail` &rarr; "🔑 Mail Credentials & Profiles"), click **"Add Python API / REST Endpoint +"**:
   - **Provider**: `pythonanywhere_api`
   - **Endpoint URL**: `https://<your-username>.pythonanywhere.com/api/v1/mail/dispatch`
   - **API Key**: Enter any secret token (e.g. `techscout_secure_vault_2026`)
5. Click Save. Every email routed through this profile transmits the credentials dynamically with HMAC-SHA256 signature verification.

---

## STEP 9: Live Testing, Verification & SMTP Diagnostics Runbook

### Testing via Admin Hub
1. Go to `/admin/mail?tab=credentials`.
2. Click **"⚡ Test"** on your Gmail SMTP credential card.
3. Enter your personal email address and click **"Send Test"**.

### Common SMTP Response Codes & Remedies

- **`235 2.7.0 Accepted / Authentication successful`**:
  - Email delivered successfully! Check your inbox or spam folder.

- **`534-5.7.9 Application-specific password required`**:
  - You entered your regular Google account password instead of an App Password.
  - Fix: Go to Google Account &rarr; Security &rarr; App Passwords and generate a 16-character password (see Step 1).

- **`535-5.7.8 Username and Password not accepted`**:
  - The 16-character password was mistyped or revoked.
  - Fix: Generate a new App Password and update the credential profile in `/admin/mail`.

- **`530-5.7.0 Authentication Required`**:
  - The SMTP password field was left blank in the credential settings.
  - Fix: Enter the 16-character Google App Password in the credential profile.

- **`550-5.7.1 Unauthenticated email is not accepted from this domain`**:
  - The custom domain address (`support@yourdomain.com`) has not been authorized in Gmail settings.
  - Fix: Complete Step 3 ("Send mail as" in Gmail Settings) and verify the confirmation email from ImprovMX.

---

## 🎯 Final Summary
With this architecture:
- **Zero auto-routing**: You have 100% control over which email account sends which email.
- **Zero proxy servers required**: Direct TLS socket connections to `smtp.gmail.com:587`.
- **25 free custom domain emails**: Powered by ImprovMX forwarding to Gmail.
- **28 production templates**: Beautiful dark-mode responsive HTML covering all platform features.
- **Complete security**: No passwords stored remotely on PythonAnywhere or outside TechScout.
