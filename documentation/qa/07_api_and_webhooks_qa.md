# QA Test Guide 07: REST API & Webhooks Engine QA

This document specifies the step-by-step Enterprise QA testing suite for the REST API Endpoints (`app/Controllers/ApiController.php`), API Key Management (`views/admin/api_keys.php`), Webhook Event Delivery (`app/Core/WebhookEngine.php`), Rate Limiting, and HMAC Signature Validation.

---

## Pre-Test Setup & Requirements

| Requirement | Value / Condition |
| :--- | :--- |
| **Base URL** | `http://localhost/techscout.n/api/v1` |
| **Testing Tools** | cURL, Postman / Insomnia, Webhook Listener (`webhook.site` or local receiver). |
| **Auth Headers** | `X-API-Key: <valid_api_key>`, `X-Signature: <hmac_sha256>`. |

---

## Test Suite 1.0: API Authentication & Key Management

### Test Case 1.1: Request Authentication via API Key
- **Objective**: Verify API key verification, permission scope validation, and error responses for invalid keys.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Admin Generate API Key**
   - 1.1: Log in as Admin -> Open `/admin/api-keys`.
   - 1.2: Click "Generate API Key". Name: `Mobile App Client`, Scopes: `read:content, write:comments`.
   - 1.3: Save Key Token (e.g. `ts_live_key_9983471289347`).

2. **Step 2: API Call with Valid Key**
   - 2.1: Send GET Request to `/api/v1/content/articles`:
     ```bash
     curl -H "X-API-Key: ts_live_key_9983471289347" http://localhost/techscout.n/api/v1/content/articles
     ```
   - 2.2: Verify response status `200 OK` and JSON structure containing article array.

3. **Step 3: API Call with Invalid Key**
   - 3.1: Send request with `X-API-Key: invalid_key_string`.
   - 3.2: Verify status `401 Unauthorized` and payload `{"error": "Invalid API Key supplied."}`.

---

## Test Suite 2.0: Webhook Delivery & HMAC Signature Engine (`app/Core/WebhookEngine.php`)

### Test Case 2.1: Article Published Event Webhook Trigger
- **Objective**: Verify event payload construction, HTTP POST dispatch to subscriber URL, and HMAC SHA256 header.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Register Webhook Endpoint**
   - 1.1: Navigate to `/admin/webhooks`.
   - 1.2: Add Endpoint: Target URL `https://webhook.site/test-uuid`, Secret Key `whsec_SecretKey123!`.
   - 1.3: Select Event Trigger: `article.published`.

2. **Step 2: Trigger Event**
   - 2.1: Publish an article in Admin Content Queue.
   - 2.2: Verify `WebhookEngine::dispatch('article.published', $articleData)` executes in background.

3. **Step 3: Webhook Delivery & Signature Check**
   - 3.1: Inspect payload received at listener URL.
   - 3.2: Verify header `X-TechScout-Signature` contains valid HMAC SHA256 signature calculated using `whsec_SecretKey123!`.
   - 3.3: Verify payload JSON contains article ID, title, slug, and timestamp.
   - 3.4: Verify entry created in `webhook_deliveries` table (`status_code = 200`, `attempts = 1`).

---

## Pass / Fail Criteria Checklist

- [ ] Valid API keys grant scoped access; invalid keys return 401.
- [ ] API rate limit throttles requests over quota with 429.
- [ ] Webhook Engine signs payloads with verifiable HMAC SHA256 signatures.
- [ ] Failed webhook endpoints execute exponential backoff retries.
- [ ] QA Inspector: TechScout Lead API QA.
