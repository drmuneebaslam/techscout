# TechScout Data Models & Business Engines

This document provides a technical specification for all 18 Data Models and Business Logic Engines residing in `app/Models/`.

---

## 1. `User`
- **Location**: [`app/Models/User.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/User.php)
- **Database Table**: `users`
- **Primary Responsibilities**: User account entity management, authentication queries, password hashing, profile updates, role assignments, and ban states.
- **Key Methods**:
  - `findByEmail(string $email)`: Retrieves user row by unique email address.
  - `findByUsername(string $username)`: Retrieves user by unique username handle.
  - `create(array $data)`: Inserts new member with hashed password, status `unverified` or `active`.
  - `updateProfile(int $userId, array $data)`: Modifies bio, avatar URL, social links, location.
  - `setRole(int $userId, string $role)`: Updates account role (`super_admin`, `admin`, `editor`, `author`, `member`).
  - `setBanState(int $userId, string $status, ?string $reason)`: Bans or suspends user account.

---

## 2. `Author`
- **Location**: [`app/Models/Author.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Author.php)
- **Database Table**: `authors`
- **Primary Responsibilities**: Creator profiles, reputation scores, badge levels, promotional credit balances, and publishing permissions.
- **Key Methods**:
  - `getByUserId(int $userId)`: Fetches author profile tied to user ID.
  - `getBySlug(string $slug)`: Fetches author by URL slug.
  - `addReputation(int $authorId, int $points)`: Increments author's reputation score and updates reputation tier title.
  - `updatePromoCredits(int $authorId, float $amount, string $reason)`: Adjusts author's promotional credit balance for campaign usage.

---

## 3. `AuthorTierEngine`
- **Location**: [`app/Models/AuthorTierEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuthorTierEngine.php)
- **Database Tables**: `authors`, `author_tier_history`
- **Primary Responsibilities**: Computes author progression across a 20-sublevel hierarchy spanning 5 Leagues (Bronze I-IV, Silver I-IV, Gold I-IV, Diamond I-IV, Partner Elite I-IV).
- **Key Features**:
  - Dynamically calculates CPM multipliers (ranging from 1.00x to 2.50x).
  - Evaluates threshold requirements based on total article views, published article counts, and reputation points.
  - Automatically records progression events in `author_tier_history` and triggers level-up in-app notifications.

---

## 4. `Content`
- **Location**: [`app/Models/Content.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Content.php)
- **Database Tables**: `articles`, `tools`, `prompts`, `gpts`, `campaigns`, `jobs`
- **Primary Responsibilities**: Unified data query handler across all site content types.
- **Key Methods**:
  - `getLatestArticles(int $limit = 10)`: Fetches recent published blog posts.
  - `getTrendingContent(string $type, int $limit = 6)`: Computes trending items based on recent view velocity and upvote counts.
  - `incrementViews(string $type, int $id)`: Safely increments view counts and updates author statistics.

---

## 5. `Comment`
- **Location**: [`app/Models/Comment.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Comment.php)
- **Database Table**: `comments`
- **Primary Responsibilities**: Multi-nested hierarchical comment threads on articles and directory items.
- **Key Methods**:
  - `getForContent(string $contentType, int $contentId)`: Fetches approved comment trees with author avatars and upvote counts.
  - `addComment(array $data)`: Inserts new comment; auto-approves or queues for moderation based on user trust score.

---

## 6. `Analytics`
- **Location**: [`app/Models/Analytics.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Analytics.php)
- **Database Tables**: `page_views`, `content_analytics`, `traffic_sources`
- **Primary Responsibilities**: Real-time traffic tracking, referrer logging, geography tracking, and reader retention metrics.
- **Key Methods**:
  - `recordPageView(string $url, ?int $userId, string $ip, ?string $referrer)`: Logs raw hit.
  - `getAuthorDashboardStats(int $authorId)`: Aggregates daily/weekly/monthly views, top-performing articles, and click-through rates.

---

## 7. `FinancialLedger`
- **Location**: [`app/Models/FinancialLedger.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/FinancialLedger.php)
- **Database Tables**: `financial_ledger`, `author_payouts`, `tipping_transactions`
- **Primary Responsibilities**: Double-entry financial credit system tracking platform balances, author earnings, tipping, campaign reward allocations, and payout processing.
- **Key Methods**:
  - `recordTransaction(int $userId, string $type, float $amount, string $description)`: Writes immutable financial audit row.
  - `getAuthorBalance(int $authorId)`: Computes real-time available and pending balance.

---

## 8. `CoAuthorEngine`
- **Location**: [`app/Models/CoAuthorEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CoAuthorEngine.php)
- **Database Table**: `article_coauthors`
- **Primary Responsibilities**: Multi-author collaboration engine allowing authors to invite co-authors to articles and split credit / earnings.

---

## 9. `CreatorReferral`
- **Location**: [`app/Models/CreatorReferral.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CreatorReferral.php)
- **Database Table**: `creator_referrals`
- **Primary Responsibilities**: Tracks member referral links (`/auth/register?ref={code}`), logs conversions, and disburses promotional credit rewards to referrers.

---

## 10. `CreatorTipping`
- **Location**: [`app/Models/CreatorTipping.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CreatorTipping.php)
- **Database Table**: `creator_tips`
- **Primary Responsibilities**: Allows community members to tip authors using platform credits or integrated payment gateways.

---

## 11. `PersonaFollowerEngine`
- **Location**: [`app/Models/PersonaFollowerEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/PersonaFollowerEngine.php)
- **Database Table**: `author_followers`
- **Primary Responsibilities**: Manages member-to-author follow relationships and delivers personalized activity feeds for followed authors.

---

## 12. `PlagiarismEngine`
- **Location**: [`app/Models/PlagiarismEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/PlagiarismEngine.php)
- **Database Table**: `plagiarism_checks`
- **Primary Responsibilities**: Performs n-gram text hashing and cosine similarity comparison against existing articles to detect copied content before publication.

---

## 13. `RBAC`
- **Location**: [`app/Models/RBAC.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/RBAC.php)
- **Database Tables**: `roles`, `permissions`, `role_permissions`
- **Primary Responsibilities**: Role-Based Access Control matrix. Checks whether a role (`super_admin`, `admin`, `editor`, `author`, `member`) possesses specific explicit capabilities (e.g. `manage_users`, `publish_articles`, `view_financials`).

---

## 14. `SecurityAnomalyEngine`
- **Location**: [`app/Models/SecurityAnomalyEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SecurityAnomalyEngine.php)
- **Database Tables**: `security_anomalies`, `suspicious_ip_logs`
- **Primary Responsibilities**: Analyzes request patterns for credential stuffing, automated scraping, SQL injection probing, and rate-limit violations. Flags high-risk IPs for firewall blocking.

---

## 15. `SessionManager`
- **Location**: [`app/Models/SessionManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SessionManager.php)
- **Database Table**: `user_sessions`
- **Primary Responsibilities**: Tracks active user devices, IP addresses, session tokens, and last active timestamps. Allows users and admins to remotely revoke active login sessions.

---

## 16. `SupportEngine`
- **Location**: [`app/Models/SupportEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SupportEngine.php)
- **Database Tables**: `support_tickets`, `support_messages`
- **Primary Responsibilities**: Ticketing lifecycle management (Open, Pending, Resolved, Closed) and support agent assignment.

---

## 17. `AuditLog`
- **Location**: [`app/Models/AuditLog.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuditLog.php)
- **Database Table**: `audit_logs`
- **Primary Responsibilities**: Security compliance audit logger. Captures IP address, user agent, actor ID, action category, target entity ID, and old vs new data diffs for administrative actions.

---

## 18. `DatabaseBackupManager`
- **Location**: [`app/Models/DatabaseBackupManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/DatabaseBackupManager.php)
- **Primary Responsibilities**: Executes MySQL database backups via native PDO schema dumps, compresses output SQL files into `assets/backups/`, and manages automated backup retention cycles.
