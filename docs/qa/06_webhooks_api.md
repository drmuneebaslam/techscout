# QA Test Suite: Incoming Webhook Engine & Strict Ingestion Gateway (`QA-WH-03`)

## Test Cases & Specifications

### TC-WH-001: Webhook Bearer Token Authentication Enforcement
- **Preconditions:** Webhook endpoint `/api/v1/webhook/midjourney-prompts` configured with `auth_type: bearer` and secret key `whsec_test_abc123`.
- **Action:** Send `POST /api/v1/webhook/midjourney-prompts` with no `Authorization` header.
- **Expected Result:**
  - Status `HTTP 401 Unauthorized`.
  - JSON payload: `{"success": false, "code": 401, "error": "Unauthorized: Missing or invalid bearer authorization token."}`.
  - Log entry recorded in `incoming_webhook_logs` with `status: FAILED` and `response_code: 401`.

### TC-WH-002: Strict Zero-Auto-Map Data Isolation (Preventing Unmapped Writes)
- **Preconditions:**
  - Webhook endpoint configured for `target_table: prompts`.
  - Field mapping rules explicitly define:
    - `prompt_text` -> `prompt_template` (rule: `required`)
    - `model_name` -> `ai_model` (rule: `string`)
  - Incoming payload contains extra unmapped parameters:
    `{"prompt_text": "Cyberpunk cityscape", "model_name": "Midjourney v6", "unmapped_token": "malicious_payload", "extra_notes": "secret"}`
- **Action:** Send authenticated `POST /api/v1/webhook/midjourney-prompts`.
- **Expected Result:**
  - Status `HTTP 200 OK`.
  - Row created in `contents` and `prompts` tables.
  - `prompts.prompt_template` = `"Cyberpunk cityscape"`.
  - `prompts.ai_model` = `"Midjourney v6"`.
  - Fields `unmapped_token` and `extra_notes` are **strictly excluded** from database write operations.

### TC-WH-003: Validation Rule Failure on Required & Type Mismatches
- **Preconditions:**
  - Rule: `download_url` -> `featured_image_url` (rule: `url`).
  - Rule: `generation_seed` -> `seed` (rule: `integer`).
- **Action:** Dispatch payload with `download_url: "not-a-valid-url"` and `generation_seed: "forty-two"`.
- **Expected Result:**
  - Status `HTTP 422 Unprocessable Entity`.
  - JSON response contains specific validation violation details:
    `{"success": false, "code": 422, "error": "Validation failed for field 'download_url': Must be a valid URL."}`.
  - Entire transaction is rolled back; zero rows inserted into the database.

### TC-WH-004: Captured Payloads Inspector & Real Keys Extraction
- **Preconditions:** 3 real payloads with keys `["id", "data.attributes.prompt", "timestamp"]` dispatched to `/api/v1/webhook/external-sync`.
- **Action:** Admin opens Webhook Studio modal and clicks "Captured Payloads & Detected Keys" tab (`GET /admin/incoming-webhooks/inspect/external-sync`).
- **Expected Result:**
  - UI displays exact detected keys badge cloud: `id`, `data.attributes.prompt`, `timestamp`.
  - Clicking "+ Map Field" immediately populates a new row in Tab 3 with the clicked key.
  - No synthetic or fabricated keys are displayed.

### TC-WH-005: Modal Overlay Display Architecture (Zero In-Flow Merging)
- **Preconditions:** Admin on `/admin/incoming-webhooks`.
- **Action:** Click "Edit" button or "New Ingestion Webhook" button.
- **Expected Result:**
  - Modal renders as a centered dialog overlay fixed to the viewport (`position: fixed; inset: 0; z-index: 99999;`).
  - Underlying page content is blurred (`backdrop-filter: blur(6px)`).
  - Modal content does NOT append below the table or push page elements.
  - Clicking backdrop or `&times;` closes the modal immediately.
