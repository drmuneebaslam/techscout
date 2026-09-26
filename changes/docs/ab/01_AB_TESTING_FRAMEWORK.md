# TechScout A/B Experimentation & Statistical Evaluation Framework

## 1. Executive Overview
The TechScout A/B Testing Engine provides zero-latency deterministic variant assignment, conversion event tracking, and Bayesian statistical significance calculation across:
1. **Hero Call-to-Actions & Registration Hooks**
2. **Editorial Card Layouts (Grid vs. Dense Cyber-HUD Stream)**
3. **Creator Monetization Callouts & Tip Drawer Modals**
4. **Search Experience (Algorithmic Relevance vs. Temporal Freshness)**

---

## 2. Deterministic Variant Partitioning (`AbTestingEngine.php`)
Variants are allocated using MurmurHash3 / SHA-256 modulus hashing against the user's permanent UUID (or persistent guest cookie `ts_ab_uid`):
```php
$hashInt = hexdec(substr(md5($experimentKey . ':' . $distinctId), 0, 8));
$variantIndex = $hashInt % count($variants);
$assignedVariant = $variants[$variantIndex];
```
This guarantees:
- **Zero Flash of Variant Shift:** The user receives the exact same variant across all sessions and device refreshes without database lookups.
- **Uniform Distribution:** Even split (e.g. 50% Control `A`, 50% Treatment `B`) across high traffic volumes.

---

## 3. Active Experimentation Test Suites

### EXP-01: Creator Tipping Modal Layout (Minimalist vs. Social Ledger)
- **Hypothesis:** Displaying recent community tipping activity inside the tip modal will increase tip conversion rates by 25%.
- **Variants:**
  - `control_a`: Standard tip amount buttons ($5, $10, $25, Custom).
  - `variant_b`: Tip buttons with live community activity stream ("John tipped $10 3m ago").
- **Primary Metric:** `tip_conversion_rate` (Completed tips / Modal impressions).
- **Guardrail Metric:** Modal bounce rate (Users closing without interaction).

### EXP-02: Search HUD Auto-Complete Density
- **Hypothesis:** Showing instant AI model tags and prompt aspect ratio badges directly in the dropdown auto-complete increases content click-through rate.
- **Variants:**
  - `control_a`: Text-only article and tool titles.
  - `variant_b`: Rich preview cards with thumbnail, tags, and category chips.
- **Primary Metric:** `search_to_detail_ctr`.
- **Sample Size Required:** 5,000 unique search sessions per variant for 95% statistical confidence ($p < 0.05$).
