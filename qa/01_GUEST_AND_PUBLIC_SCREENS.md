# 🌐 QA Specification: Guest & Public Screens

> **Scope**: All unauthenticated visitor views, landing hubs, discovery feeds, directories, global search, and public member/creator profiles.  
> **Target Environment**: `http://localhost/techscout.n`  

---

## 📋 Public Screen Matrix

| ID | Screen Title | Route Path | Primary Features & Modules |
|---|---|---|---|
| 1.1 | Homepage Discovery Engine | `GET /` | Hero banner, trending topics, featured slider, categorized feeds |
| 1.2 | Technical Articles Feed | `GET /blog` | Multi-column article grid, sorting (latest/popular), category filter |
| 1.3 | Trending Articles Radar | `GET /blog/trending` | Real-time trending ranking badges (#1, #2, #3), read velocity |
| 1.4 | Single Technical Publication | `GET /article/{slug}` | Reading estimator, table of contents, bookmark, reactions, comments |
| 1.5 | AI Tools Radar Directory | `GET /tools` | Tool cards, pricing tags (Free/Paid/OSS), category filters, live search |
| 1.6 | Single AI Tool Deep-Dive | `GET /tool/{slug}` | Tool specification, feature checklist, pros/cons, official link |
| 1.7 | Prompt Blueprints Library | `GET /prompts` | LLM prompts, model tags (Claude, GPT-4, Llama), one-click copy |
| 1.8 | Custom GPT Agents Directory | `GET /gpts` | AI agent personas, direct ChatGPT launch link, usage guidelines |
| 1.9 | Developer Hackathons & Challenges | `GET /campaigns` | Prize pools, submission countdown timer, guidelines, leaderboard |
| 1.10 | Topics & Category Taxonomy | `GET /topics`, `/topic/{slug}` | 100+ technical category cards, article counts, topic follow button |
| 1.11 | Global Search Palette | `GET /search?q={query}` | Multi-entity live search across articles, tools, prompts, authors |
| 1.12 | Engineering Jobs Board | `GET /jobs` | Remote developer listings, company cards, application links |
| 1.13 | Contact & Editorial Desk | `GET /contact` | Inquiry submission form, department routing, response alert |
| 1.14 | Member Sign-In Hub | `GET /auth/login` | Email/password login, remember me, quick password recovery link |
| 1.15 | Platform Registration | `GET /auth/register` | Account creation, username validation, terms acceptance |
| 1.16 | Public Creator Profile | `GET /profile/{slug}` | Author bio, verified badges, published articles, followers count |
| 1.17 | Public Member Profile | `GET /profile/{username}` | Non-author member profile, reading history public stats, bio |
| 1.18 | Legal & Compliance Hub | `GET /legal`, `/privacy`, `/terms` | Editorial standards, privacy policy, terms of service, security policy |

---

## 🔍 Detailed Functional & Visual Test Cases

### Screen 1.1: Homepage Discovery Engine (`/`)
1. **Hero Headline & Actions**:
   - Verify glowing hero typography *"The Frontier of Modern Engineering & AI"*.
   - Click *"Explore Tools"* &rarr; Navigates to `/tools`.
   - Click *"Start Reading"* &rarr; Smooth scrolls to the latest articles grid.
2. **Topic Pills Carousel**:
   - Click `#AI-Agents` or `#Distributed-Systems` tag pill.
   - Verify articles dynamically filter by category.
3. **Theme Adaptability**:
   - Click theme toggle icon in navbar.
   - In Light Mode: Hero background displays subtle grid pattern, text is `#0f172a`, cards have crisp borders.
   - In Dark Mode: Deep cosmic navy `#070a13`, glowing gradients, glassmorphism cards.

### Screen 1.9: Developer Hackathons & Challenges (`/campaigns` & `/campaigns/{slug}`)
1. **Directory Hub (`/campaigns`)**:
   - Prize pool badges, countdown timer, difficulty badges, and participant counts.
2. **Challenge Deep-Dive & Submission Hub (`/campaigns/{slug}`)**:
   - **Tab 1: Overview & Guidelines**: Rich mission brief, prize distribution, evaluation criteria, and timeline.
   - **Tab 2: Project Submission & Personal Referral Hub**:
     - Pre-submission: Displays comprehensive submission form matching admin-configured requirements (Project Title, Description, GitHub Repo, Live Demo URL, Video Walkthrough, Linked TechScout Publication dropdown, Tech Stack Tags).
     - Post-submission: Displays participant project card with review status badge (`Approved`, `Under Review`, `Winner`), awarded prize badge, judge feedback notes, and unique referral engine.
     - **Viral Referral System**: Personalized link (`?ref={code}`) with one-click copy button and live telemetry stats: Referral Clicks, Referred Signups, and Community Showcase Votes.
   - **Tab 3: Community Showcase & Public Voting**:
     - Interactive grid of approved participant projects.
     - Real-time AJAX upvoting with instant vote counter updates and duplicate vote prevention.

### Screen 1.4: Single Technical Publication Reader (`/article/{slug}`)
1. **Reading Experience & 10-Second Dwell Monetization**:
   - Verified read beacon is dispatched after >=10 seconds of active reading engagement (`POST /api/v1/content/read-ping`).
   - Anti-fraud duplicate prevention: Maximum 1 verified read per user/IP per publication in a 24-hour rolling window.
   - Only 10s verified reads count toward creator monetization CPM pool payouts and analytics counters.
2. **Interactive Reactions**:
   - Click Reaction button (🔥 Fire, 💡 Insight, ❤️ Love).
   - If logged in: Count increments live without full page reload.
   - If guest: Prompts user to sign in or shows quick sign-in toast.
3. **Discussion Stream (Comments)**:
   - Scroll to the bottom comment box.
   - Verified members can post threaded replies.
   - User avatar displays photo or initial fallback with safe array access.

### Screen 1.5: AI Tools Radar Directory (`/tools`)
1. **Pricing Filter**:
   - Click filter pill *"Open Source"*.
   - Confirm only open-source tools with GitHub repositories are visible.
2. **Faceted Search**:
   - Type `vector database` in the tools search input.
   - Verify instantaneous filtering.

### Screen 1.7: Prompt Blueprints Library (`/prompts`)
1. **One-Click Copy**:
   - Click *"Copy Blueprint"* button on any prompt card.
   - Verify clipboard copy action and visual feedback checkmark *"Copied! ✓"*.

### Screen 1.15: Platform Registration Hub (`/auth/register`)
1. **Name & Username Character Limits**:
   - Name input enforces `minlength="2"` and `maxlength="60"`. Rejects inputs with < 2 non-whitespace characters.
   - Username input enforces `minlength="3"`, `maxlength="30"`, and regex pattern `^[a-zA-Z0-9_]{3,30}$`. Rejects spaces, dashes, special symbols.
2. **Live Debounced AJAX Availability Verification**:
   - Type a username (e.g. `superadmin`):
     - After 350ms debounce, status pill shows red badge: `✕ Taken — Username is already taken`.
     - Registration submit button is disabled.
   - Type a unique username (e.g. `unique_developer_2026`):
     - Status pill turns green: `✓ Available — Username is available!`.
     - Registration submit button becomes active.
   - Type an email:
     - Invalid format &rarr; displays error message.
     - Registered email &rarr; displays red taken indicator.
     - New email &rarr; displays green checkmark badge.

### Screen 1.16 & 1.17: Public Profiles (`/author/{slug}` & `/profile/{username}`)
1. **Author Dossier Display**:
   - For authors: Displays authored publications, clean numerical `follower_count`, verification badge (`verified_builder`, `staff`, etc.), and custom headline.
   - Pure numerical followers: Accurately reflects creator subscriber counter without binding or displaying fake members.
2. **Standard Member Profile**:
   - Displays community reputation score, verified reading badges, and personal bio without throwing undefined warnings.

