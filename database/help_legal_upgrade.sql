-- ============================================================================
-- TechScout Database Upgrade Script: Help Articles & Legal Pages CMS
-- Description:
--   Creates dedicated tables for:
--     1. `help_articles` (Categories, detailed steps, FAQs, badges, search index)
--     2. `legal_pages` (Legal agreements, policies, effective dates, versions)
--   Seeds initial comprehensive content from existing knowledge base and legal docs.
-- Compatible with: MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. Table: help_articles
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `help_articles` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_slug` VARCHAR(60) NOT NULL DEFAULT 'getting_started',
  `category_name` VARCHAR(100) NOT NULL DEFAULT 'Getting Started & Account',
  `category_icon` VARCHAR(50) NOT NULL DEFAULT 'sparkles',
  `category_color` VARCHAR(20) NOT NULL DEFAULT '#6366f1',
  `category_desc` TEXT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `badge` VARCHAR(50) DEFAULT 'GUIDE',
  `summary` TEXT NULL,
  `key_takeaway` TEXT NULL,
  `content_html` LONGTEXT NULL,
  `steps_json` LONGTEXT NULL,
  `faqs_json` LONGTEXT NULL,
  `read_time` VARCHAR(30) DEFAULT '3 min read',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `helpful_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `unhelpful_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_help_slug` (`slug`),
  KEY `idx_help_cat` (`category_slug`),
  KEY `idx_help_featured` (`is_featured`),
  KEY `idx_help_published` (`is_published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Table: legal_pages
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `legal_pages` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(60) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` TEXT NULL,
  `badge` VARCHAR(60) DEFAULT 'LEGAL & COMPLIANCE',
  `badge_color` VARCHAR(30) DEFAULT 'indigo',
  `version` VARCHAR(20) NOT NULL DEFAULT '1.0',
  `effective_date` VARCHAR(50) NOT NULL DEFAULT 'September 2026',
  `content_html` LONGTEXT NOT NULL,
  `meta_description` TEXT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `updated_by` BIGINT(20) UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_legal_slug` (`slug`),
  KEY `idx_legal_published` (`is_published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Initial Seed Data for Legal Pages
-- ----------------------------------------------------------------------------
INSERT INTO `legal_pages` (`slug`, `title`, `subtitle`, `badge`, `badge_color`, `version`, `effective_date`, `content_html`, `meta_description`) VALUES
('privacy', 'Privacy Policy & Data Protection', 'TechScout is dedicated to safeguarding developer privacy, encrypted data governance, GDPR compliance, and transparent telemetry handling across our global engineering network.', 'LEGAL & COMPLIANCE', 'indigo', '2.1', 'September 2026', 
'<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;\">1. Information We Collect</h2>
<p>We collect information directly from you when you register an account, publish articles, submit AI tools or prompts, subscribe to our engineering newsletters, or interact with developer challenges:</p>
<ul style=\"padding-left: 1.5rem; margin-bottom: 1.5rem;\">
  <li><strong>Account Credentials:</strong> Name, username, email address, password hashes (Argon2id/Bcrypt), avatar, and biographical headlines.</li>
  <li><strong>Creator Data:</strong> Published articles, code repositories, prompt templates, GPT configurations, and verified social handles.</li>
  <li><strong>Telemetry &amp; Usage:</strong> IP address, browser user-agent, read time, bookmarked entries, and interaction telemetry.</li>
</ul>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">2. How We Use Collected Data</h2>
<p>Your data is processed strictly for legitimate platform purposes:</p>
<ul style=\"padding-left: 1.5rem; margin-bottom: 1.5rem;\">
  <li>Delivering the personalized \"For You\" technical reading radar and topic recommendations.</li>
  <li>Distributing weekly technical digests, security alerts, and creator payout notifications.</li>
  <li>Protecting platform infrastructure against DDoS attacks, automated scraping, and unauthorized intrusions via our Cyber Firewall.</li>
  <li>Calculating author view counts and disbursement of creator revenue rewards.</li>
</ul>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">3. Data Sharing &amp; Third-Party Services</h2>
<p>TechScout never sells personal developer information to third-party data brokers. We only transmit minimal required payload data to vetted infrastructure providers (e.g. Cloudflare for DDoS protection, Stripe/PayPal for creator payouts, and Resend/Amazon SES for transactional emails).</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">4. Your Rights (GDPR &amp; CCPA)</h2>
<p>Regardless of your geographic location, you retain full ownership of your data, including the right to request a complete machine-readable export (JSON) of your account data or invoke account erasure by contacting <a href=\"mailto:privacy@techscout.online\" style=\"color: var(--ts-primary);\">privacy@techscout.online</a>.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">5. Security &amp; Encryption Standards</h2>
<p>All traffic is strictly routed over TLS 1.3 encryption. Passwords and sensitive credentials are encrypted using modern key-derivation algorithms with automated rate-limiting.</p>', 
'TechScout Privacy Policy, GDPR compliance, developer data protection, and telemetric transparency guidelines.'),

('terms', 'Terms of Service & Usage Agreement', 'By accessing or participating in the TechScout developer network, AI tool registry, prompt ecosystem, or author studio, you agree to these Terms of Service.', 'LEGAL AGREEMENT', 'indigo', '2.0', 'September 2026',
'<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;\">1. User Accounts &amp; Conduct</h2>
<p>You are responsible for maintaining the confidentiality of your credentials and all actions taken under your account. You agree not to upload malicious exploits, deceptive software, unsolicited commercial spam, or execute unauthorized scanning against the TechScout infrastructure.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">2. Intellectual Property &amp; Content Ownership</h2>
<p>Authors retain 100% full intellectual property and copyright ownership of their original publications, code snippets, prompt templates, and AI agents. By submitting content to TechScout, you grant us a non-exclusive, worldwide, royalty-free license to distribute, index, and cache your publication across the network and syndication feeds.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">3. Creator Monetization &amp; Payout Terms</h2>
<p>Authors enrolled in the Creator Monetization Program receive programmatic revenue disbursements based on verified read time and milestone grants. TechScout reserves the right to withhold rewards if fraudulent view inflation or automated bot traffic is detected.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">4. Disclaimer of Warranties &amp; Limitation of Liability</h2>
<p>TechScout is provided on an \"as is\" and \"as available\" basis. Code snippets, prompts, architectural designs, and software tools published on TechScout are provided for informational and educational purposes without warranty of any kind.</p>',
'TechScout Terms of Service and developer platform usage agreement.'),

('cookies', 'Cookie & Tracking Technologies Policy', 'How TechScout utilizes essential cookies, local storage tokens, and telemetry signals to power session persistence, dark mode preferences, and DDoS prevention.', 'COMPLIANCE & PRIVACY', 'indigo', '1.8', 'September 2026',
'<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;\">1. What Are Cookies?</h2>
<p>Cookies and browser local storage items are small data fragments saved onto your device that allow web applications to remember stateful preferences across HTTP requests.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">2. Cookie Categories Used on TechScout</h2>
<ul style=\"padding-left: 1.5rem; margin-bottom: 1.5rem;\">
  <li><strong>Strictly Necessary:</strong> Secure authentication tokens (<code>PHPSESSID</code>, <code>ts_token</code>) and CSRF defense headers required to log into the author studio and comment.</li>
  <li><strong>Functional Preferences:</strong> Theme switcher preference (<code>ts_theme</code> &rarr; dark/light) and collapsible sidebar state.</li>
  <li><strong>Security &amp; Rate-Limiting:</strong> Anonymous tokens used by our Cyber Firewall to distinguish legitimate developers from malicious bot swarms.</li>
</ul>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">3. Controlling Your Cookies</h2>
<p>You can adjust your cookie preferences directly in your browser settings. Disabling functional cookies may disable persistent theme selection and automated session authentication.</p>',
'Cookie usage and persistent tracking tokens policy for TechScout.'),

('community-guidelines', 'Community Guidelines & Code of Conduct', 'TechScout is a collaborative, intellectually rigorous community for software engineers, AI researchers, architects, and technology creators.', 'DEVELOPER CODE OF CONDUCT', 'pink', '1.5', 'September 2026',
'<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;\">1. Respectful &amp; Constructive Collaboration</h2>
<p>We welcome spirited technical debates regarding architectural patterns, language paradigms, and AI models. However, personal harassment, condescending gatekeeping, discrimination, and hate speech are strictly prohibited and result in permanent suspension.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">2. Authenticity &amp; No Commercial Spam</h2>
<p>Do not use comment sections or prompt submissions solely to drop affiliate referral links, promote token launch scams, or spam external search engines. Commercial promotion must be conducted through verified sponsorship channels.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">3. Safe &amp; Responsible AI Prompting</h2>
<p>Prompts and Custom GPT agents submitted to the TechScout registry must adhere to safety benchmarks. Prompts intended to generate malware, automated exploits, non-consensual imagery, or bypass security barriers without authorization will be removed immediately.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">4. Reporting Violations</h2>
<p>Any member can flag inappropriate comments, spam tools, or malicious code using the in-app reporting dialog or by emailing <a href=\"mailto:moderation@techscout.online\" style=\"color: var(--ts-primary);\">moderation@techscout.online</a>.</p>',
'Official community guidelines and developer code of conduct on TechScout.'),

('copyright', 'DMCA & Copyright Policy', 'TechScout respects intellectual property rights and complies with the Digital Millennium Copyright Act (17 U.S.C. § 512).', 'INTELLECTUAL PROPERTY', 'cyan', '1.2', 'September 2026',
'<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;\">1. Filing a Notice of Infringement (Takedown Notice)</h2>
<p>If you believe that copyrighted material belonging to you or an entity you represent has been reproduced on TechScout without authorization, submit a written notification containing:</p>
<ul style=\"padding-left: 1.5rem; margin-bottom: 1.5rem;\">
  <li>Identification of the copyrighted work claimed to have been infringed.</li>
  <li>The exact URL of the material on TechScout that is to be removed or disabled.</li>
  <li>Your full contact information including physical address, telephone number, and official email.</li>
  <li>A statement that you have a good faith belief that the disputed use is not authorized by the copyright owner, its agent, or the law.</li>
  <li>A physical or electronic signature of the authorized agent.</li>
</ul>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">2. Designated Copyright Agent Contact</h2>
<p>Send all formal DMCA notices to: <strong>dmca@techscout.online</strong> (Subject: DMCA Takedown Notice &bull; Attention: Legal &amp; Copyright Department).</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">3. Counter-Notification Procedure</h2>
<p>If you believe your content was removed erroneously or through misidentification, you may submit a formal counter-notification under 17 U.S.C. § 512(g)(3).</p>',
'DMCA copyright agent and intellectual property takedown notice procedure.'),

('accessibility', 'Accessibility Statement (WCAG 2.1 AA)', 'TechScout is dedicated to digital inclusion and accessible developer software for engineers of all abilities.', 'ACCESSIBILITY STANDARDS', 'emerald', '1.1', 'September 2026',
'<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;\">1. Our Commitment to Accessibility</h2>
<p>TechScout is designed to meet or exceed Web Content Accessibility Guidelines (WCAG) 2.1 Level AA conformance. We believe engineering documentation and developer tools must be accessible to everyone.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">2. Implemented Accessibility Features</h2>
<ul style=\"padding-left: 1.5rem; margin-bottom: 1.5rem;\">
  <li><strong>Full Keyboard Navigation:</strong> All navigation menus, search palettes (Ctrl+K), and prompt variable inputs are 100% accessible via keyboard tab sequences.</li>
  <li><strong>High Contrast Themes:</strong> Both dark mode and light mode palettes maintain a minimum 4.5:1 text-to-background contrast ratio.</li>
  <li><strong>Screen Reader Semantic Markup:</strong> Descriptive ARIA landmarks, roles, and button labels throughout code viewers and forms.</li>
  <li><strong>Reduced Motion Options:</strong> Interfaces honor OS-level prefers-reduced-motion media query settings.</li>
</ul>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">3. Feedback &amp; Assistance</h2>
<p>If you experience any accessibility hurdles, contact our accessibility coordinators at <a href=\"mailto:accessibility@techscout.online\" style=\"color: var(--ts-primary);\">accessibility@techscout.online</a>.</p>',
'TechScout WCAG 2.1 AA compliance and developer accessibility statement.'),

('acceptable-use', 'Acceptable Use Policy (AUP)', 'Standards for automated API consumption, network requests, bot scraping, and vulnerability research.', 'SECURITY & POLICY', 'amber', '1.3', 'September 2026',
'<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;\">1. Permitted &amp; Prohibited System Usage</h2>
<p>Users and automated systems must interact with TechScout in a responsible manner that does not degrade system availability or integrity for other developers.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">2. Prohibited Activities</h2>
<ul style=\"padding-left: 1.5rem; margin-bottom: 1.5rem;\">
  <li>Executing volumetric DDoS attacks, brute-force credential stuffing, or port scanning against TechScout servers.</li>
  <li>Aggressive scraping at rates exceeding documented rate limits without developer API authentication.</li>
  <li>Distributing obfuscated malicious payload code, keyloggers, or crypto-drainers via prompt blueprints or articles.</li>
  <li>Manipulating reading metrics or bookmark counts using automated proxies or bot farms.</li>
</ul>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">3. Enforcement &amp; Account Suspension</h2>
<p>Violations of this Acceptable Use Policy result in immediate IP blacklisting via our Cyber Firewall and permanent termination of publishing and payout privileges.</p>',
'Acceptable Use Policy for TechScout API consumers and community members.'),

('security', 'Security Policy & Responsible Disclosure', 'How TechScout defends infrastructure, handles cryptographic secrets, and coordinates with security researchers.', 'DEFENSE & PROTOCOLS', 'emerald', '2.0', 'September 2026',
'<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;\">1. Infrastructure Defense Architecture</h2>
<p>TechScout utilizes edge DDoS mitigation, automated rate-limiting, WAF inspection, TLS 1.3 encryption, and Argon2id password hashing to defend platform data.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">2. Coordinated Vulnerability Disclosure (Bug Bounty)</h2>
<p>We welcome white-hat security researchers. If you identify a security vulnerability in our systems, please practice responsible disclosure:</p>
<ul style=\"padding-left: 1.5rem; margin-bottom: 1.5rem;\">
  <li>Send a detailed proof-of-concept report to <strong>security@techscout.online</strong>.</li>
  <li>Allow 48 hours for acknowledgment before any public disclosure.</li>
  <li>Do not access or modify data belonging to other users without authorization.</li>
</ul>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">3. Two-Factor Authentication Requirement</h2>
<p>2FA TOTP authentication is strictly enforced for all administrative and high-balance creator accounts to prevent credential stuffing exploits.</p>',
'Security policy, defensive architecture, and coordinated vulnerability disclosure guidelines.'),

('disclosure', 'Affiliate & Sponsored Content Disclosure', 'Full transparency regarding monetization, sponsored AI tool radar placements, and referral links.', 'TRANSPARENCY', 'purple', '1.0', 'September 2026',
'<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;\">1. FTC Compliance &amp; Editorial Independence</h2>
<p>In accordance with FTC guidelines, TechScout discloses that certain links on this platform (including /go/{slug} outbound tool redirects) may be affiliate referral links from which TechScout earns a small commission at no additional cost to you.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">2. Sponsored Tools &amp; Benchmark Integrity</h2>
<p>Sponsored tool placements are clearly labeled with a \"SPONSORED\" badge. Sponsorship does NOT influence our benchmark ratings, latency scores, or editorial reviews.</p>

<h2 style=\"color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;\">3. Author Earnings</h2>
<p>Verified authors earn payouts directly from platform reading fund pools and hackathon bounties, keeping editorial deep dives honest and unbiased.</p>',
'Affiliate disclosure and sponsored content policy for TechScout.')
ON DUPLICATE KEY UPDATE 
  `title` = VALUES(`title`),
  `subtitle` = VALUES(`subtitle`),
  `badge` = VALUES(`badge`),
  `badge_color` = VALUES(`badge_color`),
  `version` = VALUES(`version`),
  `effective_date` = VALUES(`effective_date`),
  `content_html` = VALUES(`content_html`),
  `meta_description` = VALUES(`meta_description`);

-- ----------------------------------------------------------------------------
-- 4. Initial Seed Data for Help Articles (Getting Started, Security, Creator, AI, Monetization, Support)
-- ----------------------------------------------------------------------------
INSERT INTO `help_articles` (`category_slug`, `category_name`, `category_icon`, `category_color`, `category_desc`, `slug`, `title`, `badge`, `summary`, `key_takeaway`, `steps_json`, `faqs_json`, `read_time`, `is_featured`, `sort_order`) VALUES
('getting_started', 'Getting Started & Account', 'sparkles', '#6366f1', 'Platform overview, account preferences, roles, and profile settings.',
'what-is-techscout', 'What is TechScout and How Does the Platform Work?', 'CORE OVERVIEW',
'Understand TechScout’s architecture, content vetting, AI tool radar, and creator network.',
'TechScout is a high-performance single-domain engineering platform unifying technical publications, benchmarked AI tools, prompt blueprints, custom GPTs, and developer hackathons.',
'[{\"title\":\"Explore Without Limits\",\"desc\":\"Browse verified technical deep dives, compare frontier AI model benchmarks, and test interactive prompt blueprints without mandatory sign-up.\"},{\"title\":\"Create a Verified Developer Account\",\"desc\":\"Sign up instantly to personalize your radar feed, track favorite creators, save prompt parameter presets, and bookmark articles.\"},{\"title\":\"Apply for Creator & Author Privileges\",\"desc\":\"Publish your own technical research, index open-source or commercial AI tools, and earn creator rewards from verified reads and bounty competitions.\"}]',
'[{\"q\":\"Is TechScout free to use for developers?\",\"a\":\"Yes! Reading articles, searching benchmarked AI tools, and copying prompt blueprints is 100% free with no paywalls.\"},{\"q\":\"How are publications vetted?\",\"a\":\"Every submitted article undergoes automated code syntax validation, originality scoring, and staff peer review prior to syndication.\"}]',
'3 min read', 1, 1),

('getting_started', 'Getting Started & Account', 'sparkles', '#6366f1', 'Platform overview, account preferences, roles, and profile settings.',
'account-preferences-and-tracks', 'Account Setup & Experience Tracks', 'PREFERENCES',
'Configure your technical interests, developer seniority, dispatch alerts, and creator pitch.',
'Customize your profile preferences to tailor your home feed recommendations, newsletter digest topics, and community interaction permissions.',
'[{\"title\":\"Select Topic Tracks\",\"desc\":\"Choose your core engineering domains (AI & Machine Learning, Distributed Databases, AppSec, Frontend Frameworks, Cloud Infrastructure).\"},{\"title\":\"Set Seniority & Experience Level\",\"desc\":\"Indicate whether you are a Junior Developer, Senior Engineer, Principal Architect, or AI Researcher to receive calibrated technical depth.\"},{\"title\":\"Configure Dispatch Alerts\",\"desc\":\"Choose your preferred email intelligence digest frequency (Weekly Digest, Real-Time Breaking Announcements, or In-App Only).\"},{\"title\":\"Creator Track (Optional)\",\"desc\":\"Provide a short creator bio and portfolio links to request instant technical authoring studio access.\"}]',
'[{\"q\":\"Can I change my topic interests later?\",\"a\":\"Yes, navigate to Account Settings &rarr; Preferences at any time to update your followed tracks and tags.\"}]',
'2 min read', 0, 2),

('getting_started', 'Getting Started & Account', 'sparkles', '#6366f1', 'Platform overview, account preferences, roles, and profile settings.',
'navigation-and-shortcuts', 'Keyboard Shortcuts & Global Command Palette (Cmd+K)', 'PRODUCTIVITY',
'Accelerate your browsing workflow with keyboard navigation and instant multi-entity search.',
'Use Cmd+K / Ctrl+K from any page to search articles, AI tools, prompt blueprints, GPTs, and author profiles simultaneously.',
'[{\"title\":\"Universal Command Palette\",\"desc\":\"Press Cmd + K (macOS) or Ctrl + K (Windows/Linux) to summon the instant fuzzy search modal.\"},{\"title\":\"Quick Dark/Light Toggle\",\"desc\":\"Press Alt + T to toggle between sleek dark mode and high-contrast light mode.\"},{\"title\":\"Modal Dismissal\",\"desc\":\"Press Escape (Esc) to close any open modal, preview frame, or drawer instantly.\"}]',
'[{\"q\":\"Does search work offline?\",\"a\":\"Search queries query our high-throughput local indexes with sub-5ms response latencies.\"}]',
'2 min read', 0, 3),

('privacy_security', 'Privacy, Security & 2FA', 'shield-check', '#10b981', 'Two-factor authentication, security perimeters, password recovery, and GDPR data export.',
'how-to-enable-2fa', 'How to Enable Two-Factor Authentication (2FA TOTP)', 'SECURITY',
'Secure your account and creator earnings using Google Authenticator, Authy, or 1Password.',
'Two-Factor Authentication is required for all Verified Authors and Administrators before requesting payout withdrawals.',
'[{\"title\":\"Open Security Settings\",\"desc\":\"Go to your Profile Menu in the top right &rarr; Account Settings &rarr; Security tab.\"},{\"title\":\"Scan QR Code\",\"desc\":\"Click \\\"Enable TOTP Two-Factor Authentication\\\" and scan the displayed QR code with your mobile authenticator app.\"},{\"title\":\"Verify 6-Digit PIN\",\"desc\":\"Enter the current 6-digit TOTP code generated by your app to verify synchronization.\"},{\"title\":\"Save Backup Recovery Codes\",\"desc\":\"Safely copy and store your 8 emergency backup codes in a password manager in case you lose device access.\"}]',
'[{\"q\":\"What if I lose my authenticator app?\",\"a\":\"You can use one of your 8 Emergency Backup Codes to sign in, or contact support desk with your registered email verification PIN.\"}]',
'3 min read', 1, 4),

('privacy_security', 'Privacy, Security & 2FA', 'shield-check', '#10b981', 'Two-factor authentication, security perimeters, password recovery, and GDPR data export.',
'password-reset-and-recovery', 'Resetting Your Password with One-Time Security PIN', 'AUTH RECOVERY',
'What to do if you forgot your password or need an immediate emergency reset.',
'Password reset links and 6-digit authorization PINs are cryptographically hashed and expire automatically after 15 minutes.',
'[{\"title\":\"Request Reset PIN\",\"desc\":\"Visit /forgot-password and input your registered account email address.\"},{\"title\":\"Check Email Inbox\",\"desc\":\"Locate the TechScout Security Dispatch email containing your 6-digit PIN and secure token URL.\"},{\"title\":\"Create Strong Password\",\"desc\":\"Enter a new password with at least 8 characters, combining uppercase, lowercase, numbers, and symbols.\"}]',
'[{\"q\":\"Didn’t receive the reset email?\",\"a\":\"Check your Spam/Junk folder, verify your email address spelling, or ensure our sender domain notifications@techscout.online is whitelisted.\"}]',
'2 min read', 0, 5),

('privacy_security', 'Privacy, Security & 2FA', 'shield-check', '#10b981', 'Two-factor authentication, security perimeters, password recovery, and GDPR data export.',
'gdpr-data-export-and-erasure', 'GDPR Data Portability & Account Erasure', 'PRIVACY',
'Export your complete activity archive as JSON or permanently scrub your account from the database.',
'In accordance with GDPR & CCPA, you maintain complete sovereignty over your data and can export or delete it with a single click.',
'[{\"title\":\"Download Data Package\",\"desc\":\"Go to Account Settings &rarr; Privacy &rarr; click \\\"Export My Data Archive\\\" to download your profile, publications, bookmarks, and telemetry.\"},{\"title\":\"Permanent Account Erasure\",\"desc\":\"To permanently delete your account, click \\\"Permanently Delete Account\\\" in the Danger Zone. All PII records are completely purged.\"}]',
'[{\"q\":\"Can deleted accounts be restored?\",\"a\":\"No. For compliance reasons, once an account is erased, all data is permanently purged and cannot be recovered.\"}]',
'2 min read', 0, 6),

('creator_publishing', 'Creator Studio & Publishing', 'pen-tool', '#38bdf8', 'Authoring technical deep dives, Markdown syntax, KaTeX LaTeX math, code fences, and peer review.',
'markdown-and-syntax-guide', 'Markdown, Code Highlighting & LaTeX Math Reference', 'AUTHOR GUIDE',
'Format technical articles with GitHub-Flavored Markdown, multi-language code highlighting, and math formulas.',
'TechScout supports full GFM syntax, Prism.js syntax highlighting for 50+ languages, KaTeX LaTeX formulas, and callout alerts.',
'[{\"title\":\"Multi-Language Code Blocks\",\"desc\":\"Wrap your code in triple backticks with the language identifier (e.g. ```python, ```rust, ```sql, ```json).\"},{\"title\":\"Inline & Block LaTeX Math\",\"desc\":\"Use $O(N \\log N)$ for inline math formulas, or $$...$$ for centered block equations.\"},{\"title\":\"GitHub-Style Alert Callouts\",\"desc\":\"Use > [!NOTE], > [!TIP], > [!IMPORTANT], or > [!WARNING] to generate stylized highlight boxes.\"},{\"title\":\"Dynamic Table Formatting\",\"desc\":\"Create markdown tables using standard pipe syntax (| Header | Header |) with automatic responsive wrapping.\"}]',
'[{\"q\":\"Can I embed architecture diagrams?\",\"a\":\"Yes, you can upload PNG/SVG architecture diagrams directly in the Author Studio or embed external GitHub/GitLab image links.\"}]',
'4 min read', 1, 7),

('creator_publishing', 'Creator Studio & Publishing', 'pen-tool', '#38bdf8', 'Authoring technical deep dives, Markdown syntax, KaTeX LaTeX math, code fences, and peer review.',
'editorial-review-process', 'Editorial Review & Approval Lifecycle', 'EDITORIAL',
'Learn how articles are reviewed, scored for quality, and approved by staff editors.',
'Publications are reviewed within 12 to 24 hours. Authors receive real-time notifications for approval or revision requests.',
'[{\"title\":\"Automated Pre-Flight Check\",\"desc\":\"System validates readability score, estimated reading time, broken links, and code block formatting.\"},{\"title\":\"Staff Peer Review\",\"desc\":\"An editorial board member checks technical accuracy, original insights, and adherence to community guidelines.\"},{\"title\":\"Instant Syndication\",\"desc\":\"Once approved, your article is published to the live radar feed, sitemap XML, and included in email digests.\"}]',
'[{\"q\":\"What if revisions are requested?\",\"a\":\"You will receive specific reviewer notes in your notifications. Edit the draft in Author Studio and click \\\"Resubmit for Review\\\".\"}]',
'3 min read', 0, 8),

('ai_tools_and_prompts', 'AI Tools, Prompts & Custom GPTs', 'cpu', '#ec4899', 'AI tool directory submissions, benchmark comparisons, prompt variable injection, and GPT integrations.',
'submitting-ai-tools', 'How to Submit an AI Tool or Coding Assistant to the Radar', 'AI DIRECTORY',
'List your AI platform, autonomous agent, or developer tool in the benchmarked directory.',
'Verified AI tool listings receive prominent radar placement, benchmark badges, and direct /go/{slug} referral tracking.',
'[{\"title\":\"Launch Tool Submission Form\",\"desc\":\"Navigate to /author/editor and select \\\"AI Tool Listing\\\" as the publication type.\"},{\"title\":\"Provide Model Coordinates\",\"desc\":\"Specify tool pricing (Open Source, Freemium, Paid), base model dependencies (Claude, GPT-4o, DeepSeek), and features.\"},{\"title\":\"Add Verification Link & Benchmarks\",\"desc\":\"Include official documentation links, latency benchmarks, and pricing tiers for editorial verification.\"}]',
'[{\"q\":\"How long does tool indexing take?\",\"a\":\"Verified submissions are indexed on the live /tools radar within 24 hours of editorial review.\"}]',
'3 min read', 1, 9),

('ai_tools_and_prompts', 'AI Tools, Prompts & Custom GPTs', 'cpu', '#ec4899', 'AI tool directory submissions, benchmark comparisons, prompt variable injection, and GPT integrations.',
'using-prompt-variable-injectors', 'Using the Interactive Prompt Variable Injector & Copy Engine', 'PROMPT STUDIO',
'Fill dynamic parameters in prompt blueprints to generate production-ready prompts instantly.',
'Prompt blueprints use dynamic variables like {{code_context}} and {{target_stack}} for fast, error-free LLM prompting.',
'[{\"title\":\"Browse Prompt Blueprints\",\"desc\":\"Explore tested prompt blueprints in the /prompts catalog filtered by category or model.\"},{\"title\":\"Fill Variable Inputs\",\"desc\":\"In the right-hand Variable Injector panel, fill in your project-specific parameters.\"},{\"title\":\"One-Click Clipboard Copy\",\"desc\":\"Click \\\"Copy Injected Prompt ⚡\\\" to copy the fully interpolated prompt directly to your clipboard.\"}]',
'[{\"q\":\"Can I submit my own prompt blueprints?\",\"a\":\"Yes! Verified Authors can submit parameterized prompt templates via the Author Studio.\"}]',
'2 min read', 0, 10),

('monetization_payouts', 'Monetization & Creator Fund', 'wallet', '#f59e0b', 'Creator reward rates, reading milestone payouts, hackathon prize pools, and cash withdrawals.',
'creator-reward-rates-and-payouts', 'Creator Reward Rates, Engagement Milestones & Payouts', 'EARNINGS',
'Understand how creator earnings are calculated from verified reading time and developer bounties.',
'Authors earn revenue from verified member read minutes, article bookmarks, and developer challenge prize pools with bi-monthly cash withdrawals.',
'[{\"title\":\"Verified Reading Minutes\",\"desc\":\"Earn revenue for every 1,000 verified reading minutes accumulated on your technical articles.\"},{\"title\":\"Developer Challenge Bounties\",\"desc\":\"Participate in sponsored hackathons and bounties on /campaigns to win prize pools from $250 to $5,000.\"},{\"title\":\"Bi-Monthly Withdrawals\",\"desc\":\"Withdraw earnings on the 1st and 15th of each month once your balance reaches the $50.00 USD minimum threshold.\"},{\"title\":\"Supported Payout Methods\",\"desc\":\"Receive payouts via PayPal, Direct Bank Wire (ACH/SWIFT), or USDC Crypto wallet transfer.\"}]',
'[{\"q\":\"Is 2FA required for payouts?\",\"a\":\"Yes. To protect your earnings from unauthorized access, Two-Factor Authentication must be enabled on your account before requesting withdrawals.\"}]',
'4 min read', 1, 11),

('support_troubleshooting', 'Support Desk & Troubleshooting', 'life-buoy', '#06b6d4', 'Submitting support tickets, tracking ticket status, reporting bugs, and editorial contact.',
'submitting-support-tickets', 'How to Open and Track a Support Ticket', 'SUPPORT DESK',
'Get direct 1-on-1 assistance from our engineering and community operations staff.',
'Support tickets receive an official response within 4 to 12 hours with complete email dispatch updates.',
'[{\"title\":\"Visit Support Portal\",\"desc\":\"Navigate to /support or click Support Desk in the platform footer.\"},{\"title\":\"Select Ticket Department\",\"desc\":\"Choose your inquiry department (Technical Support, Creator Monetization, Account Security, Editorial Inquiries).\"},{\"title\":\"Provide Details & Attachments\",\"desc\":\"Enter a descriptive subject, detailed explanation, and attach relevant screenshots or error logs.\"},{\"title\":\"Track Ticket Progress\",\"desc\":\"View live staff responses and ticket status updates at /support/ticket/{id}.\"}]',
'[{\"q\":\"Can non-registered users submit tickets?\",\"a\":\"Yes! Guests can submit tickets with their email address and will receive updates via email dispatch.\"}]',
'2 min read', 1, 12)
ON DUPLICATE KEY UPDATE 
  `title` = VALUES(`title`),
  `summary` = VALUES(`summary`),
  `key_takeaway` = VALUES(`key_takeaway`),
  `steps_json` = VALUES(`steps_json`),
  `faqs_json` = VALUES(`faqs_json`),
  `read_time` = VALUES(`read_time`),
  `category_name` = VALUES(`category_name`),
  `category_icon` = VALUES(`category_icon`),
  `category_color` = VALUES(`category_color`),
  `category_desc` = VALUES(`category_desc`);
