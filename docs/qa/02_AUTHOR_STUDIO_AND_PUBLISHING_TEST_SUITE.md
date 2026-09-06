# QA Test Suite 02: Author Studio, Word Import & Template Gating

## 1. Test Suite Metadata
* **Module:** Author Studio, WYSIWYG Ribbon, DOCX Parser, Publishing Queue, Template Permission Security
* **Target Routes:** `/author/dashboard`, `/author/editor`, `/author/editor/{id}`, `/author/editor/import-docx`, `/author/editor/save`, `/admin/content`

---

## 2. Detailed Test Cases

### TC-STUDIO-01: Template Permission Gating (Strict Enforcement)
* **Preconditions:**
  - Author User A has `can_use_templates = 0` (unauthorized).
  - Author User B has `can_use_templates = 1` (authorized by admin).
* **Test Steps:**
  1. Log in as Author User A and visit `/author/editor`.
  2. Inspect editor header, WYSIWYG ribbon, and empty canvas placeholder.
  3. Log in as Author User B and visit `/author/editor`.
* **Expected Result:**
  - **For Author User A:** "Save as Template" button, ribbon "Insert Template" button, starter Pro Tip template CTA, and template selector modal are completely HIDDEN from DOM.
  - **For Author User B:** Template library modal and "Save as Template" buttons are accessible.

### TC-STUDIO-02: Word Document (.docx) High-Fidelity Import
* **Preconditions:** Logged in as verified author on `/author/editor`.
* **Test Steps:**
  1. Click "Import Word (.docx)" in editor header.
  2. Upload a sample `.docx` file containing bold text, H1/H2 headings, lists, and code blocks.
  3. Verify fallback parsing if ZipArchive is disabled vs enabled.
* **Expected Result:**
  - Document contents are converted cleanly into semantic HTML inside the WYSIWYG editor canvas without corruption.

### TC-STUDIO-03: Auto-Save & Word Count Telemetry
* **Preconditions:** Author composing draft in `/author/editor`.
* **Test Steps:**
  1. Type article title and body content.
  2. Verify live word count and calculated reading time minutes (words / 200 WPM).
  3. Click "Save Draft".
* **Expected Result:**
  - Draft is saved to `contents` & `articles` tables; returns success toast and retains article ID in URL.

### TC-STUDIO-04: Editorial Review Queue Approval Flow
* **Preconditions:** Article submitted with `status = 'in_review'`.
* **Test Steps:**
  1. Log in as Admin and visit `/admin/content` (Content Review Queue).
  2. Verify pending article card with author details and diff preview.
  3. Click "Approve & Publish".
* **Expected Result:**
  - Article status transitions to `published`, `published_at` timestamp is set to `NOW()`, and article immediately appears on the public `/blog` and category feeds.
