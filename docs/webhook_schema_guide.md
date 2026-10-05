# TechScout Live Ingestion & Webhook Schema Guide
*Master Reference for 100X Enhanced Data Ingestion*

## Overview
The **TechScout Incoming Webhook Engine** utilizes a highly advanced, multi-table synchronous ingestion architecture. You do not need to provide `id`, `uuid`, `created_at`, or `updated_at` fields in your JSON payloads—the system automatically generates these database constraints and timestamps securely.

This document serves as the absolute guide on structuring your JSON payloads for both **Single-Table** and **Multi-Table Transactions**.

---

## 1. Single Table vs. Multi-Table Ingestion

### Single Table Payloads
Use this if you are only injecting standard rows into a single table (e.g. `categories` or `tags`).
```json
{
  "name": "Generative AI",
  "slug": "generative-ai",
  "applicable_to": "all"
}
```

### Multi-Table Transaction Payloads (The "100X" Engine)
TechScout's core utilizes an **Extension Table Architecture**. All major content types (Articles, Prompts, AI Tools, Custom GPTs) share a base row in the `contents` table. To properly ingest a complete Post/Prompt/Tool, you **must** use the Multi-Table Payload.

The engine will automatically generate the `content_id` for the base `contents` row and seamlessly link it to your extension table (e.g. `prompts`) and your `tags`.

```json
{
  "tables": {
    "contents": {
      "title": "My Awesome Content",
      "summary": "Short snippet about this post.",
      "content_type": "article",
      "category_name": "Artificial Intelligence"
    },
    "articles": {
      "difficulty_level": "intermediate",
      "reading_time_mins": 5
    },
    "tags": [
      { "name": "AI" },
      { "name": "Tech" }
    ]
  }
}
```

---

## 2. Supported Core Extension Data Models

If you are ingesting any of the major content types below, you must map the **base metadata** to the `contents` table, and the **specific data** to the corresponding extension table.

### `contents` (Base Table - REQUIRED for all posts)
Every post, article, tool, or prompt requires this base structure.
* **Auto-generated:** `id`, `uuid`, `created_at`, `updated_at`, `published_at`, `status` (defaults to 'published').
* **Fields:**
  * `title` (string, required): Title of the post
  * `slug` (string, optional): URL friendly slug (auto-generated if omitted)
  * `summary` (string, optional): Short excerpt
  * `content_type` (enum: `article`, `prompt`, `ai_tool`, `gpt`, `campaign`)
  * `category_id` (integer, optional) OR `category_name` (string, auto-resolves ID)
  * `author_id` (integer, defaults to 1)
  * `featured_image_url` (string, optional)

---

### A. Ingesting an "Article"
*Requires: `contents` + `articles`*

**`articles` Fields:**
* `reading_time_mins` (integer)
* `difficulty_level` (enum: `beginner`, `intermediate`, `advanced`, `expert`)
* `table_of_contents` (JSON array string)
* `code_snippets_count` (integer)

**Example Payload:**
```json
{
  "tables": {
    "contents": {
      "title": "Understanding Neural Networks",
      "content_type": "article",
      "category_name": "Machine Learning"
    },
    "articles": {
      "difficulty_level": "advanced",
      "reading_time_mins": 12
    }
  }
}
```

---

### B. Ingesting an "AI Image Prompt" (Complete Master Example)
*Requires: `contents`, `prompts`, `prompt_images`, `content_categories` and `tags`*

To create a highly detailed Image Prompt, utilizing the **latest schema** which supports distinct secondary categories and high-quality prompt image galleries, you will map the following:

1. **`contents`**: Handles the universal metadata (Title, Primary Category, Featured Image URL).
2. **`prompts`**: Core prompt data (`prompt_template`, `target_models`, etc.).
3. **`prompt_images`**: If you have multiple gallery images for the prompt.
4. **`content_categories`**: Used to attach **Secondary Categories**.
5. **`tags`**: Acts as granular descriptor tags.

**`prompts` Key Fields Supported (Latest Schema):**
* `prompt_type` (enum: `text` or `image`)
* `prompt_template` (string, required): The actual prompt text
* `template_variables` (JSON array string)
* `target_models` (JSON array string, e.g. `["Midjourney", "DALL-E 3"]`)
* `sample_output` (string)

**`prompt_images` Key Fields Supported:**
* `image_url` (string, required)
* `caption` (string)
* `sort_order` (integer)

**Comprehensive Image Prompt Payload Example:**
```json
{
  "tables": {
    "contents": {
      "title": "Cinematic Cyberpunk Cityscape in the Rain",
      "content_type": "prompt",
      "category_name": "Image Generation", 
      "featured_image_url": "https://example.com/cyberpunk-city-preview.jpg",
      "summary": "A highly detailed neon-lit street level view of a futuristic city."
    },
    "prompts": {
      "prompt_type": "image",
      "prompt_template": "A neon-lit cyberpunk city street level in the rain, 8k resolution, cinematic lighting, photorealistic, reflections on wet pavement --v 6.0",
      "target_models": "[\"Midjourney v6\"]",
      "sample_output": "A sprawling neon metropolis at midnight..."
    },
    "prompt_images": [
      {
        "image_url": "https://example.com/gallery1.jpg",
        "caption": "Variant 1 - Neon Blue",
        "sort_order": 1
      },
      {
        "image_url": "https://example.com/gallery2.jpg",
        "caption": "Variant 2 - Rain Heavy",
        "sort_order": 2
      }
    ],
    "content_categories": [
      { "category_id": 12, "is_primary": 0 },
      { "category_id": 15, "is_primary": 0 }
    ],
    "tags": [
      { "name": "Cyberpunk" },
      { "name": "Sci-Fi" },
      { "name": "Neon Lighting" }
    ]
  }
}
```
*Note: In the example above, `category_name: "Image Generation"` on the `contents` table automatically maps the **Primary Category**. The `content_categories` array links additional **Secondary Categories**, while `tags` adds discoverability markers.*

---

### C. Real-World Example: External Prompt Ingestion (e.g. PromptPlum)
*How to seamlessly ingest scraped or third-party prompt data.*

Let's assume you scraped the following raw data:
* **Title:** 10mm Fisheye Top-Down Portrait AI Prompt
* **Category:** Cowboy
* **Model:** Gemini, Gemini Watermark Remover
* **Tags (Secondary Categories):** Creative, Fashion, Streetwear, Urban
* **Prompt Text:** A high-fashion editorial studio portrait...
* **Description:** Use this AI prompt to transform...
* **Image URL:** https://img.promptplum.com/...webp
* **Source URL:** https://promptplum.com/prompt/...

**The Professional TechScout Payload Mapping:**
```json
{
  "tables": {
    "contents": {
      "title": "10mm Fisheye Top-Down Portrait AI Prompt",
      "content_type": "prompt",
      "status": "published",
      "category_name": "Cowboy",
      "summary": "Use this AI prompt to transform any photo into a dramatic 10mm fisheye portrait featuring a top-down angle, streetwear jacket, and bold red studio background.",
      "featured_image_url": "https://img.promptplum.com/cdn-cgi/image/q=78,scq=50,width=1400,height=1400,fit=contain,f=auto/promptImages/a26ef25d-8550-4cdc-94b4-8f7babcdb90a.webp",
      "canonical_url": "https://promptplum.com/prompt/fisheye-top-down-portrait"
    },
    "prompts": {
      "prompt_type": "image",
      "prompt_template": "A high-fashion editorial studio portrait of the person from the uploaded photo, captured using an ultra-wide 10mm fisheye lens from a dramatic high top-down angle, creating strong perspective distortion with a noticeably larger head and a compressed body. The subject wears black oversized sunglasses and a plain black cap, with their left hand actively adjusting the sunglasses, and features a confident upward gaze. They are dressed in a color-block puffer jacket featuring panels of blue, yellow, and orange, worn over a black t-shirt, accessorized with a gold chain and white sneakers. The background is a bold red studio seamless with a smooth gradient, high contrast, sharp editorial lighting, captured in a 4:5 aspect ratio.",
      "target_models": "[\"Gemini\", \"Gemini Watermark Remover\"]"
    },
    "prompt_images": [
      {
        "image_url": "https://img.promptplum.com/cdn-cgi/image/q=78,scq=50,width=1400,height=1400,fit=contain,f=auto/promptImages/a26ef25d-8550-4cdc-94b4-8f7babcdb90a.webp",
        "sort_order": 1
      }
    ],
    "tags": [
      { "name": "Cowboy" },
      { "name": "Creative" },
      { "name": "Fashion" },
      { "name": "Streetwear" },
      { "name": "Urban" }
    ]
  }
}
```
**Why this is the 100X Professional Standard:**
1. **`category_name`:** Resolves "Cowboy" to a primary category automatically.
2. **`tags` as Secondary Categories:** Passing the strings in the `tags` array ensures the platform automatically links them (or creates them if missing) without needing exact database `category_id` matches.
3. **`canonical_url`:** We store the source URL in the `contents.canonical_url` column which heavily protects SEO authority.
4. **`prompt_images` + `featured_image_url`:** The image serves as the main thumbnail on the `contents` record and is also added to the `prompt_images` gallery for multi-image sliders on the prompt's detail page.

---

### D. Ingesting an "AI Tool" (Directory Listings)
*Requires: `contents` + `ai_tools`*

**`ai_tools` Fields:**
* `website_url` (string, required)
* `pricing_model` (enum: `free`, `freemium`, `paid`, `subscription`, `open_source`)
* `free_trial_available` (boolean: 0 or 1)
* `pricing_starting_at` (string)
* `api_available` (boolean: 0 or 1)
* `rating_avg` (float)

**Example Payload:**
```json
{
  "tables": {
    "contents": {
      "title": "OpenAI ChatGPT",
      "content_type": "ai_tool"
    },
    "ai_tools": {
      "website_url": "https://chat.openai.com",
      "pricing_model": "freemium",
      "api_available": 1
    }
  }
}
```

---

## 3. Support Tables & Taxonomies

### `tags`
You can attach tags dynamically inside a Multi-Table Payload. The engine will auto-create missing tags and link them to the content via `content_tags`.
**Fields:**
* `name` (string)
* `slug` (string, optional - auto-generated from name)

### `categories`
**Fields:**
* `name` (string, required)
* `slug` (string, optional)
* `description` (string)
* `icon_class` (string)
* `parent_id` (integer, optional)
* `applicable_to` (enum: `all`, `article`, `ai_tool`, `prompt`, `gpt`)

### `ticket_internal_notes` & `ticket_canned_macros`
If managing helpdesk/support tickets:
* `ticket_internal_notes`: Requires `ticket_id`, `author_id`, `note_content`.
* `ticket_canned_macros`: Requires `title`, `body_content`, `usage_count`, `applicable_to`.

---

## Webhook Log Filtering & UI Mapping

When setting up your endpoint in the **Live Ingestion Studio**:
1. Select the tables required (e.g., `contents`, `prompts`, `tags`).
2. Map your external JSON keys (e.g., `data.post_title`) to the unified columns (e.g., `contents.title`).
3. The dropdown explicitly prefixes tables (`Table: contents`, `Table: prompts`) to prevent collisions if two tables have a `body_content` column.
4. You can filter logs inside the **Global Activity Logs** tab by Webhook Endpoint, Status (Success/Failed/Simulated/Duplicate), and Date.
