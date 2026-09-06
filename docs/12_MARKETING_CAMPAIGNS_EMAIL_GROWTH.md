# TechScout — Campaigns, Email & Organic Growth Engine

> **Tasks Covered**: 501–518 (Phase 17 — Campaigns), 519–535 (Phase 18 — Social / Organic Growth), 536–554 (Phase 19 — Email Systems), 871–890 (Phase 37 — Growth Engine)  
> **Key Capabilities**: Campaign Landing Builders, Automated Dynamic OG Images, UTM Conversion Funnels, Double Opt-In Email Engine, and Referral Loops.

---

## 1. Marketing Campaigns & Tech Challenges (Tasks 501–518)

TechScout supports structured marketing campaigns and hackathons under `/campaigns/{slug}`:

```
+------------------------------------------------------------------------------------+
| CAMPAIGN: "The Autonomous Agent Challenge 2026"                                    |
| Sponsored by: [OpenAI / Anthropic] | Prize Pool: $15,000 & 500k API Credits        |
+------------------------------------------------------------------------------------+
|                                                                                    |
| [Hero Banner with Countdown Timer]: [ 14 Days : 08 Hours : 22 Mins Remaining ]     |
|                                                                                    |
| Build and document a production multi-agent system using TechScout prompt models.  |
|                                                                                    |
| [ 🚀 Submit Your Project ]    [ 📜 View Challenge Rules ]    [ 🏆 Leaderboard ]    |
|                                                                                    |
| +------------------------------------+-------------------------------------------+ |
| | LIVE LEADERBOARD (Top Entries)     | RECENT SUBMISSIONS                        | |
| | 1. AgentSwarm PHP by @alexvance    | - "PostgreSQL Agent" by @sarah_dev        | |
| | 2. CodeReviewer GPT by @dev_mike   | - "Automated RAG Crawler" by @kenji       | |
| +------------------------------------+-------------------------------------------+ |
+------------------------------------------------------------------------------------+
```

### Tasks 511–515: UTM & Conversion Tracking
- Every campaign URL accepts standard UTM parameters (`utm_source`, `utm_medium`, `utm_campaign`, `utm_content`).
- Automatically stored in session cookies to attribute registrations, newsletter signups, and submissions back to specific sponsor campaigns.

---

## 2. Social Growth & Dynamic OG Image Automation (Tasks 519–535)

### Task 524 & 525: Automated OpenGraph Image Generation
- Server-side PHP script dynamically synthesizes branded 1200x630px social preview cards using GD / Imagick:
  - Gradient Obsidian Background (`#07090e` -> `#1e1b4b`).
  - Article Headline & Author Avatar.
  - Tech category badge & TechScout logo mark.

### Task 527: Shareable Quote & Code Snippet Cards
- Readers can highlight any sentence or code block in an article.
- A floating action pill appears: `[ 🐦 Tweet Quote ] [ 📋 Copy Link ] [ 🔖 Save ]`.
- Generates pre-populated intent URLs with author attribution and short links.

---

## 3. Email Engine & Weekly Digest Automation (Tasks 536–554)

```
                    READER ENTERS EMAIL AT /NEWSLETTER
                                   │
                                   ▼
                 [1. Double Opt-In Verification Email]
                 - Cryptographic 64-char confirmation token
                                   │
                                   ▼
                 [2. Subscriber Preferences Selection]
                 [x] Weekly Top Tech Reads
                 [x] New AI Tools & Breakthroughs
                 [ ] Prompt Engineering Weekly
                                   │
                                   ▼
                 [3. Automated Weekly Digest Cron Trigger]
                 - Runs every Tuesday 08:00 AM UTC
                 - Synthesizes top 5 articles + 3 trending AI tools
                 - Dispatched in batches of 100 via SMTP / Mailer API
```

### Responsive Email Template Structure (HTML):
- Dark-mode compatible fluid tables with inline CSS.
- One-click unsubscribe link in footer complying with CAN-SPAM and GDPR.
- Tracking pixel for open rates and custom redirect links for click analytics.

---

## 4. Viral Growth & Referral Loop (Tasks 871–890)

```
                       EXISTING AUTHOR / READER
                                   │
                                   ▼
                  [Unique Referral Link: /r/alexvance]
                                   │
                   ┌───────────────┴───────────────┐
                   ▼                               ▼
         [Reader Signs Up]               [Author Submits Article]
                   │                               │
                   ▼                               ▼
       +50 Reader Points               +200 Author Promo Credits
                                      + Verified Contributor Badge
```

### Growth Mechanics:
- **Author Referral Program (Task 872)**: Authors receive promo credits to run self-serve ad banners whenever their referred colleagues publish their first technical article.
- **Reader Milestone Rewards (Task 875)**: Readers unlock exclusive deep-dive PDF guides after reading 10 articles and referring 2 peers.
