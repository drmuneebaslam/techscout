# TechScout Views & Frontend Architecture

This document provides complete documentation for TechScout's front-end architecture, view layout hierarchy, reusable components, CSS design system, and client-side JavaScript execution engine.

---

## 1. View Directory Layout

TechScout's template layer is located in `views/` and strictly uses native PHP templates (`.php`) with auto-escaped data variables (`e()`) for maximum execution speed and zero template compilation overhead.

```
views/
├── account/            # User account settings, profile edit, security & 2FA views
├── admin/              # Admin control panel views (Dashboard, Users, Mail, Financials)
├── auth/               # Authentication views (Login, Register, Forgot Password, Onboarding)
├── author/             # Author studio views (Dashboard, Article Editor, Analytics, Earnings)
├── blog/               # Blog views (Article list, Single article reader, Categories)
├── components/         # Reusable UI partials (Comments, Share Buttons, Category Explorer)
├── directory/          # Directory views (AI Tools, Prompts, GPTs, Campaigns, Jobs)
├── errors/             # Custom HTTP error templates (404 Not Found, 500 Server Error)
├── inbox/              # Direct messaging thread views
├── layouts/            # Master page wrapper layouts (Header, Footer, Admin Header/Footer)
├── notifications/      # In-app notification center views
├── pages/              # Static & CMS content page layouts (Terms, Privacy, About)
├── public/             # Public landing pages and contact forms
└── support/            # Support ticket submission and messaging views
```

---

## 2. Layout Hierarchy & Component System

### Layout Templates (`views/layouts/`)
- **`header.php`**: Master public header containing HTML `<head>`, CSS includes (`assets/css/style.css`), meta tags, main navigation bar, global search modal trigger, notification badge dropdown, user profile menu, and theme toggler.
- **`footer.php`**: Master public footer containing footer navigation links, newsletter subscription widget, social media icons, brand copyright, and JS scripts (`assets/js/lucide.min.js`, `assets/js/main.js`).
- **`admin_header.php`**: Specialized dashboard header containing sidebar navigation links for administrative sub-panels, active route indicators, quick stat badges, and super-admin session controls.
- **`admin_footer.php`**: Dashboard footer including administrative JS utilities (`assets/js/admin_table_paginator.js`).

### Reusable UI Components (`views/components/`)
- **`comments_section.php`**: Renders nested comment trees, reply forms, upvote buttons, and moderation report triggers.
- **`social_share.php`**: Social share buttons with pre-formatted share links for Twitter/X, LinkedIn, Facebook, WhatsApp, and Reddit.
- **`category_explorer.php`**: Visual grid card selector for browsing taxonomy categories and topics.
- **`report_modal.php`**: Popup modal dialog for members to report spam, copyright infringement, or abuse on content items.

---

## 3. CSS Design System (`assets/css/`)

TechScout uses Vanilla CSS3 with dynamic CSS custom properties (variables) to maintain a modern aesthetic featuring sleek dark/light mode modes, subtle glassmorphism, and fluid layout grids.

### Core Stylesheet (`assets/css/style.css`)
- **CSS Custom Properties**:
  ```css
  :root {
      --bg-primary: #0f172a;
      --bg-secondary: #1e293b;
      --text-primary: #f8fafc;
      --text-muted: #94a3b8;
      --accent-color: #6366f1;
      --accent-hover: #4f46e5;
      --border-color: #334155;
      --card-bg: rgba(30, 41, 59, 0.7);
      --glass-blur: blur(12px);
      --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
  }
  ```
- **Typography & Layout**: Modern sans-serif fonts, responsive flexbox and CSS Grid containers.
- **UI Styling**: Glassmorphic cards, custom form controls, pill badges, subtle hover micro-animations, custom scrollbars, and toast alerts.

### Admin Dashboard Stylesheet (`assets/css/admin.css`)
- Specialized layout CSS for dark sidebar, sticky data table headers, status pill colors (`published`, `pending`, `suspended`), financial charts, and modal overlay animations.

---

## 4. Client-Side JavaScript Engine (`assets/js/`)

- **`main.js`**: Core front-end script handling:
  - Header global search auto-complete dropdown via AJAX (`/api/v1/search`).
  - Dark/Light mode theme switching with `localStorage` persistence.
  - Interactive upvote/downvote reaction handling via asynchronous `fetch()` calls.
  - Bookmark toggling and copy-prompt-to-clipboard functionality.
  - Toast notification display system.
- **`content-engine.js`**: Asynchronous infinite scroll loader, client-side category filtering, and live search filtration for directories.
- **`admin_table_paginator.js`**: Client-side data table filter, search bar, sorting, and pagination helper for admin data tables.
- **`lucide.min.js`**: Lucide SVG icon library bundled locally for fast icon rendering without external CDN latency.
