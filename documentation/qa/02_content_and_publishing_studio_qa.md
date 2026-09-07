# QA Test Guide 02: Content & Publishing Studio QA

This document details the step-by-step Enterprise QA testing suite for Content Creation, Publishing Workflows, Co-Author Engine (`app/Models/CoAuthorEngine.php`), Plagiarism Engine (`app/Models/PlagiarismEngine.php`), SEO Studio, and Content Queue Moderation (`app/Controllers/BlogController.php`, `app/Controllers/AuthorStudioController.php`).

---

## Pre-Test Setup & Requirements

| Requirement | Value / Condition |
| :--- | :--- |
| **Base URL** | `http://localhost/techscout.n/` |
| **User Sessions** | Author A (`author@techscout.com`), Author B (`coauthor@techscout.com`), Admin (`admin@techscout.com`). |
| **Test Assets** | Sample DOCX file (`sample_article.docx`), PNG/JPG header images (< 5MB), sample plagiarized text. |

---

## Test Suite 1.0: Article Authoring & Draft Management

### Test Case 1.1: New Article Creation in Author Studio
- **Objective**: Verify that an author can draft, format, upload media, and save an article.
- **Preconditions**: Logged in as `author@techscout.com`. Navigate to `/author/editor`.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Editor Form Completion**
   - 1.1: Enter Title: `Building Next-Gen AI Microservices with PHP 8.3` in `#article_title`.
   - 1.2: Select Topic: `Artificial Intelligence` in `#topic_id`.
   - 1.3: Enter Excerpt: `A deep dive into building asynchronous PHP microservices using modern AI tooling.` in `#excerpt`.
   - 1.4: Type rich content into Markdown/WYSIWYG editor (`#editor_body`).
   - 1.5: Add Tags: `PHP, AI, Microservices, TechScout` in `#tags_input`.

2. **Step 2: Featured Image Upload**
   - 2.1: Click "Upload Cover Image" (`#featured_image_input`).
   - 2.2: Select `ai_microservices_banner.png` (Dimensions 1200x630, File size 850KB).
   - 2.3: Verify client-side preview renders in image card.
   - 2.4: Verify server handles image crop/compression via GD/Imagick to `/uploads/blog/`.

3. **Step 3: Save Draft Action**
   - 3.1: Click "Save Draft" button (`#btn_save_draft`).
   - 3.2: Verify database record created in `contents` table:
     - `status = 'draft'`
     - `author_id = <author_a_id>`
     - `slug = 'building-next-gen-ai-microservices-with-php-8-3'`
   - 3.3: Verify toast alert: *"Draft saved successfully at 10:45 AM."*

---

### Test Case 1.2: DOCX File Import (`app/Core/DocxParser.php`)
- **Objective**: Verify automatic conversion of uploaded Word documents into formatted markdown article body.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Import Document**
   - 1.1: In Author Studio editor, click "Import DOCX Document" button.
   - 1.2: Select `sample_article.docx` containing headings, bullet lists, bold text, and embedded code blocks.
   - 1.3: Click "Upload & Parse".

2. **Step 2: Parser Output Validation**
   - 2.1: Verify `DocxParser::parse()` extracts document XML structure correctly.
   - 2.2: Verify `#editor_body` populates with clean markdown (`# Heading 1`, `**bold**`, `- bullet`).
   - 2.3: Verify embedded images in DOCX are extracted and saved to `/uploads/media/` directory.

---

## Test Suite 2.0: Co-Author Collaboration Engine

### Test Case 2.1: Inviting & Confirming Co-Authors (`app/Models/CoAuthorEngine.php`)
- **Objective**: Verify secondary author invitation, permissions, and attribution.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Send Co-Author Invitation**
   - 1.1: Author A opens article draft `building-next-gen-ai-microservices-with-php-8-3`.
   - 1.2: In "Co-Authors" tab, type `coauthor@techscout.com` and select contribution role: `Technical Editor`.
   - 1.3: Click "Add Co-Author".
   - 1.4: Confirm record created in `article_coauthors` table with `status = 'pending'`.

2. **Step 2: Co-Author Acceptance**
   - 2.1: Log in as Author B (`coauthor@techscout.com`).
   - 2.2: Navigate to `/author/invitations`.
   - 2.3: Verify pending invitation card displays article title and Author A avatar.
   - 2.4: Click "Accept Invitation".
   - 2.5: Verify database updates `article_coauthors.status = 'accepted'`.

3. **Step 3: Shared Editing Permissions**
   - 3.1: Verify Author B can access and edit article body in `/author/editor?id=XYZ`.
   - 3.2: Verify published article public page (`/blog/building-next-gen-ai-microservices-with-php-8-3`) displays dual author bios!

---

## Test Suite 3.0: Automated Plagiarism Engine Checks

### Test Case 3.1: Content Originality Verification (`app/Models/PlagiarismEngine.php`)
- **Objective**: Verify plagiarism scanner detects duplicated content against internal database index.

#### Test Execution Matrix:
| Sub-Step | Content Input | Originality Score | Engine Status | Action Triggered |
| :--- | :--- | :---: | :---: | :--- |
| **3.1.1** | 100% Unique original technical text | 98% Unique | `PASSED` | Article cleared for publication. |
| **3.1.2** | 30% Copied from existing published article | 70% Unique | `WARNING` | Flagged for manual editor review. |
| **3.1.3** | 90% Copied verbatim from another database row | 10% Unique | `BLOCKED` | Publication rejected: *"High similarity detected."* |

---

## Test Suite 4.0: SEO Studio & Meta Engine

### Test Case 4.1: SEO Metadata & OpenGraph Validation (`app/Core/SeoEngine.php`)
- **Objective**: Verify dynamic generation of meta tags, Schema.org JSON-LD structured data, and Twitter cards.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Configure SEO Fields**
   - 1.1: In Article Editor, expand "SEO & Meta Settings" accordion.
   - 1.2: Enter Meta Title: `AI Microservices PHP 8.3 Guide | TechScout`.
   - 1.3: Enter Meta Description: `Learn how to construct resilient AI microservices with PHP 8.3 and OpenAI APIs.`.
   - 1.4: Enter Canonical URL: `https://techscout.com/blog/ai-microservices-php-8-3`.

2. **Step 2: HTML Output Inspection**
   - 2.1: Save and view published article source code (`Ctrl + U`).
   - 2.2: Verify `<title>` matches Meta Title.
   - 2.3: Verify `<meta name="description">` matches Meta Description.
   - 2.4: Verify `<meta property="og:image">` points to absolute image URL.
   - 2.5: Verify `<script type="application/ld+json">` contains valid `@type: "BlogPosting"` JSON.

---

## Test Suite 5.0: Editorial Queue & Content Moderation

### Test Case 5.1: Submission to Editorial Queue
- **Objective**: Verify author submission flow to Admin Content Queue (`views/admin/content_queue.php`).

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Submit for Review**
   - 1.1: Author A clicks "Submit for Editorial Review".
   - 1.2: Confirm article status changes from `'draft'` to `'pending_review'`.
   - 1.3: Confirm article becomes read-only for Author A until review completes.

2. **Step 2: Admin Approval Workflow**
   - 2.1: Log in as Admin (`admin@techscout.com`).
   - 2.2: Navigate to `/admin/content-queue`.
   - 2.3: Verify article card displays in "Pending Queue" with similarity score, word count, and author info.
   - 2.4: Click "Approve & Publish".
   - 2.5: Confirm database `contents.status` updates to `'published'` and `published_at = NOW()`.
   - 2.6: Verify article is live on public blog index (`/blog`).

---

## Pass / Fail Criteria Checklist

- [ ] Draft saving, updating, and auto-save timers pass without data loss.
- [ ] DOCX parser converts headers, lists, bold text, and images properly.
- [ ] Co-Author engine updates permissions and renders dual attribution.
- [ ] Plagiarism engine blocks high-similarity articles.
- [ ] Schema.org JSON-LD validates clean against Google Rich Results test.
- [ ] Content queue transitions articles smoothly between Draft -> Review -> Published states.
- [ ] QA Inspector: TechScout Lead Content Engineer.
