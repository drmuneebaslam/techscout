# TechScout System Architecture, Documentation & QA Portal

Welcome to the official interactive documentation and QA testing portal for **TechScout.n**.

---

## 🌐 Interactive Web Documentation & QA Portal
You can read this complete documentation system directly in your browser on XAMPP:
👉 **`http://localhost/techscout.n/documentation/`** (driven by [`documentation/index.php`](file:///c:/xampp/htdocs/techscout.n/documentation/index.php)).

Features of the Enhanced Web Reader:
- **Client-Side Markdown Rendering**: Live syntax highlighting with Tokyo Night theme.
- **Auto Table of Contents (TOC)**: Automatically extracts headings (`#`, `##`, `###`) and displays a smooth-scrolling TOC.
- **One-Click Code Copy**: Copy buttons on all code blocks with feedback toast.
- **Reading Time & Line Counter**: Displays total lines, word count, and estimated reading time.
- **Dark/Light Mode Switcher**: Persisted in `localStorage`.
- **Instant Search**: Search across 60+ detailed documentation markdown files in real time.

---

## 🧪 Enterprise QA Testing Master Guide
- 🧪 **[`qa_testing_master_guide.md`](file:///c:/xampp/htdocs/techscout.n/documentation/qa_testing_master_guide.md)** - Master Quality Assurance testing suite with step-by-step manual test cases, automated cURL commands, input/output assertions, and test credentials for every feature!

---

## 📁 `documentation/project/` Subfolder (60 Granular Specification Files)

Inside [`documentation/project/`](file:///c:/xampp/htdocs/techscout.n/documentation/project), you will find **60 individual, highly detailed documentation files**:

### 🏛️ Core Architecture & Framework (13 Files)
1. [`01_framework_overview.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/01_framework_overview.md) - Framework principles and MVC request pipeline.
2. [`02_front_controller_index.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/02_front_controller_index.md) - `index.php` Front Controller, QA auth helpers, dynamic base URL self-healing.
3. [`03_autoloader.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/03_autoloader.md) - `app/autoload.php` PSR-4 class namespace resolver.
4. [`04_routing_engine.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/04_routing_engine.md) - `Router.php`, `Request.php`, `Response.php` HTTP routing engine.
5. [`05_database_abstraction.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/05_database_abstraction.md) - `Database.php` PDO singleton connection, auto-reset, schema migrations.
6. [`06_authentication_guard.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/06_authentication_guard.md) - `Auth.php` RBAC authorization, staff auto-provisioning, admin impersonation.
7. [`07_session_and_csrf.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/07_session_and_csrf.md) - `Session.php` cookie security and CSRF token defense.
8. [`08_firewall_and_security.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/08_firewall_and_security.md) - `Firewall.php` IP rate limiting slider, threat detection, IP bans.
9. [`09_sanitization_and_xss.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/09_sanitization_and_xss.md) - `Sanitizer.php` & `ContentFilter.php` XSS escaping and profanity filtering.
10. [`10_error_logging.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/10_error_logging.md) - `ErrorLogger.php` exception logging and crash diagnostics.
11. [`11_mail_subsystem.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/11_mail_subsystem.md) - `Mailer.php` & `SmtpClient.php` multi-transport transactional mailer and Python proxy fallback.
12. [`12_notifications_and_webhooks.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/12_notifications_and_webhooks.md) - `NotificationHelper.php` & `WebhookEngine.php` in-app alerts & outbound webhooks.
13. [`13_helpers_and_utilities.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/13_helpers_and_utilities.md) - System helpers (`e()`, `url()`, `asset()`, `auth()`, `SeoEngine`, `DocxParser`, `SvgHelper`).

### 🕹️ HTTP Controllers (14 Files)
14. [`14_controller_admin.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/14_controller_admin.md) - `AdminController.php` dashboard, editorial queue, user management, ledger, backup manager.
15. [`15_controller_api.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/15_controller_api.md) - `ApiController.php` RESTful JSON endpoints.
16. [`16_controller_auth.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/16_controller_auth.md) - `AuthController.php` authentication, registration, password reset, 2FA, onboarding.
17. [`17_controller_author_application.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/17_controller_author_application.md) - `AuthorApplicationController.php` author applications.
18. [`18_controller_author_profile.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/18_controller_author_profile.md) - `AuthorProfileController.php` public creator showcase.
19. [`19_controller_author_studio.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/19_controller_author_studio.md) - `AuthorStudioController.php` author creator studio dashboard and editor.
20. [`20_controller_blog.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/20_controller_blog.md) - `BlogController.php` blog feed and article reader.
21. [`21_controller_directory.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/21_controller_directory.md) - `DirectoryController.php` AI Tools, Prompts, GPTs, Campaigns, Jobs, `/go/{slug}` affiliate click tracking.
22. [`22_controller_home.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/22_controller_home.md) - `HomeController.php` homepage and contact form.
23. [`23_controller_inbox.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/23_controller_inbox.md) - `InboxController.php` peer messaging.
24. [`24_controller_page.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/24_controller_page.md) - `PageController.php` static CMS pages.
25. [`25_controller_search.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/25_controller_search.md) - `SearchController.php` global search.
26. [`26_controller_support.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/26_controller_support.md) - `SupportController.php` support tickets.
27. [`27_controller_topic.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/27_controller_topic.md) - `TopicController.php` topic hub.

### ⚙️ Data Models & Business Engines (18 Files)
28. [`28_model_analytics.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/28_model_analytics.md) - `Analytics.php` traffic logging.
29. [`29_model_audit_log.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/29_model_audit_log.md) - `AuditLog.php` security logging.
30. [`30_model_author.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/30_model_author.md) - `Author.php` creator profile data & reputation.
31. [`31_model_author_tier_engine.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/31_model_author_tier_engine.md) - `AuthorTierEngine.php` 20-sublevel author progression.
32. [`32_model_co_author_engine.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/32_model_co_author_engine.md) - `CoAuthorEngine.php` multi-author collaboration.
33. [`33_model_comment.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/33_model_comment.md) - `Comment.php` threaded comments.
34. [`34_model_content.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/34_model_content.md) - `Content.php` content entity model.
35. [`35_model_creator_referral.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/35_model_creator_referral.md) - `CreatorReferral.php` referral link engine.
36. [`36_model_creator_tipping.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/36_model_creator_tipping.md) - `CreatorTipping.php` member author tipping.
37. [`37_model_database_backup_manager.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/37_model_database_backup_manager.md) - `DatabaseBackupManager.php` SQL backup exports.
38. [`38_model_financial_ledger.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/38_model_financial_ledger.md) - `FinancialLedger.php` double-entry financial ledger.
39. [`39_model_persona_follower_engine.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/39_model_persona_follower_engine.md) - `PersonaFollowerEngine.php` author follow graph.
40. [`40_model_plagiarism_engine.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/40_model_plagiarism_engine.md) - `PlagiarismEngine.php` n-gram similarity detection.
41. [`41_model_rbac.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/41_model_rbac.md) - `RBAC.php` access control matrix.
42. [`42_model_security_anomaly_engine.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/42_model_security_anomaly_engine.md) - `SecurityAnomalyEngine.php` threat detection scanner.
43. [`43_model_session_manager.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/43_model_session_manager.md) - `SessionManager.php` device session tracking.
44. [`44_model_support_engine.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/44_model_support_engine.md) - `SupportEngine.php` support tickets.
45. [`45_model_user.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/45_model_user.md) - `User.php` account model & 3-strike enforcement.

### 🗄️ Database Schemas & Migrations (5 Files)
46. [`46_db_master_schema.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/46_db_master_schema.md) - `schema.sql` 76 tables breakdown.
47. [`47_db_seed_data.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/47_db_seed_data.md) - `seeds.sql` initial dataset.
48. [`48_db_upgrade_v2.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/48_db_upgrade_v2.md) - `database_upgrade_v2.sql` v2 schema migration.
49. [`49_db_upgrade_help_legal.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/49_db_upgrade_help_legal.md) - `help_legal_upgrade.sql` CMS upgrade.
50. [`50_db_upgrade_mail_tracking.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/50_db_upgrade_mail_tracking.md) - `mail_tracking_upgrade.sql` open tracking logs.

### 🎨 Assets, Views & Workflows (10 Files)
51. [`51_assets_css_design_system.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/51_assets_css_design_system.md) - CSS design system tokens.
52. [`52_assets_javascript_engines.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/52_assets_javascript_engines.md) - Client JS scripts.
53. [`53_views_layouts_and_components.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/53_views_layouts_and_components.md) - Master layouts & UI components.
54. [`54_views_admin_control_panel.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/54_views_admin_control_panel.md) - 49 admin view templates.
55. [`55_views_auth_and_onboarding.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/55_views_auth_and_onboarding.md) - Authentication templates.
56. [`56_views_author_studio.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/56_views_author_studio.md) - Creator studio templates.
57. [`57_views_blog_and_directories.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/57_views_blog_and_directories.md) - Blog & directory templates.
58. [`58_views_public_and_pages.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/58_views_public_and_pages.md) - Public portal & CMS pages.
59. [`59_views_account_support_and_errors.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/59_views_account_support_and_errors.md) - Account, support & error views.
60. [`60_complete_system_workflows.md`](file:///c:/xampp/htdocs/techscout.n/documentation/project/60_complete_system_workflows.md) - End-to-end user workflows.

---

## 📚 Master Index Reference Guides

| Document | Description |
| :--- | :--- |
| 🧪 **[`qa_testing_master_guide.md`](file:///c:/xampp/htdocs/techscout.n/documentation/qa_testing_master_guide.md)** | Step-by-step QA testing suite for all features. |
| 📁 **[`complete_files_and_folders_guide.md`](file:///c:/xampp/htdocs/techscout.n/documentation/complete_files_and_folders_guide.md)** | Directory & file listing with full features. |
| 🪪 **[`symbols_and_functions_catalog.md`](file:///c:/xampp/htdocs/techscout.n/documentation/symbols_and_functions_catalog.md)** | Catalog of PHP classes/methods & JS functions. |
| 🗂️ **[`file_by_file_reference.md`](file:///c:/xampp/htdocs/techscout.n/documentation/file_by_file_reference.md)** | Technical reference for all 201 files. |
