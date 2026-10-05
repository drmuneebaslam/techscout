# TechScout Webhooks: Complete Mapping & Troubleshooting Guide

This guide is designed to prevent data ingestion errors by explaining exactly how the TechScout Incoming Webhook Engine interprets your JSON payloads, resolves missing data, and what happens when things are mapped incorrectly.

## 1. Does the System Auto-Create Missing Categories & Tags?

**YES.** The TechScout Webhook Engine features an intelligent **Auto-Resolution System**.

*   **Categories:** If you send a payload with `"category_name": "New AI Topic"` (or `"category_main": "New AI Topic"`), the engine first searches the `categories` table for an exact match or slug match. If it does not exist, the engine will **automatically create it** with `applicable_to = 'all'` and instantly link your new content to this newly created category ID.
*   **Tags & Secondary Categories:** If you pass a `tags` array like `[{"name": "Streetwear"}]`, the engine checks the `tags` table. If "Streetwear" is missing, it is instantly generated and mapped to your content via the `content_tags` junction table.

You **never** need to manually ensure categories or tags exist before sending a payload.

---

## 2. The Auto-Alias Engine (Forgiving Mapping)

To make integrations with external tools (like PromptPlum, Zapier, or Make.com) easier, the engine has a built-in "Alias Decoder". If you send a field name that doesn't perfectly match our database schema, the engine will intercept it and map it correctly.

### Supported Aliases

**Titles & Names**
*   If you send `name` or `prompt_title`, it automatically maps to `title`.

**Summaries & Descriptions**
*   If you send `description` or `caption`, it automatically maps to `summary`.

**Images**
*   If you send `image`, `image_url`, `photo`, `field`, or `thumbnail`, it automatically maps to `featured_image_url`.

**Prompts**
*   If you send `prompt`, `prompt_text`, `template`, or `prompt_body`, it automatically maps to `prompt_template`.

**Categories**
*   If you send `category`, `category_main`, or `topic`, it automatically maps to the internal Category Resolver.

**Models / Engines**
*   If you send `model`, `models`, or `engine`, it automatically maps to `target_models` (and properly encodes it as a JSON array).

*Because of this Alias Engine, the payload you provided from PromptPlum works perfectly even if the keys aren't an exact match to our DB columns.*

---

## 3. What Happens If You Map Something Wrong? (Error Handling)

### Scenario A: Sending a Field That Doesn't Exist (And Has No Alias)
If you send `"my_custom_data": "value"`, and `my_custom_data` is NOT a column in the target table (e.g., `contents` or `prompts`), the engine will simply **ignore it**.
*   **Will it crash?** No.
*   **Will it cause a 500 Error?** No. The `IncomingWebhookEngine` dynamically checks `getColumnMeta()` for the target table. It safely drops any keys that do not exist in the actual database schema.

### Scenario B: Sending the Wrong Data Type
If a column in the database is an `integer` (like `reading_time_mins`) and you send a string (like `"reading_time_mins": "five"`), the database query will fail.
*   **What happens?** The transaction will roll back. The content will NOT be partially saved.
*   **How to debug?** You can navigate to **Admin Panel > Logs & Tools > Webhook Logs**. You will see the exact payload that failed, marked with a `failed` status, and the `error_message` will tell you exactly which SQL constraint or type casting failed.

### Scenario C: Forgetting a REQUIRED Field
If you forget a strictly required field (like `prompt_template` for the `prompts` table, or `title` for the `contents` table):
*   **What happens?** The insertion will fail because the database enforces `NOT NULL`.
*   **How to debug?** Check the **Webhook Logs** in the admin panel. The error message will say something like: `Integrity constraint violation: Column 'prompt_template' cannot be null`.

### Scenario D: Missing or Invalid JSON
If you send malformed JSON (e.g., missing a closing bracket):
*   **What happens?** The server will instantly return an HTTP 400 Bad Request: `Invalid JSON payload`. This doesn't even reach the database layer.

---

## 4. The 3 Golden Rules for Flawless Webhooks

To guarantee zero errors, follow these rules:

1.  **Always use the `tables` nesting object for content:**
    Because our system uses an Extension Table Architecture (e.g., `contents` + `prompts`), your payload MUST wrap the data in `"tables": { "contents": {...}, "prompts": {...} }`. If you send a flat JSON object for a prompt, the engine won't know how to split the data between the two tables.
2.  **Let the Engine Handle Timestamps & IDs:**
    Never try to send `id`, `uuid`, `created_at`, or `updated_at`. The engine handles this flawlessly, ensuring UUIDs are cryptographically secure and timestamps match the server timezone perfectly.
3.  **Check the Webhook Logs UI:**
    If an automation from Make.com or Zapier says "Success" but you don't see the post on the frontend, check the **Webhook Logs** in the TechScout Admin Panel. The system meticulously logs every single incoming request, its raw payload, and any silent background errors.
