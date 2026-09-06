# TechScout — AI Tools, GPTs & Prompt Engine

> **Tasks Covered**: 161–190 (Phase 5 — AI / Tech Resource System) & 486–500 (Phase 16 — Search & Discovery)  
> **Key Capabilities**: Interactive SaaS & AI Directory, Custom GPT Hub, Dynamic Prompt Variable Injector, One-Click Clipboard Engine, and Typo-Tolerant Search.

---

## 1. AI Tools & Software Directory (Tasks 161–171 & 187–190)

The AI Tools directory (`/tools`) provides an interactive catalog of developer tools, foundation models, and SaaS applications.

```
+------------------------------------------------------------------------------------+
|                                AI TOOLS DIRECTORY                                  |
| [Search Tools...] | Filter: [All Categories ▼] [Pricing: Free/Freemium ▼] [Sort ▼] |
+------------------------------------------------------------------------------------+
|                                                                                    |
|  +---------------------------+  +---------------------------+  +-----------------+  |
|  | [Icon] Cursor AI          |  | [Icon] v0 by Vercel       |  | [Icon] Perplex  |  |
|  | ⭐ 4.9 (1,240 reviews)    |  | ⭐ 4.8 (890 reviews)      |  | ⭐ 4.9 (2,100)  |  |
|  | Freemium | Dev Tools      |  | Freemium | UI Gen         |  | Freemium | AI   |
|  | AI-first code editor with |  | Generative UI engine for  |  | Conversational  |  |
|  | deep codebase indexing.   |  | React & Tailwind markup.  |  | search engine.  |
|  |                           |  |                           |                 |  |
|  | [View Details] [Visit ↗]  |  | [View Details] [Visit ↗]  |  | [View Details]  |  |
|  +---------------------------+  +---------------------------+  +-----------------+  |
+------------------------------------------------------------------------------------+
```

### Key Tool Features & Specifications:
1. **Pricing Models (Task 163)**: Categorized as `Free`, `Freemium`, `Paid`, `Subscription`, or `Open Source`.
2. **Feature Comparison & Alternatives Matrix (Tasks 165 & 166)**: Interactive side-by-side comparison table comparing features, pricing, API availability, and performance benchmarks against competing tools.
3. **Community Reviews & Verified Ratings (Tasks 167 & 168)**: 5-star rating system with verified user badges, pros/cons breakdown, and use-case tags.
4. **Affiliate & Direct Attribution (Task 171)**: Outbound links tracked securely via redirect gateway (`/go/{tool_slug}`) with automated UTM parameters.

---

## 2. Custom GPTs & Agent Catalog (Tasks 172–177)

The GPT Directory (`/gpts`) curates the best custom OpenAI GPTs and autonomous agents.

### Data Attributes & Display:
- **Model Base (Task 172)**: Targets `GPT-4o`, `Claude 3.5 Sonnet`, `o1-preview`, or `Gemini 1.5 Pro`.
- **Prompt Starters (Task 173)**: Pre-configured conversation starter buttons that copy directly or deep-link to the target agent.
- **Direct Launch Gateway (Task 177)**: Deep-links readers directly into the target platform with attribution tracking.
- **Submission & Approval Workflow (Tasks 174 & 175)**: Creators submit custom GPT links -> validated by editors for prompt quality, safety, and functionality -> published.

---

## 3. Engineering Prompt Library & Variable Injector (Tasks 178–186)

The Prompt Library (`/prompts`) allows builders to find, test, customize, and copy production-tested system and user prompts.

```
+------------------------------------------------------------------------------------+
| PROMPT: "Production PostgreSQL Schema & Index Optimizer"                           |
| Target Models: [ChatGPT 4o] [Claude 3.5 Sonnet] | Category: Database Architecture  |
+------------------------------------------------------------------------------------+
|                                                                                    |
| [Dynamic Variable Customizer]                                                      |
| 1. Database Table Name:  [ users, orders, order_items            ]                 |
| 2. Expected Daily Load:  [ 5,000,000 queries                     ]                 |
| 3. Target Query Latency: [ < 10ms                                ]                 |
|                                                                                    |
| [Generated Output Preview]                                                         |
| +--------------------------------------------------------------------------------+ |
| | You are an expert Principal Database Reliability Engineer.                     | |
| | Analyze the following schema for tables: users, orders, order_items.           | |
| | Optimize indexes for 5,000,000 queries daily with latency target < 10ms.       | |
| +--------------------------------------------------------------------------------+ |
|                                                                                    |
| [ 📋 Copy Customized Prompt ]     [ 💾 Save to Bookmarks ]     [ ↗ Test in LLM ]   |
+------------------------------------------------------------------------------------+
```

### Task 180 & 181: Dynamic Template Engine & Variable Injection
Prompts use standard mustache-style placeholders (`{{variable_name}}`):
```javascript
// Client-side Prompt Variable Replacement Engine
function renderPromptTemplate(template, variables) {
  let output = template;
  for (const [key, value] of Object.entries(variables)) {
    const regex = new RegExp(`{{\\s*${key}\\s*}}`, 'gi');
    output = output.replace(regex, value.trim() || `[${key}]`);
  }
  return output;
}
```

### Task 182: One-Click Copy Mechanics & Analytics
- Clicking **Copy Prompt** triggers native `navigator.clipboard.writeText(output)`.
- Dispatches a subtle green Toastify alert: *"Prompt copied to clipboard!"*.
- Asynchronously increments `copy_count` in the database via `POST /api/v1/prompts/{id}/copy` to feed popularity algorithms.

---

## 4. Search Engine & Discovery Algorithms (Tasks 486–500)

```
                            USER SEARCH INPUT
                                    │
                                    ▼
                     [1. Input Normalizer & Typo Fixer]
                                    │
                                    ▼
                [2. FullText Weighted MySQL Query (MATCH AGAINST)]
                      - Title match (Weight: 4x)
                      - Tags match (Weight: 3x)
                      - Summary match (Weight: 2x)
                      - Body match (Weight: 1x)
                                    │
                                    ▼
                [3. Multi-Entity Categorization & Faceting]
                      - Filter by Type (Articles, Tools, Prompts)
                      - Filter by Category & Author
                      - Filter by Date & Popularity
                                    │
                                    ▼
                     [4. Instant Autocomplete UI (Cmd+K)]
```

### Task 488: Instant Global Autocomplete Overlay (`Cmd + K`)
- Modal search interface accessible from anywhere on the platform via keyboard shortcut `Cmd + K` or `Ctrl + K`.
- Results render instantly with keyboard arrow navigation (`Up`, `Down`, `Enter`).
- Zero-result searches are logged into `analytics_events` to inform content acquisition and keyword gap analysis.
