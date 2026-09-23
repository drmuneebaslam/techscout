# Amazon AWS SES Production Access & EC2 Port 25 Unblocking Request Application

This document provides a complete, copy-paste-ready formal application for Amazon Web Services (AWS) Support to:
1. **Lift EC2 Port 25 Egress Restrictions** (Request to remove outbound port 25 throttle on your EC2 Elastic IP).
2. **Move Amazon Simple Email Service (SES) Out of Sandbox Mode** into Production with increased sending quotas.

---

## 1. Application: Lifting EC2 Port 25 Throttle

To send transactional mail directly from your Amazon EC2 instance (running Postfix or direct SMTP) or to connect to external mail servers on port 25, AWS imposes a default throttle. Submit this request via the **[AWS Support Console: Request to Remove Email Sending Limitations](https://aws.amazon.com/forms/ec2-email-limit-rdns-request/)**.

### Form Fields & Recommended Submissions

* **AWS Account ID**: `<YOUR_12_DIGIT_AWS_ACCOUNT_ID>`
* **Email Address**: `admin@techscout.online`
* **Use Case Description**:
  *(Copy and paste the template below)*

```text
Subject: Request to Remove Outbound Port 25 Restrictions and Configure Reverse DNS (rDNS) for TechScout Production Server

Dear AWS Trust & Safety and EC2 Postmaster Team,

We are submitting this formal request to remove the default outbound port 25 restriction for our production EC2 instance associated with TechScout (https://techscout.online). We also request the configuration of Reverse DNS (PTR) for our Elastic IP.

--- 1. Infrastructure & Elastic IP Details ---
• Primary Domain: techscout.online
• Elastic IP (EIP): <YOUR_EC2_ELASTIC_IP>
• Desired Reverse DNS (rDNS / PTR Record): mail.techscout.online
• AWS Region: us-east-1 (or your active EC2 region, e.g. eu-west-1 / ap-south-1)
• Instance ID: <YOUR_EC2_INSTANCE_ID>

--- 2. Business Description & Sending Purpose ---
TechScout (https://techscout.online) is an authoritative open knowledge and intelligence directory for artificial intelligence tools, prompt engineering blueprints, technical software benchmarks, and open-source models.

Our outbound email volume consists EXCLUSIVELY of legitimate transactional user communications initiated directly by end users:
1. Account verification emails and two-factor authentication (2FA) one-time passcodes (OTP).
2. Password reset links requested directly by account owners.
3. System notifications for authors (submission approvals, editorial status changes, security alerts).
4. Direct developer inquiry responses submitted through our verified contact forms.
5. Critical platform security alerts (suspicious login notices, API key creation alerts).

We DO NOT engage in cold outreach, third-party lead generation, purchased lists, affiliate spam, or bulk marketing emails.

--- 3. Email Authentication & Security Standards ---
We have implemented strict industry-standard authentication mechanisms:
• SPF: "v=spf1 ip4:<YOUR_EC2_ELASTIC_IP> include:techscout.online -all"
• DKIM: 2048-bit cryptographic key signing enabled for all outbound messages.
• DMARC: "v=DMARC1; p=reject; rua=mailto:dmarc-reports@techscout.online; pct=100; aspf=s; adkim=s"
• TLS Enforcement: Mandatory opportunistic and forced STARTTLS encryption (TLS 1.2 / TLS 1.3) for all outbound transit.

--- 4. Bounce, Complaint & Unsubscribe Handling ---
• Zero Purchased Lists: Every recipient is an authenticated user of techscout.online.
• Automated Bounce Management: Hard bounces (5xx) are automatically marked in our relational database and permanently suppressed from subsequent dispatches.
• Complaint Loop (FBL): Abuse and postmaster mailboxes (abuse@techscout.online and postmaster@techscout.online) are actively monitored 24/7 by our security team.
• Rate Throttling: Dispatch rates are regulated by an asynchronous queue to prevent burst spikes and respect destination mail server connection limits.

We take email deliverability and sender reputation extremely seriously. We kindly request that you remove the port 25 limitation and set the requested PTR record:
  <YOUR_EC2_ELASTIC_IP> -> mail.techscout.online

Thank you for your prompt assistance and support.

Sincerely,
TechScout Engineering & Operations Team
support@techscout.online | https://techscout.online
```

---

## 2. Application: Amazon SES Production Access (Moving Out of Sandbox)

If you are using Amazon Simple Email Service (SES) to dispatch transactional emails via API or SMTP credentials, your AWS account starts in the SES Sandbox (restricted to sending only to verified email addresses and limited to 200 emails per 24 hours).

Submit this request via the **[AWS SES Console > Account Dashboard > Request Production Access](https://console.aws.amazon.com/ses/)**.

### Form Input Data

1. **Mail Type**: `Transactional`
2. **Website URL**: `https://techscout.online`
3. **Sending Quota Requested**: `50,000 emails/day` (or `10,000 emails/day` for new domains)
4. **Use Case Description**:
   *(Copy and paste the template below)*

```text
Detailed Use Case Description:

1. Nature of the Service & Audience:
TechScout (https://techscout.online) is a premier technology discovery platform and developer ecosystem indexing cutting-edge artificial intelligence tools, prompt blueprints, custom GPTs, and technical research articles. Our users are software engineers, AI researchers, and technical founders.

2. Acquisition of Recipient Addresses:
100% of our recipient email addresses are acquired via double opt-in during user account registration at https://techscout.online/auth/register or when existing registered users opt in to weekly technical digests from their account security settings. We strictly forbid and never utilize third-party lists, web scrapers, or purchased databases.

3. Email Message Types:
All dispatches through Amazon SES will fall into two categories:
• Priority 1: High-urgency transactional messages:
  - Email address confirmation / activation links.
  - Multi-factor authentication security tokens.
  - Password recovery requests.
  - Real-time author payout notifications and security audit alerts.
• Priority 2: User-subscribed engineering updates:
  - Weekly summaries of top-ranked AI tools and curated prompts to which the user has explicitly opted in.

4. Bounce & Complaint Management Architecture:
We have integrated Amazon SES with Amazon SNS (Simple Notification Service) and our webhook listener (https://techscout.online/api/v1/webhook/ses-notifications) to process real-time feedback:
• Hard Bounces (Permanent Failures): Automatically intercepted via SNS webhook. The recipient's email address is immediately set to 'bounced' in our database, and all future dispatches are permanently blocked.
• Complaints: Any recipient spam complaint is received via Amazon SNS and immediately triggers an automatic account suppression flag.
• Unsubscribe: Every notification contains a one-click HTTP unsubscribe link as well as standard RFC-8058 List-Unsubscribe and List-Unsubscribe-Post email headers.
• Our historical bounce rate is maintained below 1.0%, and our complaint rate is maintained strictly below 0.05%, well within AWS Trust & Safety thresholds.

5. Compliance and Authentication:
Our domain is fully verified in Amazon SES:
• Easy DKIM: Verified with 3 CNAME records generated by AWS.
• Custom MAIL FROM Domain: Configured as mail.techscout.online with matching MX and SPF records.
• DMARC: Active strict policy (p=reject) with aggregate reports sent to dmarc-reports@techscout.online.

Please grant production access for our domain techscout.online with an initial daily sending limit of 50,000 emails and a maximum sending rate of 25 emails/second.
```

---

## 3. Required DNS Records for Maximum Deliverability

Before submitting the request, ensure the following DNS records are published on Cloudflare / Route 53 / your DNS registrar:

| Type | Name / Host | Target / Value | TTL | Purpose |
|---|---|---|---|---|
| **A** | `mail` | `<YOUR_EC2_ELASTIC_IP>` | Auto | Direct mail exchanger host |
| **MX** | `@` | `10 mail.techscout.online` | Auto | Domain mail routing |
| **TXT** | `@` | `v=spf1 ip4:<YOUR_EC2_ELASTIC_IP> include:techscout.online -all` | Auto | SPF Sender Policy Framework |
| **TXT** | `_dmarc` | `v=DMARC1; p=reject; rua=mailto:dmarc-reports@techscout.online; pct=100; aspf=s; adkim=s` | Auto | DMARC Anti-Spoofing Policy |
| **CNAME** | `aws-dkim-1` | `(Provided by AWS SES Console)` | Auto | SES DKIM Token 1 |
| **CNAME** | `aws-dkim-2` | `(Provided by AWS SES Console)` | Auto | SES DKIM Token 2 |
| **CNAME** | `aws-dkim-3` | `(Provided by AWS SES Console)` | Auto | SES DKIM Token 3 |

---

## 4. Verification Commands

Once AWS Support approves the request, run the following commands on your EC2 instance to verify port 25 and SMTP deliverability:

```bash
# 1. Test outbound connectivity to external mail server on Port 25
nc -zv smtp.gmail.com 25
# Expected: Connection to smtp.gmail.com 25 port [tcp/smtp] succeeded!

# 2. Check Reverse DNS (PTR) Resolution
dig -x <YOUR_EC2_ELASTIC_IP> +short
# Expected: mail.techscout.online.

# 3. Test SPF & DMARC records
dig txt techscout.online +short
dig txt _dmarc.techscout.online +short
```
