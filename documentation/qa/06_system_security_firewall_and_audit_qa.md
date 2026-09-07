# QA Test Guide 06: System Security, Firewall & Audit Log QA

This document specifies the step-by-step Enterprise QA testing suite for IP Firewall (`app/Core/Firewall.php`), Security Anomaly Engine (`app/Models/SecurityAnomalyEngine.php`), Input Sanitizer (`app/Core/Sanitizer.php`), Audit Logs (`app/Models/AuditLog.php`), and Defense Mechanics.

---

## Pre-Test Setup & Requirements

| Requirement | Value / Condition |
| :--- | :--- |
| **Base URL** | `http://localhost/techscout.n/` |
| **Test Tools** | cURL, Postman / Insomnia HTTP Client, Browser Developer Tools. |
| **Firewall Tables** | `firewall_rules`, `firewall_logs`, `audit_logs`, `security_anomalies`. |

---

## Test Suite 1.0: IP Firewall & Rate Limiting (`app/Core/Firewall.php`)

### Test Case 1.1: IP Blacklisting & Ban Enforcement
- **Objective**: Verify that malicious IP addresses blocked in Firewall are immediately rejected with HTTP 403.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Admin Add Firewall Rule**
   - 1.1: Log in as Admin and navigate to `/admin/firewall`.
   - 1.2: Click "Add IP Rule".
   - 1.3: Enter IP Address: `198.51.100.45`, Action: `Block / Blacklist`, Notes: `Malicious scanner IP`.
   - 1.4: Click "Save Rule".
   - 1.5: Verify record created in `firewall_rules` table.

2. **Step 2: Simulated Request from Blacklisted IP**
   - 2.1: Execute cURL request sending spoofed IP header:
     ```bash
     curl -i -H "X-Forwarded-For: 198.51.100.45" http://localhost/techscout.n/
     ```
   - 2.2: Verify response status `HTTP/1.1 403 Forbidden`.
   - 2.3: Verify response body renders Firewall Error Page (`views/errors/403.php` or `firewall_blocked.html`).
   - 2.4: Verify entry logged in `firewall_logs` table (`ip = '198.51.100.45'`, `action_taken = 'BLOCKED'`).

---

## Test Suite 2.0: XSS & Injection Defense (`app/Core/Sanitizer.php`)

### Test Case 2.1: Cross-Site Scripting (XSS) Input Cleansing
- **Objective**: Verify input sanitizer strips `<script>` payloads and malicious attributes from comment and profile forms.

#### Test Execution Matrix:
| Sub-Step | Input Vector Payload | Target Field | Expected Sanitized Result in DB |
| :--- | :--- | :--- | :--- |
| **2.1.1** | `<script>alert('XSS')</script>` | Comment `#body` | `&lt;script&gt;alert('XSS')&lt;/script&gt;` or stripped cleanly. |
| **2.1.2** | `<img src="x" onerror="stealCookies()">` | User Bio | `<img src="x">` (onerror attribute stripped). |
| **2.1.3** | `<a href="javascript:void(0)" onclick="exploit()">Link</a>` | Profile Web URL | `href="javascript:..."` rejected or sanitized to `https://...`. |

---

## Test Suite 3.0: Audit Logging & Anomaly Detection (`app/Models/AuditLog.php`)

### Test Case 3.1: Automated Audit Trail Recording
- **Objective**: Verify that sensitive administrative and user security events trigger immutable audit log records.

#### Audit Event Verification Checklist:
- [ ] User Password Reset -> Logs `EVENT_PASSWORD_RESET_SUCCESS`.
- [ ] Admin User Role Change -> Logs `EVENT_USER_ROLE_UPDATED` with `old_role` and `new_role`.
- [ ] Database Backup Download -> Logs `EVENT_DB_BACKUP_DOWNLOADED`.
- [ ] Settings Modification -> Logs `EVENT_SYSTEM_SETTINGS_MUTATED`.

---

## Pass / Fail Criteria Checklist

- [ ] IP Firewall blocks blacklisted IPs before executing application framework logic.
- [ ] Sanitizer renders XSS vectors 100% inert across all inputs.
- [ ] Security audit log records user IP, timestamp, user agent, and payload diffs.
- [ ] QA Inspector: TechScout Lead Security Auditor.
