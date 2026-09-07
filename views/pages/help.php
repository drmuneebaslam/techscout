<?php 
  require __DIR__ . '/../layouts/header.php'; 

  // Master Knowledge Base Dictionary (Structured like YouTube & TikTok Help Centers)
  $helpCategories = [
    'getting_started' => [
      'id' => 'getting_started',
      'title' => 'Getting Started & Account',
      'icon' => 'sparkles',
      'color' => '#6366f1',
      'desc' => 'Platform overview, account preferences, roles, and profile settings.',
      'articles' => [
        [
          'slug' => 'what-is-techscout',
          'title' => 'What is TechScout and How Does the Platform Work?',
          'badge' => 'CORE OVERVIEW',
          'summary' => 'Understand TechScout’s architecture, content vetting, AI tool radar, and creator network.',
          'read_time' => '3 min read',
          'key_takeaway' => 'TechScout is a high-performance single-domain engineering platform unifying technical publications, benchmarked AI tools, prompt blueprints, custom GPTs, and developer hackathons.',
          'steps' => [
            ['title' => 'Explore Without Limits', 'desc' => 'Browse verified technical deep dives, compare frontier AI model benchmarks, and test interactive prompt blueprints without mandatory sign-up.'],
            ['title' => 'Create a Verified Developer Account', 'desc' => 'Sign up instantly to personalize your radar feed, track favorite creators, save prompt parameter presets, and bookmark articles.'],
            ['title' => 'Apply for Creator & Author Privileges', 'desc' => 'Publish your own technical research, index open-source or commercial AI tools, and earn creator rewards from verified reads and bounty competitions.']
          ],
          'faqs' => [
            ['q' => 'Is TechScout free to use for developers?', 'a' => 'Yes! Reading articles, searching benchmarked AI tools, and copying prompt blueprints is 100% free with no paywalls.'],
            ['q' => 'How are publications vetted?', 'a' => 'Every submitted article undergoes automated code syntax validation, originality scoring, and staff peer review prior to syndication.']
          ]
        ],
        [
          'slug' => 'account-preferences-and-tracks',
          'title' => 'Account Setup & Experience Tracks',
          'badge' => 'PREFERENCES',
          'summary' => 'Configure your technical interests, developer seniority, dispatch alerts, and creator pitch.',
          'read_time' => '2 min read',
          'key_takeaway' => 'Customize your profile preferences to tailor your home feed recommendations, newsletter digest topics, and community interaction permissions.',
          'steps' => [
            ['title' => 'Select Topic Tracks', 'desc' => 'Choose your core engineering domains (AI & Machine Learning, Distributed Databases, AppSec, Frontend Frameworks, Cloud Infrastructure).'],
            ['title' => 'Set Seniority & Experience Level', 'desc' => 'Indicate whether you are a Junior Developer, Senior Engineer, Principal Architect, or AI Researcher to receive calibrated technical depth.'],
            ['title' => 'Configure Dispatch Alerts', 'desc' => 'Choose your preferred email intelligence digest frequency (Weekly Digest, Real-Time Breaking Announcements, or In-App Only).'],
            ['title' => 'Creator Track (Optional)', 'desc' => 'Provide a short creator bio and portfolio links to request instant technical authoring studio access.']
          ],
          'faqs' => [
            ['q' => 'Can I change my topic interests later?', 'a' => 'Yes, navigate to Account Settings &rarr; Preferences at any time to update your followed tracks and tags.']
          ]
        ],
        [
          'slug' => 'navigation-and-shortcuts',
          'title' => 'Keyboard Shortcuts & Global Command Palette (Cmd+K)',
          'badge' => 'PRODUCTIVITY',
          'summary' => 'Accelerate your browsing workflow with keyboard navigation and instant multi-entity search.',
          'read_time' => '2 min read',
          'key_takeaway' => 'Use Cmd+K / Ctrl+K from any page to search articles, AI tools, prompt blueprints, GPTs, and author profiles simultaneously.',
          'steps' => [
            ['title' => 'Universal Command Palette', 'desc' => 'Press Cmd + K (macOS) or Ctrl + K (Windows/Linux) to summon the instant fuzzy search modal.'],
            ['title' => 'Quick Dark/Light Toggle', 'desc' => 'Press Alt + T to toggle between sleek dark mode and high-contrast light mode.'],
            ['title' => 'Modal Dismissal', 'desc' => 'Press Escape (Esc) to close any open modal, preview frame, or drawer instantly.']
          ],
          'faqs' => [
            ['q' => 'Does search work offline?', 'a' => 'Search queries query our high-throughput local indexes with sub-5ms response latencies.']
          ]
        ]
      ]
    ],
    'privacy_security' => [
      'id' => 'privacy_security',
      'title' => 'Privacy, Security & 2FA',
      'icon' => 'shield-check',
      'color' => '#10b981',
      'desc' => 'Two-factor authentication, security perimeters, password recovery, and GDPR data export.',
      'articles' => [
        [
          'slug' => 'how-to-enable-2fa',
          'title' => 'How to Enable Two-Factor Authentication (2FA TOTP)',
          'badge' => 'SECURITY',
          'summary' => 'Secure your account and creator earnings using Google Authenticator, Authy, or 1Password.',
          'read_time' => '3 min read',
          'key_takeaway' => 'Two-Factor Authentication is required for all Verified Authors and Administrators before requesting payout withdrawals.',
          'steps' => [
            ['title' => 'Open Security Settings', 'desc' => 'Go to your Profile Menu in the top right &rarr; Account Settings &rarr; Security tab.'],
            ['title' => 'Scan QR Code', 'desc' => 'Click "Enable TOTP Two-Factor Authentication" and scan the displayed QR code with your mobile authenticator app.'],
            ['title' => 'Verify 6-Digit PIN', 'desc' => 'Enter the current 6-digit TOTP code generated by your app to verify synchronization.'],
            ['title' => 'Save Backup Recovery Codes', 'desc' => 'Safely copy and store your 8 emergency backup codes in a password manager in case you lose device access.']
          ],
          'faqs' => [
            ['q' => 'What if I lose my authenticator app?', 'a' => 'You can use one of your 8 Emergency Backup Codes to sign in, or contact support desk with your registered email verification PIN.']
          ]
        ],
        [
          'slug' => 'password-reset-and-recovery',
          'title' => 'Resetting Your Password with One-Time Security PIN',
          'badge' => 'AUTH RECOVERY',
          'summary' => 'What to do if you forgot your password or need an immediate emergency reset.',
          'read_time' => '2 min read',
          'key_takeaway' => 'Password reset links and 6-digit authorization PINs are cryptographically hashed and expire automatically after 15 minutes.',
          'steps' => [
            ['title' => 'Request Reset PIN', 'desc' => 'Visit /forgot-password and input your registered account email address.'],
            ['title' => 'Check Email Inbox', 'desc' => 'Locate the TechScout Security Dispatch email containing your 6-digit PIN and secure token URL.'],
            ['title' => 'Create Strong Password', 'desc' => 'Enter a new password with at least 8 characters, combining uppercase, lowercase, numbers, and symbols.']
          ],
          'faqs' => [
            ['q' => 'Didn’t receive the reset email?', 'a' => 'Check your Spam/Junk folder, verify your email address spelling, or ensure our sender domain notifications@techscout.online is whitelisted.']
          ]
        ],
        [
          'slug' => 'gdpr-data-export-and-erasure',
          'title' => 'GDPR Data Portability & Account Erasure',
          'badge' => 'PRIVACY',
          'summary' => 'Export your complete activity archive as JSON or permanently scrub your account from the database.',
          'read_time' => '2 min read',
          'key_takeaway' => 'In accordance with GDPR & CCPA, you maintain complete sovereignty over your data and can export or delete it with a single click.',
          'steps' => [
            ['title' => 'Download Data Package', 'desc' => 'Go to Account Settings &rarr; Privacy &rarr; click "Export My Data Archive" to download your profile, publications, bookmarks, and telemetry.'],
            ['title' => 'Permanent Account Erasure', 'desc' => 'To permanently delete your account, click "Permanently Delete Account" in the Danger Zone. All PII records are completely purged.']
          ],
          'faqs' => [
            ['q' => 'Can deleted accounts be restored?', 'a' => 'No. For compliance reasons, once an account is erased, all data is permanently purged and cannot be recovered.']
          ]
        ]
      ]
    ],
    'creator_publishing' => [
      'id' => 'creator_publishing',
      'title' => 'Creator Studio & Publishing',
      'icon' => 'pen-tool',
      'color' => '#38bdf8',
      'desc' => 'Authoring technical deep dives, Markdown syntax, KaTeX LaTeX math, code fences, and peer review.',
      'articles' => [
        [
          'slug' => 'markdown-and-syntax-guide',
          'title' => 'Markdown, Code Highlighting & LaTeX Math Reference',
          'badge' => 'AUTHOR GUIDE',
          'summary' => 'Format technical articles with GitHub-Flavored Markdown, multi-language code highlighting, and math formulas.',
          'read_time' => '4 min read',
          'key_takeaway' => 'TechScout supports full GFM syntax, Prism.js syntax highlighting for 50+ languages, KaTeX LaTeX formulas, and callout alerts.',
          'steps' => [
            ['title' => 'Multi-Language Code Blocks', 'desc' => 'Wrap your code in triple backticks with the language identifier (e.g. ```python, ```rust, ```sql, ```json).'],
            ['title' => 'Inline & Block LaTeX Math', 'desc' => 'Use $O(N \log N)$ for inline math formulas, or $$...$$ for centered block equations.'],
            ['title' => 'GitHub-Style Alert Callouts', 'desc' => 'Use > [!NOTE], > [!TIP], > [!IMPORTANT], or > [!WARNING] to generate stylized highlight boxes.'],
            ['title' => 'Dynamic Table Formatting', 'desc' => 'Create markdown tables using standard pipe syntax (| Header | Header |) with automatic responsive wrapping.']
          ],
          'faqs' => [
            ['q' => 'Can I embed architecture diagrams?', 'a' => 'Yes, you can upload PNG/SVG architecture diagrams directly in the Author Studio or embed external GitHub/GitLab image links.']
          ]
        ],
        [
          'slug' => 'editorial-review-process',
          'title' => 'Editorial Review & Approval Lifecycle',
          'badge' => 'EDITORIAL',
          'summary' => 'Learn how articles are reviewed, scored for quality, and approved by staff editors.',
          'read_time' => '3 min read',
          'key_takeaway' => 'Publications are reviewed within 12 to 24 hours. Authors receive real-time notifications for approval or revision requests.',
          'steps' => [
            ['title' => 'Automated Pre-Flight Check', 'desc' => 'System validates readability score, estimated reading time, broken links, and code block formatting.'],
            ['title' => 'Staff Peer Review', 'desc' => 'An editorial board member checks technical accuracy, original insights, and adherence to community guidelines.'],
            ['title' => 'Instant Syndication', 'desc' => 'Once approved, your article is published to the live radar feed, sitemap XML, and included in email digests.']
          ],
          'faqs' => [
            ['q' => 'What if revisions are requested?', 'a' => 'You will receive specific reviewer notes in your notifications. Edit the draft in Author Studio and click "Resubmit for Review".']
          ]
        ]
      ]
    ],
    'ai_tools_and_prompts' => [
      'id' => 'ai_tools_and_prompts',
      'title' => 'AI Tools, Prompts & Custom GPTs',
      'icon' => 'cpu',
      'color' => '#ec4899',
      'desc' => 'AI tool directory submissions, benchmark comparisons, prompt variable injection, and GPT integrations.',
      'articles' => [
        [
          'slug' => 'submitting-ai-tools',
          'title' => 'How to Submit an AI Tool or Coding Assistant to the Radar',
          'badge' => 'AI DIRECTORY',
          'summary' => 'List your AI platform, autonomous agent, or developer tool in the benchmarked directory.',
          'read_time' => '3 min read',
          'key_takeaway' => 'Verified AI tool listings receive prominent radar placement, benchmark badges, and direct /go/{slug} referral tracking.',
          'steps' => [
            ['title' => 'Launch Tool Submission Form', 'desc' => 'Navigate to /author/editor and select "AI Tool Listing" as the publication type.'],
            ['title' => 'Provide Model Coordinates', 'desc' => 'Specify tool pricing (Open Source, Freemium, Paid), base model dependencies (Claude, GPT-4o, DeepSeek), and features.'],
            ['title' => 'Add Verification Link & Benchmarks', 'desc' => 'Include official documentation links, latency benchmarks, and pricing tiers for editorial verification.']
          ],
          'faqs' => [
            ['q' => 'How long does tool indexing take?', 'a' => 'Verified submissions are indexed on the live /tools radar within 24 hours of editorial review.']
          ]
        ],
        [
          'slug' => 'using-prompt-variable-injectors',
          'title' => 'Using the Interactive Prompt Variable Injector & Copy Engine',
          'badge' => 'PROMPT STUDIO',
          'summary' => 'Fill dynamic parameters in prompt blueprints to generate production-ready prompts instantly.',
          'read_time' => '2 min read',
          'key_takeaway' => 'Prompt blueprints use dynamic variables like {{code_context}} and {{target_stack}} for fast, error-free LLM prompting.',
          'steps' => [
            ['title' => 'Browse Prompt Blueprints', 'desc' => 'Explore tested prompt blueprints in the /prompts catalog filtered by category or model.'],
            ['title' => 'Fill Variable Inputs', 'desc' => 'In the right-hand Variable Injector panel, fill in your project-specific parameters.'],
            ['title' => 'One-Click Clipboard Copy', 'desc' => 'Click "Copy Injected Prompt ⚡" to copy the fully interpolated prompt directly to your clipboard.']
          ],
          'faqs' => [
            ['q' => 'Can I submit my own prompt blueprints?', 'a' => 'Yes! Verified Authors can submit parameterized prompt templates via the Author Studio.']
          ]
        ]
      ]
    ],
    'monetization_payouts' => [
      'id' => 'monetization_payouts',
      'title' => 'Monetization & Creator Fund',
      'icon' => 'wallet',
      'color' => '#f59e0b',
      'desc' => 'Creator reward rates, reading milestone payouts, hackathon prize pools, and cash withdrawals.',
      'articles' => [
        [
          'slug' => 'creator-reward-rates-and-payouts',
          'title' => 'Creator Reward Rates, Engagement Milestones & Payouts',
          'badge' => 'EARNINGS',
          'summary' => 'Understand how creator earnings are calculated from verified reading time and developer bounties.',
          'read_time' => '4 min read',
          'key_takeaway' => 'Authors earn revenue from verified member read minutes, article bookmarks, and developer challenge prize pools with bi-monthly cash withdrawals.',
          'steps' => [
            ['title' => 'Verified Reading Minutes', 'desc' => 'Earn revenue for every 1,000 verified reading minutes accumulated on your technical articles.'],
            ['title' => 'Developer Challenge Bounties', 'desc' => 'Participate in sponsored hackathons and bounties on /campaigns to win prize pools from $250 to $5,000.'],
            ['title' => 'Bi-Monthly Withdrawals', 'desc' => 'Withdraw earnings on the 1st and 15th of each month once your balance reaches the $50.00 USD minimum threshold.'],
            ['title' => 'Supported Payout Methods', 'desc' => 'Receive payouts via PayPal, Direct Bank Wire (ACH/SWIFT), or USDC Crypto wallet transfer.']
          ],
          'faqs' => [
            ['q' => 'Is 2FA required for payouts?', 'a' => 'Yes. To protect your earnings from unauthorized access, Two-Factor Authentication must be enabled on your account before requesting withdrawals.']
          ]
        ]
      ]
    ],
    'support_troubleshooting' => [
      'id' => 'support_troubleshooting',
      'title' => 'Support Desk & Troubleshooting',
      'icon' => 'life-buoy',
      'color' => '#06b6d4',
      'desc' => 'Submitting support tickets, tracking ticket status, reporting bugs, and editorial contact.',
      'articles' => [
        [
          'slug' => 'submitting-support-tickets',
          'title' => 'How to Open and Track a Support Ticket',
          'badge' => 'SUPPORT DESK',
          'summary' => 'Get direct 1-on-1 assistance from our engineering and community operations staff.',
          'read_time' => '2 min read',
          'key_takeaway' => 'Support tickets receive an official response within 4 to 12 hours with complete email dispatch updates.',
          'steps' => [
            ['title' => 'Visit Support Portal', 'desc' => 'Navigate to /support or click Support Desk in the platform footer.'],
            ['title' => 'Select Ticket Department', 'desc' => 'Choose your inquiry department (Technical Support, Creator Monetization, Account Security, Editorial Inquiries).'],
            ['title' => 'Provide Details & Attachments', 'desc' => 'Enter a descriptive subject, detailed explanation, and attach relevant screenshots or error logs.'],
            ['title' => 'Track Ticket Progress', 'desc' => 'View live staff responses and ticket status updates at /support/ticket/{id}.']
          ],
          'faqs' => [
            ['q' => 'Can non-registered users submit tickets?', 'a' => 'Yes! Guests can submit tickets with their email address and will receive updates via email dispatch.']
          ]
        ]
      ]
    ]
  ];

  // Dynamically load from database if help_articles table exists and has rows
  try {
    $dbArticles = \App\Core\Database::query("SELECT * FROM help_articles WHERE is_published = 1 ORDER BY sort_order ASC, id ASC");
    if (!empty($dbArticles)) {
      $dbCategories = [];
      foreach ($dbArticles as $row) {
        $cSlug = $row['category_slug'] ?: 'general';
        if (!isset($dbCategories[$cSlug])) {
          $dbCategories[$cSlug] = [
            'id' => $cSlug,
            'title' => $row['category_name'] ?: 'General Knowledge',
            'icon' => $row['category_icon'] ?: 'help-circle',
            'color' => $row['category_color'] ?: '#6366f1',
            'desc' => $row['category_desc'] ?: '',
            'articles' => []
          ];
        }

        $steps = [];
        if (!empty($row['steps_json'])) {
          $decoded = json_decode($row['steps_json'], true);
          if (is_array($decoded)) $steps = $decoded;
        }

        $faqs = [];
        if (!empty($row['faqs_json'])) {
          $decoded = json_decode($row['faqs_json'], true);
          if (is_array($decoded)) $faqs = $decoded;
        }

        $dbCategories[$cSlug]['articles'][] = [
          'id' => (int)$row['id'],
          'slug' => $row['slug'],
          'title' => $row['title'],
          'badge' => $row['badge'] ?: 'GUIDE',
          'summary' => $row['summary'] ?: '',
          'read_time' => $row['read_time'] ?: '3 min read',
          'key_takeaway' => $row['key_takeaway'] ?: '',
          'content_html' => $row['content_html'] ?: '',
          'steps' => $steps,
          'faqs' => $faqs,
          'is_featured' => (int)($row['is_featured'] ?? 0)
        ];
      }
      if (!empty($dbCategories)) {
        $helpCategories = $dbCategories;
      }
    }
  } catch (\Throwable $e) {}

  // Flatten all articles for quick slug lookup
  $allArticles = [];
  foreach ($helpCategories as $catKey => $cat) {
    foreach ($cat['articles'] as $art) {
      $art['category_id'] = $catKey;
      $art['category_title'] = $cat['title'];
      $art['category_icon'] = $cat['icon'];
      $art['category_color'] = $cat['color'];
      $allArticles[$art['slug']] = $art;
    }
  }

  // Active article or active category from URL
  $selectedArticle = !empty($activeSlug) && isset($allArticles[$activeSlug]) ? $allArticles[$activeSlug] : null;
  $activeCatKey = $selectedArticle ? $selectedArticle['category_id'] : ($activeCat ?? 'all');
?>

<!-- YouTube / TikTok Style Help Center CSS Tokens (Light/Dark Mode Adaptive) -->
<style>
.yt-help-container {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 2.5rem;
  margin-top: 2rem;
  align-items: start;
}
@media (max-width: 900px) {
  .yt-help-container {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  .yt-help-sidebar {
    position: static !important;
    max-height: none !important;
    overflow-x: auto;
    display: flex;
    gap: 0.5rem;
    padding-bottom: 0.75rem;
    scrollbar-width: none;
  }
}
.yt-help-nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  color: var(--ts-text-secondary);
  font-size: 0.92rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.15s ease;
  cursor: pointer;
  border: 1px solid transparent;
  white-space: nowrap;
}
.yt-help-nav-item:hover {
  background: rgba(99,102,241,0.08);
  color: var(--ts-text-primary);
  border-color: rgba(99,102,241,0.2);
}
.yt-help-nav-item.active {
  background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(56,189,248,0.1));
  color: #38bdf8;
  border-color: rgba(56,189,248,0.3);
  font-weight: 700;
}
.yt-article-card {
  background: var(--ts-card-bg);
  border: 1px solid var(--ts-border-subtle);
  border-radius: 12px;
  padding: 1.35rem;
  transition: all 0.2s ease;
  cursor: pointer;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.yt-article-card:hover {
  border-color: rgba(56,189,248,0.4);
  background: var(--ts-card-bg-elevated, var(--ts-card-bg));
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
.yt-step-box {
  background: var(--ts-card-bg-elevated, var(--ts-card-bg));
  border: 1px solid var(--ts-border-subtle);
  border-radius: 10px;
  padding: 1.25rem 1.5rem;
  margin-bottom: 1rem;
  display: flex;
  gap: 1.25rem;
  align-items: flex-start;
}
.yt-step-num {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #38bdf8);
  color: #fff;
  font-weight: 800;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.yt-faq-accordion {
  background: var(--ts-card-bg-elevated, var(--ts-card-bg));
  border: 1px solid var(--ts-border-subtle);
  border-radius: 8px;
  padding: 0.85rem 1.15rem;
  margin-bottom: 0.75rem;
}
.yt-faq-accordion summary {
  cursor: pointer;
  font-weight: 700;
  color: var(--ts-text-primary);
  font-size: 0.92rem;
  user-select: none;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.yt-faq-accordion summary::-webkit-details-marker {
  display: none;
}
.yt-faq-accordion p {
  margin: 0.65rem 0 0 0;
  color: var(--ts-text-secondary);
  font-size: 0.88rem;
  line-height: 1.6;
  border-top: 1px solid var(--ts-border-subtle);
  padding-top: 0.65rem;
}
</style>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 6rem; max-width: 1200px;">

  <!-- YouTube / TikTok Style Search Banner -->
  <div class="ts-card" style="background: radial-gradient(circle at 50% 0%, rgba(99,102,241,0.18), transparent 70%), var(--ts-card-bg); border-color: rgba(99,102,241,0.3); padding: 3rem 2rem; text-align: center; border-radius: 16px; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-indigo" style="font-size: 0.72rem; letter-spacing: 0.5px;">TECHSCOUT HELP &amp; KNOWLEDGE HUB</span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); font-weight: 800; font-family: 'Outfit', sans-serif; margin: 0 0 0.5rem 0;">
      How can we help you today?
    </h1>
    <p style="color: var(--ts-text-secondary); font-size: 1.05rem; max-width: 650px; margin: 0 auto 1.75rem auto;">
      Search step-by-step developer guides, creator publishing guidelines, security protocols, and support tickets.
    </p>

    <!-- Prominent Instant Search Input -->
    <div style="position: relative; max-width: 680px; margin: 0 auto;">
      <i data-lucide="search" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); width: 22px; height: 22px; color: #38bdf8;"></i>
      <input type="text" id="ytHelpSearch" placeholder="Search guides, 2FA, payouts, bookmarks, markdown, prompt engineering..." class="admin-modal-input" style="padding: 1.15rem 1.25rem 1.15rem 3.5rem; border-radius: 999px; font-size: 1.05rem; background: var(--ts-bg); border: 1px solid rgba(56,189,248,0.35); color: var(--ts-text-primary); box-shadow: 0 8px 30px rgba(0,0,0,0.15);" oninput="handleInstantHelpSearch(this.value)">
    </div>

    <!-- Trending Quick-Search Badges -->
    <div style="display: flex; justify-content: center; gap: 0.5rem; flex-wrap: wrap; margin-top: 1.25rem; font-size: 0.8rem; color: var(--ts-text-tertiary);">
      <span>Popular topics:</span>
      <button type="button" onclick="selectQuickSearch('2FA')" style="background: rgba(148,163,184,0.1); border: 1px solid var(--ts-border-subtle); color: var(--ts-text-secondary); padding: 0.15rem 0.55rem; border-radius: 999px; font-size: 0.75rem; cursor: pointer;">#2FA Security</button>
      <button type="button" onclick="selectQuickSearch('payouts')" style="background: rgba(148,163,184,0.1); border: 1px solid var(--ts-border-subtle); color: var(--ts-text-secondary); padding: 0.15rem 0.55rem; border-radius: 999px; font-size: 0.75rem; cursor: pointer;">#Creator Payouts</button>
      <button type="button" onclick="selectQuickSearch('markdown')" style="background: rgba(148,163,184,0.1); border: 1px solid var(--ts-border-subtle); color: var(--ts-text-secondary); padding: 0.15rem 0.55rem; border-radius: 999px; font-size: 0.75rem; cursor: pointer;">#Markdown Formatting</button>
      <button type="button" onclick="selectQuickSearch('bookmarks')" style="background: rgba(148,163,184,0.1); border: 1px solid var(--ts-border-subtle); color: var(--ts-text-secondary); padding: 0.15rem 0.55rem; border-radius: 999px; font-size: 0.75rem; cursor: pointer;">#Saved Bookmarks</button>
      <button type="button" onclick="selectQuickSearch('tickets')" style="background: rgba(148,163,184,0.1); border: 1px solid var(--ts-border-subtle); color: var(--ts-text-secondary); padding: 0.15rem 0.55rem; border-radius: 999px; font-size: 0.75rem; cursor: pointer;">#Support Tickets</button>
    </div>
  </div>

  <!-- 2-Column YouTube / TikTok Help Layout -->
  <div class="yt-help-container">
    
    <!-- LEFT SIDEBAR: CATEGORY NAVIGATION -->
    <div class="yt-help-sidebar" style="position: sticky; top: 90px; background: var(--ts-card-bg); border: 1px solid var(--ts-border-subtle); border-radius: 14px; padding: 0.85rem; backdrop-filter: blur(10px);">
      <div style="font-size: 0.72rem; font-weight: 800; color: var(--ts-text-tertiary); text-transform: uppercase; letter-spacing: 0.8px; padding: 0.5rem 0.75rem 0.35rem 0.75rem;">
        Knowledge Topics
      </div>

      <div class="yt-help-nav-item <?= (!$selectedArticle && $activeCatKey === 'all') ? 'active' : '' ?>" onclick="switchHelpCategory('all')">
        <i data-lucide="layout-grid" style="width: 18px; height: 18px; color: #38bdf8;"></i>
        <span>All Knowledge Guides (<?= count($allArticles) ?>)</span>
      </div>

      <?php foreach ($helpCategories as $catKey => $cat): ?>
        <div class="yt-help-nav-item <?= (!$selectedArticle && $activeCatKey === $catKey) ? 'active' : '' ?>" id="nav-item-<?= $catKey ?>" onclick="switchHelpCategory('<?= $catKey ?>')">
          <i data-lucide="<?= htmlspecialchars($cat['icon']) ?>" style="width: 18px; height: 18px; color: <?= $cat['color'] ?>;"></i>
          <span><?= htmlspecialchars($cat['title']) ?></span>
        </div>
      <?php endforeach; ?>

      <div style="border-top: 1px solid var(--ts-border-subtle); margin: 0.75rem 0; padding-top: 0.75rem;">
        <a href="<?= url('/legal') ?>" class="yt-help-nav-item" style="color: var(--ts-text-secondary);">
          <i data-lucide="scale" style="width: 18px; height: 18px; color: #c084fc;"></i>
          <span>Legal &amp; Compliance Hub &rarr;</span>
        </a>
        <a href="<?= url('/support') ?>" class="yt-help-nav-item" style="color: #38bdf8;">
          <i data-lucide="life-buoy" style="width: 18px; height: 18px; color: #38bdf8;"></i>
          <span>Open Support Desk Ticket &rarr;</span>
        </a>
      </div>
    </div>

    <!-- RIGHT MAIN CONTENT: DYNAMIC CATEGORY HUB OR SINGLE ARTICLE VIEW -->
    <div id="ytMainContent" style="min-width: 0;">

      <!-- ===================================================================== -->
      <!-- VIEW A: SINGLE ARTICLE DETAIL VIEW (YOUTUBE / TIKTOK HELP STYLE) -->
      <!-- ===================================================================== -->
      <div id="ytArticleDetailView" style="<?= $selectedArticle ? 'display: block;' : 'display: none;' ?>">
        <?php if ($selectedArticle): ?>
          <!-- Breadcrumb Trail -->
          <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; color: var(--ts-text-tertiary); margin-bottom: 1rem; flex-wrap: wrap;">
            <a href="javascript:void(0)" onclick="switchHelpCategory('all')" style="color: var(--ts-text-secondary); text-decoration: none;">Help Center</a>
            <span>&rsaquo;</span>
            <a href="javascript:void(0)" onclick="switchHelpCategory('<?= $selectedArticle['category_id'] ?>')" style="color: #38bdf8; text-decoration: none;"><?= htmlspecialchars($selectedArticle['category_title']) ?></a>
            <span>&rsaquo;</span>
            <span style="color: var(--ts-text-primary); font-weight: 600;"><?= htmlspecialchars($selectedArticle['title']) ?></span>
          </div>

          <!-- Article Header Card -->
          <div class="ts-card" style="background: var(--ts-card-bg); border-color: rgba(56,189,248,0.3); padding: 2.25rem; border-radius: 14px; margin-bottom: 1.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
              <span class="ts-badge ts-badge-cyan" id="artBadge"><?= htmlspecialchars($selectedArticle['badge']) ?></span>
              <span style="color: var(--ts-text-tertiary); font-size: 0.78rem;">Verified by TechScout Operations &bull; <?= htmlspecialchars($selectedArticle['read_time']) ?></span>
            </div>
            <h1 id="artTitle" style="font-size: 2rem; color: var(--ts-text-primary); font-weight: 800; font-family: 'Outfit', sans-serif; margin: 0 0 1rem 0; line-height: 1.3;">
              <?= htmlspecialchars($selectedArticle['title']) ?>
            </h1>
            <p id="artSummary" style="color: var(--ts-text-secondary); font-size: 1.05rem; line-height: 1.6; margin: 0 0 1.25rem 0;">
              <?= htmlspecialchars($selectedArticle['summary']) ?>
            </p>

            <!-- Key Takeaway Callout Box -->
            <div id="artTakeawayBox" style="background: rgba(99,102,241,0.08); border-left: 4px solid #6366f1; border-radius: 0 8px 8px 0; padding: 1rem 1.25rem; color: var(--ts-text-primary); font-size: 0.92rem; line-height: 1.6;">
              <strong style="color: #38bdf8; display: block; margin-bottom: 0.25rem;">⚡ Key Takeaway:</strong>
              <span id="artTakeaway"><?= htmlspecialchars($selectedArticle['key_takeaway']) ?></span>
            </div>
          </div>

          <!-- Rich In-Depth Content (if provided) -->
          <?php if (!empty($selectedArticle['content_html'])): ?>
            <div id="artContentHtml" class="ts-card" style="background: var(--ts-card-bg); border-color: var(--ts-border-subtle); padding: 2rem; margin-bottom: 2rem; line-height: 1.75; font-size: 0.95rem;">
              <?= $selectedArticle['content_html'] ?>
            </div>
          <?php endif; ?>

          <!-- Step-by-Step Walkthrough Section -->
          <?php if (!empty($selectedArticle['steps'])): ?>
            <h2 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0 0 1rem 0; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 0.5rem;">
              <i data-lucide="list-ordered" style="color: #38bdf8;"></i> Step-by-Step Instructions
            </h2>

            <div id="artStepsContainer">
              <?php foreach ($selectedArticle['steps'] as $idx => $step): ?>
                <div class="yt-step-box">
                  <div class="yt-step-num"><?= $idx + 1 ?></div>
                  <div style="flex: 1;">
                    <h3 style="font-size: 1.05rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0; font-weight: 700;">
                      <?= htmlspecialchars($step['title']) ?>
                    </h3>
                    <div style="color: var(--ts-text-secondary); font-size: 0.9rem; line-height: 1.6;">
                      <?= htmlspecialchars($step['desc']) ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- In-Article Frequently Asked Questions (Accordions) -->
          <?php if (!empty($selectedArticle['faqs'])): ?>
            <h2 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 2rem 0 1rem 0; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 0.5rem;">
              <i data-lucide="help-circle" style="color: #a855f7;"></i> Common Questions About This Guide
            </h2>
            <div id="artFaqsContainer">
              <?php foreach ($selectedArticle['faqs'] as $faq): ?>
                <details class="yt-faq-accordion">
                  <summary>
                    <span><?= htmlspecialchars($faq['q']) ?></span>
                    <span style="color: var(--ts-text-tertiary); font-size: 0.8rem;">▾</span>
                  </summary>
                  <p><?= htmlspecialchars($faq['a']) ?></p>
                </details>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- YouTube / TikTok Style "Was this helpful?" Feedback Widget -->
          <div class="ts-card" style="background: var(--ts-card-bg); border: 1px solid var(--ts-border-subtle); border-radius: 12px; padding: 1.5rem 2rem; margin: 2.5rem 0; text-align: center;">
            <h4 style="font-size: 1.1rem; color: var(--ts-text-primary); margin: 0 0 0.5rem 0;">Was this information helpful?</h4>
            <div style="display: flex; justify-content: center; gap: 0.75rem; margin-top: 1rem;" id="feedbackButtonBox">
              <button type="button" onclick="submitArticleFeedback(true)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #34d399; border-color: rgba(52,211,153,0.3); padding: 0.45rem 1.25rem;">
                👍 Yes, this helped!
              </button>
              <button type="button" onclick="submitArticleFeedback(false)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f87171; border-color: rgba(239,68,68,0.3); padding: 0.45rem 1.25rem;">
                👎 No, need more details
              </button>
              <button type="button" onclick="copyCurrentArticleLink()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3); padding: 0.45rem 1rem;">
                <i data-lucide="link" style="width: 14px;"></i> Copy Link
              </button>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- ===================================================================== -->
      <!-- VIEW B: CATEGORIES & POPULAR TOPICS HUB VIEW -->
      <!-- ===================================================================== -->
      <div id="ytCategoryHubView" style="<?= !$selectedArticle ? 'display: block;' : 'display: none;' ?>">

        <!-- Top Featured / Popular Quick Questions (YouTube Help style) -->
        <div style="margin-bottom: 2.5rem;" id="featuredFaqSection">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <div>
              <h2 style="font-size: 1.45rem; color: var(--ts-text-primary); margin: 0; font-family: 'Outfit', sans-serif;">
                🔥 Top Frequently Asked Guides
              </h2>
              <p style="color: var(--ts-text-secondary); font-size: 0.88rem; margin: 0.2rem 0 0 0;">
                Most visited articles by creators, engineers, and platform administrators.
              </p>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 1rem;">
            <div class="yt-article-card" onclick="loadArticleDetail('how-to-enable-2fa')">
              <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                  <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">SECURITY &bull; 2FA</span>
                  <span style="color: #38bdf8; font-size: 0.75rem;">3 min &rarr;</span>
                </div>
                <h3 style="font-size: 1.05rem; color: var(--ts-text-primary); margin: 0 0 0.4rem 0; font-weight: 700; line-height: 1.4;">
                  Enabling Two-Factor Authentication (2FA TOTP)
                </h3>
                <p style="color: var(--ts-text-secondary); font-size: 0.82rem; margin: 0; line-height: 1.5;">
                  Pair your account with Google Authenticator, Authy, or Apple Passwords to secure creator payouts.
                </p>
              </div>
            </div>

            <div class="yt-article-card" onclick="loadArticleDetail('creator-reward-rates-and-payouts')">
              <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                  <span class="ts-badge ts-badge-amber" style="font-size: 0.68rem;">CREATOR FUND</span>
                  <span style="color: #38bdf8; font-size: 0.75rem;">4 min &rarr;</span>
                </div>
                <h3 style="font-size: 1.05rem; color: var(--ts-text-primary); margin: 0 0 0.4rem 0; font-weight: 700; line-height: 1.4;">
                  Creator Reward Rates, Milestones &amp; Payouts
                </h3>
                <p style="color: var(--ts-text-secondary); font-size: 0.82rem; margin: 0; line-height: 1.5;">
                  Learn how revenue is calculated from verified reading time and developer challenge prize pools.
                </p>
              </div>
            </div>

            <div class="yt-article-card" onclick="loadArticleDetail('markdown-and-syntax-guide')">
              <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem;">MARKDOWN &bull; MATH</span>
                  <span style="color: #38bdf8; font-size: 0.75rem;">4 min &rarr;</span>
                </div>
                <h3 style="font-size: 1.05rem; color: var(--ts-text-primary); margin: 0 0 0.4rem 0; font-weight: 700; line-height: 1.4;">
                  Markdown, Code Highlighting &amp; LaTeX Formulas
                </h3>
                <p style="color: var(--ts-text-secondary); font-size: 0.82rem; margin: 0; line-height: 1.5;">
                  Full technical writing reference with syntax blocks, Prism styling, and KaTeX math equations.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Categorized Knowledge Sections -->
        <div id="ytCategoriesContainer">
          <?php foreach ($helpCategories as $catKey => $cat): ?>
            <div class="yt-category-section" id="section-cat-<?= $catKey ?>" style="margin-bottom: 3rem;">
              <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.4rem;">
                <div style="width: 28px; height: 28px; border-radius: 7px; background: rgba(99,102,241,0.12); display: flex; align-items: center; justify-content: center; color: <?= $cat['color'] ?>;">
                  <i data-lucide="<?= htmlspecialchars($cat['icon']) ?>" style="width: 16px; height: 16px;"></i>
                </div>
                <h2 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0; font-family: 'Outfit', sans-serif;">
                  <?= htmlspecialchars($cat['title']) ?>
                </h2>
              </div>
              <p style="color: var(--ts-text-secondary); font-size: 0.88rem; margin: 0 0 1.25rem 0;">
                <?= htmlspecialchars($cat['desc']) ?>
              </p>

              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 1rem;">
                <?php foreach ($cat['articles'] as $art): ?>
                  <div class="yt-article-card" data-slug="<?= htmlspecialchars($art['slug']) ?>" data-search-text="<?= htmlspecialchars(strtolower($art['title'] . ' ' . $art['summary'] . ' ' . $cat['title'])) ?>" onclick="loadArticleDetail('<?= htmlspecialchars($art['slug']) ?>')">
                    <div>
                      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem;"><?= htmlspecialchars($art['badge']) ?></span>
                        <span style="color: #38bdf8; font-size: 0.72rem; font-weight: 600;"><?= htmlspecialchars($art['read_time']) ?> &rarr;</span>
                      </div>
                      <h3 style="font-size: 1.05rem; color: var(--ts-text-primary); margin: 0 0 0.4rem 0; font-weight: 700; line-height: 1.4;">
                        <?= htmlspecialchars($art['title']) ?>
                      </h3>
                      <p style="color: var(--ts-text-secondary); font-size: 0.84rem; line-height: 1.5; margin: 0;">
                        <?= htmlspecialchars($art['summary']) ?>
                      </p>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      </div>

      <!-- Dedicated Bottom Support Escalation Banner -->
      <div class="ts-card" style="background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(56,189,248,0.06)); border: 1px solid var(--ts-border-main); border-radius: 14px; padding: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem; margin-top: 3rem;">
        <div>
          <span class="ts-badge ts-badge-pink" style="margin-bottom: 0.4rem;">STILL NEED ASSISTANCE?</span>
          <h3 style="font-size: 1.3rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0; font-family: 'Outfit', sans-serif;">
            Talk directly to our developer operations team
          </h3>
          <p style="color: var(--ts-text-secondary); font-size: 0.88rem; margin: 0; max-width: 580px;">
            Submit a support ticket with your inquiry details. Our engineering staff triages and replies within 4 to 12 hours.
          </p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
          <a href="<?= url('/support') ?>" class="ts-btn ts-btn-primary" style="font-weight: 700;">
            <i data-lucide="life-buoy"></i> Open Support Desk Ticket &rarr;
          </a>
          <a href="<?= url('/contact') ?>" class="ts-btn ts-btn-outline">
            <i data-lucide="mail"></i> Contact Editorial Team
          </a>
        </div>
      </div>

    </div>
  </div>

</div>

<!-- Real-time YouTube / TikTok Style Client Router JS -->
<script>
// JSON Dictionary of all articles
const KB_ARTICLES = <?= json_encode($allArticles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const KB_CATEGORIES = <?= json_encode($helpCategories, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

let activeArticleSlug = <?= json_encode($selectedArticle ? $selectedArticle['slug'] : '') ?>;

function switchHelpCategory(catKey) {
  // Update sidebar active classes
  document.querySelectorAll('.yt-help-nav-item').forEach(b => b.classList.remove('active'));
  const targetNav = (catKey === 'all') 
    ? document.querySelector('.yt-help-sidebar .yt-help-nav-item:first-child')
    : document.getElementById(`nav-item-${catKey}`);
  if (targetNav) targetNav.classList.add('active');

  // Hide single article view, show category view
  document.getElementById('ytArticleDetailView').style.display = 'none';
  document.getElementById('ytCategoryHubView').style.display = 'block';

  // Toggle category sections
  const sections = document.querySelectorAll('.yt-category-section');
  const featFaq = document.getElementById('featuredFaqSection');

  if (catKey === 'all') {
    if (featFaq) featFaq.style.display = 'block';
    sections.forEach(s => s.style.display = 'block');
    history.pushState(null, '', '<?= url('/help') ?>');
  } else {
    if (featFaq) featFaq.style.display = 'none';
    sections.forEach(s => {
      s.style.display = (s.id === `section-cat-${catKey}`) ? 'block' : 'none';
    });
    history.pushState(null, '', '<?= url('/help?cat=') ?>' + catKey);
  }

  window.scrollTo({ top: 200, behavior: 'smooth' });
}

function loadArticleDetail(slug) {
  const art = KB_ARTICLES[slug];
  if (!art) return;

  activeArticleSlug = slug;

  // Build HTML dynamically for single article view
  let stepsHtml = '';
  if (art.steps && art.steps.length) {
    stepsHtml = art.steps.map((st, i) => `
      <div class="yt-step-box">
        <div class="yt-step-num">${i + 1}</div>
        <div style="flex: 1;">
          <h3 style="font-size: 1.05rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0; font-weight: 700;">${st.title}</h3>
          <div style="color: var(--ts-text-secondary); font-size: 0.9rem; line-height: 1.6;">${st.desc}</div>
        </div>
      </div>
    `).join('');
  }

  let faqsHtml = '';
  if (art.faqs && art.faqs.length) {
    faqsHtml = `
      <h2 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 2rem 0 1rem 0; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="help-circle" style="color: #a855f7;"></i> Common Questions About This Guide
      </h2>
      <div>
        ${art.faqs.map(f => `
          <details class="yt-faq-accordion">
            <summary>
              <span>${f.q}</span>
              <span style="color: var(--ts-text-tertiary); font-size: 0.8rem;">▾</span>
            </summary>
            <p>${f.a}</p>
          </details>
        `).join('')}
      </div>
    `;
  }

  const detailHtml = `
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; color: var(--ts-text-tertiary); margin-bottom: 1rem; flex-wrap: wrap;">
      <a href="javascript:void(0)" onclick="switchHelpCategory('all')" style="color: var(--ts-text-secondary); text-decoration: none;">Help Center</a>
      <span>&rsaquo;</span>
      <a href="javascript:void(0)" onclick="switchHelpCategory('${art.category_id}')" style="color: #38bdf8; text-decoration: none;">${art.category_title}</a>
      <span>&rsaquo;</span>
      <span style="color: var(--ts-text-primary); font-weight: 600;">${art.title}</span>
    </div>

    <div class="ts-card" style="background: var(--ts-card-bg); border-color: rgba(56,189,248,0.3); padding: 2.25rem; border-radius: 14px; margin-bottom: 1.75rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
        <span class="ts-badge ts-badge-cyan">${art.badge}</span>
        <span style="color: var(--ts-text-tertiary); font-size: 0.78rem;">Verified by TechScout Operations &bull; ${art.read_time}</span>
      </div>
      <h1 style="font-size: 2rem; color: var(--ts-text-primary); font-weight: 800; font-family: 'Outfit', sans-serif; margin: 0 0 1rem 0; line-height: 1.3;">
        ${art.title}
      </h1>
      <p style="color: var(--ts-text-secondary); font-size: 1.05rem; line-height: 1.6; margin: 0 0 1.25rem 0;">
        ${art.summary}
      </p>

      <div style="background: rgba(99,102,241,0.08); border-left: 4px solid #6366f1; border-radius: 0 8px 8px 0; padding: 1rem 1.25rem; color: var(--ts-text-primary); font-size: 0.92rem; line-height: 1.6;">
        <strong style="color: #38bdf8; display: block; margin-bottom: 0.25rem;">⚡ Key Takeaway:</strong>
        <span>${art.key_takeaway}</span>
      </div>
    </div>

    ${art.content_html ? `<div class="ts-card" style="background: var(--ts-card-bg); border-color: var(--ts-border-subtle); padding: 2rem; margin-bottom: 2rem; line-height: 1.75; font-size: 0.95rem;">${art.content_html}</div>` : ''}

    ${stepsHtml ? `
      <h2 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0 0 1rem 0; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="list-ordered" style="color: #38bdf8;"></i> Step-by-Step Instructions
      </h2>
      <div>${stepsHtml}</div>
    ` : ''}
    ${faqsHtml}

    <div class="ts-card" style="background: var(--ts-card-bg); border: 1px solid var(--ts-border-subtle); border-radius: 12px; padding: 1.5rem 2rem; margin: 2.5rem 0; text-align: center;">
      <h4 style="font-size: 1.1rem; color: var(--ts-text-primary); margin: 0 0 0.5rem 0;">Was this information helpful?</h4>
      <div style="display: flex; justify-content: center; gap: 0.75rem; margin-top: 1rem;" id="feedbackButtonBox">
        <button type="button" onclick="submitArticleFeedback(true)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #34d399; border-color: rgba(52,211,153,0.3); padding: 0.45rem 1.25rem;">
          👍 Yes, this helped!
        </button>
        <button type="button" onclick="submitArticleFeedback(false)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f87171; border-color: rgba(239,68,68,0.3); padding: 0.45rem 1.25rem;">
          👎 No, need more details
        </button>
        <button type="button" onclick="copyCurrentArticleLink()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3); padding: 0.45rem 1rem;">
          <i data-lucide="link" style="width: 14px;"></i> Copy Link
        </button>
      </div>
    </div>
  `;

  document.getElementById('ytArticleDetailView').innerHTML = detailHtml;
  document.getElementById('ytArticleDetailView').style.display = 'block';
  document.getElementById('ytCategoryHubView').style.display = 'none';

  // Update URL seamlessly
  history.pushState(null, '', '<?= url('/help/') ?>' + slug);

  if (typeof lucide !== 'undefined') lucide.createIcons();
  window.scrollTo({ top: 220, behavior: 'smooth' });
}

function handleInstantHelpSearch(val) {
  const q = val.toLowerCase().trim();
  const cards = document.querySelectorAll('.yt-article-card');
  const sections = document.querySelectorAll('.yt-category-section');
  const featFaq = document.getElementById('featuredFaqSection');

  if (!q) {
    if (featFaq) featFaq.style.display = 'block';
    sections.forEach(s => s.style.display = 'block');
    cards.forEach(c => c.style.display = 'flex');
    return;
  }

  // Switch to category hub view to display search results across all categories
  document.getElementById('ytArticleDetailView').style.display = 'none';
  document.getElementById('ytCategoryHubView').style.display = 'block';
  if (featFaq) featFaq.style.display = 'none';

  sections.forEach(s => {
    let hasMatch = false;
    const catCards = s.querySelectorAll('.yt-article-card');
    catCards.forEach(c => {
      const txt = c.getAttribute('data-search-text') || '';
      if (txt.includes(q)) {
        c.style.display = 'flex';
        hasMatch = true;
      } else {
        c.style.display = 'none';
      }
    });
    s.style.display = hasMatch ? 'block' : 'none';
  });
}

function selectQuickSearch(term) {
  const input = document.getElementById('ytHelpSearch');
  if (input) {
    input.value = term;
    handleInstantHelpSearch(term);
    input.focus();
  }
}

function copyCurrentArticleLink() {
  const url = window.location.href;
  if (navigator.clipboard) {
    navigator.clipboard.writeText(url).then(() => {
      if (typeof showToast === 'function') {
        showToast('Direct guide URL copied to clipboard! 📋', 'success');
      } else {
        alert('Direct guide URL copied to clipboard!');
      }
    });
  }
}

function submitArticleFeedback(isHelpful) {
  const box = document.getElementById('feedbackButtonBox');
  if (box) {
    box.innerHTML = isHelpful 
      ? '<div style="color: #34d399; font-weight: 700; padding: 0.5rem;"><i data-lucide="check-circle"></i> Thank you! Your feedback helps us improve TechScout guides. 🚀</div>'
      : '<div style="color: #f87171; font-weight: 700; padding: 0.5rem;">Thank you! We have logged this for our editorial team to expand. 🙏</div>';
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }
}

// Support browser back/forward buttons
window.addEventListener('popstate', () => {
  const path = window.location.pathname;
  const match = path.match(/\/help\/([a-zA-Z0-9_-]+)/);
  if (match && match[1]) {
    loadArticleDetail(match[1]);
  } else {
    switchHelpCategory('all');
  }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
