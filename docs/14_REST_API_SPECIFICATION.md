# TechScout — RESTful API Specification (v1)

> **Tasks Covered**: 586–609 (Phase 22 — API Platform)  
> **Base URL**: `https://techscout.online/api/v1` (or `http://localhost/techscout.n/api/v1`)  
> **Protocols**: HTTPS, JSON Payloads, Bearer Token / Session Cookies, Standard RFC 7807 Error Responses.

---

## 1. Authentication & Common Headers (Tasks 586–592)

### Common Request Headers:
```http
Content-Type: application/json
Accept: application/json
X-CSRF-Token: <csrf_token_string>
Authorization: Bearer <jwt_or_api_key> (Optional for public endpoints)
```

### Common Standard Response Envelope:
```json
{
  "success": true,
  "data": { ... },
  "meta": {
    "page": 1,
    "per_page": 20,
    "total": 142
  }
}
```

### Standard HTTP Status Codes:
- `200 OK`: Request succeeded.
- `201 Created`: Resource created.
- `400 Bad Request`: Validation error in payload.
- `401 Unauthorized`: Missing or invalid authentication.
- `403 Forbidden`: Insufficient permissions (RBAC).
- `404 Not Found`: Target resource does not exist.
- `429 Too Many Requests`: Rate limit exceeded (60 req/min).
- `500 Internal Server Error`: Server exception.

---

## 2. API Endpoints Catalog (Tasks 593–605)

### A. Authentication Endpoints
- `POST /api/v1/auth/register` — Register a new member/author.
  - *Payload*: `{"username": "alex", "email": "alex@example.com", "password": "SecurePassword123!"}`
- `POST /api/v1/auth/login` — Authenticate and issue session/token.
  - *Payload*: `{"email": "alex@example.com", "password": "SecurePassword123!"}`
- `POST /api/v1/auth/logout` — Invalidate session and tokens.
- `GET /api/v1/auth/me` — Return current authenticated user profile and roles.

### B. Public Content & Directory Endpoints (Tasks 593–599)
- `GET /api/v1/content` — List paginated content items.
  - *Query Params*: `?type=article&category=ai&sort=trending&page=1&limit=20`
- `GET /api/v1/content/{slug}` — Retrieve full content item by slug (increments view count).
- `GET /api/v1/tools` — List AI tools directory with filters (`?pricing=freemium&verified=1`).
- `GET /api/v1/prompts/{slug}` — Fetch prompt details & variable schema.
- `POST /api/v1/prompts/{id}/copy` — Record prompt copy action and return rendered template.

### C. Search & Discovery Endpoints (Task 600)
- `GET /api/v1/search` — Instant fulltext search across articles, tools, and prompts.
  - *Query Params*: `?q=rag+pipeline&type=all&limit=10`
  - *Response*: Categorized results object with highlighted matching snippets.

### D. Comments & Reactions Endpoints (Tasks 602 & 603)
- `GET /api/v1/content/{id}/comments` — Retrieve 2-level nested comment tree.
- `POST /api/v1/content/{id}/comments` — Post a new root comment or reply.
  - *Payload*: `{"parent_id": null, "comment_text": "Great insights on MySQL indexing!"}`
- `PUT /api/v1/comments/{id}` — Edit an existing comment within the 15-minute window.
- `DELETE /api/v1/comments/{id}` — Soft delete comment.
- `POST /api/v1/content/{id}/react` — Toggle reaction (`like`, `clap`, `rocket`, etc.).
  - *Payload*: `{"reaction_type": "rocket"}`

### E. Author Studio Endpoints (Tasks 594 & 601)
- `GET /api/v1/author/stats` — Author's real-time read counts, engaged reads, and credits.
- `POST /api/v1/author/drafts` — Create a new draft.
- `PUT /api/v1/author/drafts/{id}/autosave` — Debounced autosave endpoint.
- `POST /api/v1/author/drafts/{id}/submit` — Transition draft to `in_review`.
- `POST /api/v1/author/media/upload` — Upload image file (multipart/form-data) -> returns WebP URLs.

### F. High-Speed Client Telemetry (Task 601)
- `POST /api/v1/analytics/event` — High-speed beacon endpoint for client telemetry.
  - *Payload*: `{"event_type": "scroll_depth", "content_id": 482, "duration_seconds": 120, "metadata": {"depth": "75%"}}`

---

## 3. Rate Limiting & Abuse Prevention (Tasks 607–609)

| Client Category | Rate Limit | Burst Limit | Enforcement Mechanism |
|---|---|---|---|
| **Anonymous / Public** | 60 requests / minute | 15 / 5 seconds | Redis / MySQL Memory Table by IP |
| **Authenticated Member** | 180 requests / minute | 30 / 5 seconds | User ID Token Bucket |
| **Author Studio** | 300 requests / minute | 50 / 5 seconds | User ID Token Bucket |
| **Telemetry Beacon** | 600 requests / minute | 100 / 5 seconds | Aggregated In-Memory Buffer |
