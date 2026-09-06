# Custom Email Dispatcher API on PythonAnywhere (Free Tier)
## Architecture, Implementation & Deployment Blueprint for TechScout

---

## 1. Executive Summary & Purpose

This document provides the complete, production-ready specification for building, configuring, and hosting an **independent Custom Email API Microservice** on [PythonAnywhere.com](https://www.pythonanywhere.com) to service all transactional and broadcast email needs for **TechScout**.

This microservice acts as a secure, authenticated bridge between TechScout's PHP application backend and upstream email delivery engines.

```
+--------------------------+         HTTPS JSON POST          +--------------------------------------------+
|        TechScout         |  ------------------------------> |         PythonAnywhere Microservice        |
|    (PHP / Mailer.php)    |  Authorization: Bearer <API_KEY> |  (https://<user>.pythonanywhere.com/send)  |
+--------------------------+                                  +--------------------------------------------+
            |                                                                        |
            v                                                                        | HTTPS (Whitelisted)
+--------------------------+                                                         v
|     Database Storage     |                                          +-------------------------------+
|  `site_settings` table   |                                          | Upstream HTTPS API Provider   |
|  `mail_logs` audit table |                                          | (Resend / Brevo / SendGrid)   |
+--------------------------+                                          +-------------------------------+
                                                                                     |
                                                                                     v
                                                                      +-------------------------------+
                                                                      |        Recipient Inbox        |
                                                                      +-------------------------------+
```

---

## 2. PythonAnywhere Free Account Constraints & Engineering Solutions

When developing on a **PythonAnywhere Free Account (`Beginner` Tier)**, the following architectural constraints apply:

| PythonAnywhere Free Constraint | Impact on Email Microservice | Engineering Solution Implemented |
| :--- | :--- | :--- |
| **Outbound Port Blocking (No SMTP)** | Traditional SMTP (`port 25, 465, 587`) is blocked by PythonAnywhere firewall. | Use **Outbound HTTPS REST APIs** (`port 443`) which are permitted through the free account HTTP proxy. |
| **Outbound Whitelist Restriction** | Free accounts can only make external HTTPS requests to whitelisted hosts. | Upstream providers on the PythonAnywhere whitelist: **Resend (`api.resend.com`)**, **Brevo / Sendinblue (`api.brevo.com`)**, **SendGrid (`api.sendgrid.com`)**, **Mailgun (`api.mailgun.net`)**, **Mailjet (`api.mailjet.com`)**. |
| **No Background Daemons / Celery** | Cannot run asynchronous task queues like Redis/Celery. | Keep the Flask handler lightweight with synchronous, high-speed `requests.post()` calls (< 800ms response time). |
| **CPU Quota (100s/day)** | 100 CPU seconds allotted daily. | HTTP I/O wait does **not** consume CPU quota. A lightweight Flask API can easily handle 5,000+ daily email dispatches within ~2 CPU seconds. |
| **Automatic TLS/HTTPS** | Free `https://<username>.pythonanywhere.com` subdomains include automatic valid SSL/TLS certificates. | No custom SSL certificate management required. |
| **3-Month Web App Expiry** | Free webapps sleep after 3 months if not extended. | Click "Extend web app" on PythonAnywhere dashboard every 3 months. |

---

## 3. TechScout Database & Configuration Integration

All API credentials, endpoints, sender profiles, and access tokens are managed directly in TechScout's `site_settings` MySQL database table.

### `site_settings` Key-Value Mapping

| Setting Key (`key_name`) | Default / Example Value | Description |
| :--- | :--- | :--- |
| `mail_driver` | `api` | Active mail delivery driver (`api`, `smtp`, `log`). |
| `mail_api_provider` | `custom_pythonanywhere` | Provider identifier. |
| `mail_api_endpoint` | `https://<your_username>.pythonanywhere.com/api/send` | Your PythonAnywhere API endpoint URL. |
| `mail_api_key` | `ts_live_sec_994821a8b7c6d5e4f3a2b1` | Shared secret bearer token validated by Flask. |
| `mail_from_address` | `noreply@techscout.online` | Default system sender address. |
| `mail_from_name` | `TechScout Network` | Default system sender name. |
| `mail_sender_noreply_email` | `noreply@techscout.n` | System confirmations & OTPs. |
| `mail_sender_editorial_email` | `editorial@techscout.n` | Editorial reviews and approvals. |
| `mail_sender_newsletter_email` | `newsletter@techscout.n` | Weekly intelligence digests. |
| `mail_sender_support_email` | `support@techscout.n` | Support helpdesk responses. |
| `mail_sender_security_email` | `security@techscout.n` | Password resets & login alerts. |

### How TechScout `Mailer.php` Dispatches to this API

When TechScout sends any email (welcome, verification code, password reset, ticket reply, or newsletter broadcast), `App\Core\Mailer::send()` formats the JSON payload:

```json
{
  "from": "TechScout Network <noreply@techscout.online>",
  "to": ["developer@example.com"],
  "subject": "Verify Your TechScout Account: TS-928104",
  "html": "<!DOCTYPE html><html>...</html>",
  "reply_to": "support@techscout.online",
  "template_key": "email_verification"
}
```

Header sent by TechScout:
```http
Authorization: Bearer ts_live_sec_994821a8b7c6d5e4f3a2b1
Content-Type: application/json
```

---

## 4. Complete Microservice Codebase

### Directory Structure on PythonAnywhere

```
/home/<username>/techscout_mail_api/
├── app.py                  # Main Flask API Application
├── config.py               # Provider Configurations & Fallback Chain
├── requirements.txt        # Python Dependencies
├── .env                    # Secret Environment Variables
└── wsgi.py                 # PythonAnywhere WSGI Entrypoint
```

---

### `requirements.txt`

```txt
Flask==3.0.2
requests==2.31.0
python-dotenv==1.0.1
```

---

### `config.py`

```python
import os
from dotenv import load_dotenv

# Load .env file
load_dotenv(os.path.join(os.path.dirname(__file__), '.env'))

class Config:
    # Secret API Key required from TechScout
    TECHSCOUT_API_SECRET = os.getenv('TECHSCOUT_API_SECRET', 'ts_live_sec_994821a8b7c6d5e4f3a2b1')

    # Primary Provider: Resend (https://resend.com) - Whitelisted on PythonAnywhere
    RESEND_API_KEY = os.getenv('RESEND_API_KEY', '')
    RESEND_ENDPOINT = 'https://api.resend.com/emails'

    # Fallback Provider: Brevo / Sendinblue (https://brevo.com) - Whitelisted on PythonAnywhere
    BREVO_API_KEY = os.getenv('BREVO_API_KEY', '')
    BREVO_ENDPOINT = 'https://api.brevo.com/v3/smtp/email'

    # Fallback Provider: SendGrid (https://sendgrid.com) - Whitelisted on PythonAnywhere
    SENDGRID_API_KEY = os.getenv('SENDGRID_API_KEY', '')
    SENDGRID_ENDPOINT = 'https://api.sendgrid.com/v3/mail/send'

    # Default fallback sender if none provided in request
    DEFAULT_FROM_EMAIL = os.getenv('DEFAULT_FROM_EMAIL', 'notifications@techscout.online')
    DEFAULT_FROM_NAME = os.getenv('DEFAULT_FROM_NAME', 'TechScout Platform')
```

---

### `app.py`

```python
"""
TechScout Custom Email Dispatcher Microservice
Compatible with PythonAnywhere Free Tier (Outbound HTTPS Whitelist Proxy)
"""

import os
import re
import json
import logging
import requests
from flask import Flask, request, jsonify
from config import Config

# Configure Logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(message)s'
)
logger = logging.getLogger(__name__)

app = Flask(__name__)
app.config.from_object(Config)

def parse_sender(from_string):
    """
    Parses 'Name <email@domain.com>' or plain 'email@domain.com'
    """
    if not from_string:
        return Config.DEFAULT_FROM_NAME, Config.DEFAULT_FROM_EMAIL
    
    match = re.match(r'^(.*?)\s*<([^>]+)>$', from_string.strip())
    if match:
        name = match.group(1).strip().strip('"\'')
        email = match.group(2).strip()
        return name or Config.DEFAULT_FROM_NAME, email
    return Config.DEFAULT_FROM_NAME, from_string.strip()

def send_via_resend(sender_name, sender_email, recipients, subject, html_content, reply_to=None):
    """
    Primary Dispatcher: Resend REST API (HTTPS Whitelisted)
    """
    if not Config.RESEND_API_KEY:
        return False, "Resend API Key not configured"

    headers = {
        "Authorization": f"Bearer {Config.RESEND_API_KEY}",
        "Content-Type": "application/json"
    }

    payload = {
        "from": f"{sender_name} <{sender_email}>",
        "to": recipients,
        "subject": subject,
        "html": html_content
    }
    if reply_to:
        payload["reply_to"] = reply_to

    try:
        resp = requests.post(Config.RESEND_ENDPOINT, headers=headers, json=payload, timeout=10)
        if 200 <= resp.status_code < 300:
            data = resp.json()
            return True, data.get("id", "resend_ok")
        else:
            return False, f"Resend Error ({resp.status_code}): {resp.text}"
    except Exception as e:
        return False, f"Resend Exception: {str(e)}"

def send_via_brevo(sender_name, sender_email, recipients, subject, html_content, reply_to=None):
    """
    Secondary Fallback Dispatcher: Brevo REST API (HTTPS Whitelisted)
    """
    if not Config.BREVO_API_KEY:
        return False, "Brevo API Key not configured"

    headers = {
        "api-key": Config.BREVO_API_KEY,
        "Content-Type": "application/json",
        "Accept": "application/json"
    }

    to_list = [{"email": r if isinstance(r, str) else r.get("email")} for r in recipients]

    payload = {
        "sender": {"name": sender_name, "email": sender_email},
        "to": to_list,
        "subject": subject,
        "htmlContent": html_content
    }
    if reply_to:
        payload["replyTo"] = {"email": reply_to}

    try:
        resp = requests.post(Config.BREVO_ENDPOINT, headers=headers, json=payload, timeout=10)
        if 200 <= resp.status_code < 300:
            data = resp.json()
            return True, data.get("messageId", "brevo_ok")
        else:
            return False, f"Brevo Error ({resp.status_code}): {resp.text}"
    except Exception as e:
        return False, f"Brevo Exception: {str(e)}"

def send_via_sendgrid(sender_name, sender_email, recipients, subject, html_content, reply_to=None):
    """
    Tertiary Fallback Dispatcher: SendGrid REST API (HTTPS Whitelisted)
    """
    if not Config.SENDGRID_API_KEY:
        return False, "SendGrid API Key not configured"

    headers = {
        "Authorization": f"Bearer {Config.SENDGRID_API_KEY}",
        "Content-Type": "application/json"
    }

    to_list = [{"email": r if isinstance(r, str) else r.get("email")} for r in recipients]

    payload = {
        "personalizations": [{"to": to_list, "subject": subject}],
        "from": {"email": sender_email, "name": sender_name},
        "content": [{"type": "text/html", "value": html_content}]
    }
    if reply_to:
        payload["reply_to"] = {"email": reply_to}

    try:
        resp = requests.post(Config.SENDGRID_ENDPOINT, headers=headers, json=payload, timeout=10)
        if 200 <= resp.status_code < 300:
            return True, "sendgrid_ok"
        else:
            return False, f"SendGrid Error ({resp.status_code}): {resp.text}"
    except Exception as e:
        return False, f"SendGrid Exception: {str(e)}"

# ============================================================================
# API ROUTES
# ============================================================================

@app.route("/", methods=["GET"])
def health_check():
    """Health check ping route"""
    return jsonify({
        "status": "online",
        "service": "TechScout Email Dispatcher Microservice",
        "platform": "PythonAnywhere Free Tier",
        "whitelisted_providers": ["resend", "brevo", "sendgrid"]
    }), 200

@app.route("/api/send", methods=["POST"])
@app.route("/send", methods=["POST"])
def send_email():
    """
    Main Email Dispatch Endpoint
    Validates TechScout Token, executes Multi-Provider Fallback Chain
    """
    # 1. Authenticate Request
    auth_header = request.headers.get("Authorization", "")
    expected_token = Config.TECHSCOUT_API_SECRET

    token = ""
    if auth_header.startswith("Bearer "):
        token = auth_header[7:].strip()
    elif request.headers.get("X-API-KEY"):
        token = request.headers.get("X-API-KEY", "").strip()

    if not token or token != expected_token:
        logger.warning("Unauthorized email send attempt with invalid token.")
        return jsonify({
            "success": False,
            "error": "Unauthorized. Invalid or missing API Bearer token."
        }), 401

    # 2. Parse & Validate Payload
    try:
        data = request.get_json(force=True)
    except Exception:
        return jsonify({"success": False, "error": "Malformed JSON payload."}), 400

    if not data:
        return jsonify({"success": False, "error": "Empty request body."}), 400

    # Extract fields
    to_raw = data.get("to")
    subject = data.get("subject", "").strip()
    html_content = data.get("html", "").strip()
    from_raw = data.get("from", "")
    reply_to = data.get("reply_to", "")
    template_key = data.get("template_key", "standard")

    if not to_raw:
        return jsonify({"success": False, "error": "Field 'to' is required."}), 400
    if not subject:
        return jsonify({"success": False, "error": "Field 'subject' is required."}), 400
    if not html_content:
        return jsonify({"success": False, "error": "Field 'html' is required."}), 400

    # Normalize recipient list
    recipients = to_raw if isinstance(to_raw, list) else [to_raw]
    sender_name, sender_email = parse_sender(from_raw)

    logger.info(f"Dispatching '{template_key}' email to {recipients} via fallback chain...")

    # 3. Multi-Provider Fallback Execution
    # Priority 1: Resend
    success, result = send_via_resend(sender_name, sender_email, recipients, subject, html_content, reply_to)
    if success:
        logger.info(f"Delivered via Resend: {result}")
        return jsonify({
            "success": True,
            "provider": "resend",
            "message_id": result,
            "recipients": recipients
        }), 200

    logger.warning(f"Resend dispatch failed ({result}), failing over to Brevo...")

    # Priority 2: Brevo
    success, result = send_via_brevo(sender_name, sender_email, recipients, subject, html_content, reply_to)
    if success:
        logger.info(f"Delivered via Brevo: {result}")
        return jsonify({
            "success": True,
            "provider": "brevo",
            "message_id": result,
            "recipients": recipients
        }), 200

    logger.warning(f"Brevo dispatch failed ({result}), failing over to SendGrid...")

    # Priority 3: SendGrid
    success, result = send_via_sendgrid(sender_name, sender_email, recipients, subject, html_content, reply_to)
    if success:
        logger.info(f"Delivered via SendGrid: {result}")
        return jsonify({
            "success": True,
            "provider": "sendgrid",
            "message_id": result,
            "recipients": recipients
        }), 200

    # All providers failed
    logger.error(f"All upstream providers failed: {result}")
    return jsonify({
        "success": False,
        "error": "All upstream delivery providers failed.",
        "last_error": result
    }), 502

if __name__ == "__main__":
    app.run(debug=True, port=5000)
```

---

### `.env` File Example

```env
# Shared secret token (Paste this in TechScout Admin Mail Settings)
TECHSCOUT_API_SECRET=ts_live_sec_994821a8b7c6d5e4f3a2b1

# Upstream HTTPS API Provider Keys (Get free keys from resend.com, brevo.com, sendgrid.com)
RESEND_API_KEY=re_123456789abcdefghijklmnopqrstuvwxyz
BREVO_API_KEY=xkeysib-123456789abcdefghijklmnopqrstuvwxyz
SENDGRID_API_KEY=SG.123456789abcdefghijklmnopqrstuvwxyz

# Default sender identity
DEFAULT_FROM_EMAIL=noreply@techscout.online
DEFAULT_FROM_NAME=TechScout Platform
```

---

## 5. Step-by-Step PythonAnywhere Setup & Deployment

### Step 1: Create Free Account on PythonAnywhere
1. Sign up at [https://www.pythonanywhere.com](https://www.pythonanywhere.com) (Choose **Create a Beginner Account**).
2. Note your username (e.g. `techscoutmail`). Your webapp URL will be:
   `https://techscoutmail.pythonanywhere.com`

---

### Step 2: Open a Bash Console on PythonAnywhere
1. Go to the **Consoles** tab on PythonAnywhere.
2. Under "Start a new console", click **Bash**.
3. Create the project directory:
   ```bash
   mkdir -p ~/techscout_mail_api
   cd ~/techscout_mail_api
   ```

---

### Step 3: Create Virtual Environment & Install Dependencies
Run inside the Bash console:
```bash
# Create virtual environment with Python 3.10
python3.10 -m venv venv

# Activate virtualenv
source venv/bin/activate

# Install Flask and Requests
pip install flask requests python-dotenv
```

---

### Step 4: Create Project Files
Create the files directly inside `~/techscout_mail_api/`:

```bash
# Create .env
nano .env
# (Paste your TECHSCOUT_API_SECRET and API keys, then save with Ctrl+O, Enter, Ctrl+X)

# Create config.py
nano config.py
# (Paste the config.py code above)

# Create app.py
nano app.py
# (Paste the app.py code above)
```

---

### Step 5: Configure PythonAnywhere Web App
1. Go to the **Web** tab on the PythonAnywhere dashboard.
2. Click **Add a new web app**.
3. In the wizard:
   - Select **Manual configuration** (do not select Flask wizard, as we use a custom virtualenv).
   - Select **Python 3.10**.
4. Configure Paths in the Web Tab:
   - **Source code**: `/home/<username>/techscout_mail_api`
   - **Working directory**: `/home/<username>/techscout_mail_api`
   - **Virtualenv path**: `/home/<username>/techscout_mail_api/venv`
5. Configure the **WSGI configuration file**:
   - Click on the link `/var/www/<username>_pythonanywhere_com_wsgi.py`.
   - Delete all placeholder code and replace it with:

```python
import sys
import os

# Project path
project_home = '/home/<YOUR_USERNAME>/techscout_mail_api'
if project_home not in sys.path:
    sys.path.insert(0, project_home)

# Import the Flask application object
from app import app as application
```
*(Replace `<YOUR_USERNAME>` with your actual PythonAnywhere username).*

6. Click the green **Reload <username>.pythonanywhere.com** button at the top.

---

### Step 6: Test the API Endpoint
From your local terminal, PowerShell, or Postman, run:

```bash
curl -X POST https://<YOUR_USERNAME>.pythonanywhere.com/api/send \
  -H "Authorization: Bearer ts_live_sec_994821a8b7c6d5e4f3a2b1" \
  -H "Content-Type: application/json" \
  -d '{
    "from": "TechScout <noreply@techscout.online>",
    "to": ["test@example.com"],
    "subject": "TechScout Test API Email",
    "html": "<h2>Hello from TechScout PythonAnywhere Microservice!</h2><p>This is a live test.</p>"
  }'
```

Expected Response:
```json
{
  "success": true,
  "provider": "resend",
  "message_id": "49a1b2c3-d4e5-6f7a-8b9c-0d1e2f3a4b5c",
  "recipients": ["test@example.com"]
}
```

---

## 6. Configuring TechScout to Use Your New API

Once deployed, link TechScout to your PythonAnywhere API via the **Admin Command Center**:

1. Log in as Super Admin (`admin@techscout.n`).
2. Navigate to **Admin Dashboard &rarr; Mail & SMTP Hub** (`/admin/mail`).
3. Under **Mail Driver & Dispatch Configuration**:
   - Set **Delivery Mode**: `REST API Dispatch`
   - Set **API Provider**: `Custom / PythonAnywhere API`
   - Set **API Endpoint**: `https://<YOUR_USERNAME>.pythonanywhere.com/api/send`
   - Set **API Bearer Secret Token**: `ts_live_sec_994821a8b7c6d5e4f3a2b1` (must match `.env`)
   - Set **Default Sender**: `noreply@techscout.online`
4. Click **Save Settings**.
5. Switch to the **Send Test Email** tab and click **Dispatch Test Transmission**.
6. View the real-time delivery status in the **Transmission Logs Roster** table.

---

## 7. Troubleshooting & Maintenance Guide

### 1. Viewing PythonAnywhere Error Logs
If an email fails to dispatch, check the server log on PythonAnywhere:
- Path: `/var/log/<username>.pythonanywhere.com.error.log`
- Or click the **Error log** link under the **Web** tab.

### 2. Checking the PythonAnywhere Whitelist
If you add a new upstream email provider, verify it is on the free whitelist:
- [PythonAnywhere Whitelisted Sites Documentation](https://www.pythonanywhere.com/whitelist/)
- All major providers (`api.resend.com`, `api.brevo.com`, `api.sendgrid.com`, `api.mailgun.net`, `api.postmarkapp.com`) are pre-approved on the free tier.

### 3. Automatic 3-Month Renewal Reminder
PythonAnywhere free webapps stay active for 3 months. PythonAnywhere will send a reminder email to click **"Extend"** on the Web tab, which resets the 3-month timer instantly.
