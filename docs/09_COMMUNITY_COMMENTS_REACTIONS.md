# TechScout — Community, Comments & Reactions Engine

> **Tasks Covered**: 291–315 (Phase 9 — Comments & Community) & 316–330 (Phase 10 — Reactions & Engagement)  
> **Key Capabilities**: 2-Level Nested Threaded Comments, 6-Reaction Matrix, Automated Spam & Profanity Filtering, Real-time Mentions, and Engagement Scoring Algorithms.

---

## 1. Threaded Commenting System (Tasks 291–300)

TechScout supports a high-performance **2-level nested commenting architecture** (Parent Comment -> Nested Replies) to maintain clarity and avoid unreadable deep thread nesting.

```
+------------------------------------------------------------------------------------+
| DISCUSSION (24 Comments)                               [ Sort: Top Rated ▼ ]       |
+------------------------------------------------------------------------------------+
|                                                                                    |
| [ User Avatar ]  [ Leave an insightful comment or question...                   ]  |
|                  [ Supports Markdown: `code`, **bold**, [links](url)            ]  |
|                  [ Cancel ]                             [ 💬 Post Comment ]        |
|                                                                                    |
| ---------------------------------------------------------------------------------- |
|                                                                                    |
| [ Avatar ] Maya Lin [Staff Editor]  📌 [PINNED]                     2 hours ago    |
| Fantastic breakdown Alex! For readers implementing vector searches on MySQL 8.0,  |
| remember that secondary indexing on `category_id` reduces query time significantly.|
|                                                                                    |
| [ 🔥 14 ] [ 💡 8 ]  |  [ 💬 Reply ]  [ 🚩 Report ]                                 |
|                                                                                    |
|    +-----------------------------------------------------------------------------+ |
|    | [ Avatar ] David Chen [Member]                              1 hour ago      | |
|    | Exactly what I needed. Did you test this with over 100k embedding records?  | |
|    | [ 🚀 4 ]  |  [ 💬 Reply ]                                                   | |
|    +-----------------------------------------------------------------------------+ |
|                                                                                    |
+------------------------------------------------------------------------------------+
```

### Key Technical Specifications:
- **Thread Hierarchy (Tasks 291–293)**: Root comments have `parent_id = NULL`. Replies point to the root comment ID. Max depth is enforced at 2 levels.
- **Editing & Deletion Windows (Tasks 294 & 295)**: Users can edit comments within a 15-minute window with an `(edited)` tag appended. Soft deletion preserves child reply structures.
- **Author & Verified Badges (Tasks 299 & 300)**: Distinct glowing badges highlight original authors (`Author`), editors (`Staff`), and top builders (`Verified Builder`).
- **Markdown & Code Support**: Client-side sanitized Markdown rendering using `DOMPurify` preventing script execution while allowing inline code, blocks, and quotes.

---

## 2. Moderation, Spam & Profanity Filter (Tasks 301–315)

```
                            POSTED COMMENT
                                  │
                                  ▼
                   [1. Rate Limiting Check]
                   - Max 3 comments per minute per IP/User
                                  │
                                  ▼
                   [2. Profanity & Link Spam Scanner]
                   - Regex dictionary of prohibited terms
                   - Disallow unverified external URL links for new users
                   - Check user reputation score
                                  │
                   ┌──────────────┴──────────────┐
                   ▼                             ▼
             [Passed Clean]               [Flagged / Suspicious]
                   │                             │
                   ▼                             ▼
            [Render Visible]              [Quarantine to Mod Queue]
```

### Tasks 303–306: Automated Filtering Engine
- **Spam Link Defense (Task 305)**: Users with `reputation_score < 50` cannot post external HTTP links without editorial pre-moderation.
- **Profanity & Abuse Dictionary (Task 304)**: Automated string sanitization replaces vulgarities with `***` or routes severe toxicity to the moderator queue.
- **Moderator Actions (Tasks 308 & 315)**: One-click actions for moderators: `Approve`, `Hide`, `Delete`, `Warn User`, `Issue Strike`.

---

## 3. 6-Reaction Matrix & Engagement Scoring (Tasks 316–330)

### Task 316: The 6-Reaction Taxonomy
Readers can react to articles, comments, tools, and prompts using 6 expressive reactions:
1. 🔥 **Fire / Hot** (`like`): General approval and excitement.
2. 🚀 **Rocket / Fast** (`rocket`): Cutting-edge breakthroughs and speed.
3. 💡 **Insightful / Clever** (`insightful`): Educational, illuminating, or well-researched.
4. 🤯 **Mind Blown** (`mind_blown`): Extraordinary technical revelations.
5. 👏 **Clap / Applause** (`clap`): Appreciation for author effort.
6. 🔖 **Bookmark / Save** (`bookmark`): Saved to personal reader library.

### Tasks 317–319: Deduplication & State Toggle
- Reactions are strictly idempotent. The database enforces a `UNIQUE(user_id, content_id, reaction_type)` constraint.
- Clicking an active reaction removes it (toggle off) and decrements counter.
- Switching reactions updates counters in a single atomic database transaction.

### Tasks 326–328: Platform Popularity & Trending Algorithm

$$Score = \frac{(V_{24h} \times 1.0) + (U_{24h} \times 2.0) + (R_{24h} \times 4.0) + (C_{24h} \times 6.0) + (S_{24h} \times 8.0)}{(T + 2)^{1.5}}$$

Where:
- $V_{24h}$: Pageviews in past 24 hours
- $U_{24h}$: Unique visitors in past 24 hours
- $R_{24h}$: Reactions count in past 24 hours
- $C_{24h}$: Comments count in past 24 hours
- $S_{24h}$: Social shares & bookmarks count
- $T$: Hours elapsed since publication (Decay factor)
- **Result**: High-velocity quality content surges into `/blog/trending` automatically.
