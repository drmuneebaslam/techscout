# TechScout — Brand Strategy & Design System

> **Tasks Covered**: 1 through 30 (Phase 1 — Brand Strategy & Product Foundation)  
> **Target Audience**: Developers, Tech Enthusiasts, AI Engineers, Indie Hackers, Tech Leaders  
> **Design Philosophy**: High-tech minimalism, deep dark-mode glassmorphism, hyper-legible typography, vivid neon accents, and fluid micro-interactions.

---

## 1. Brand Strategy & Core Identity (Tasks 1–11)

### Task 1: Brand Positioning
TechScout is the definitive intelligence hub for modern software architects, AI builders, and tech leaders. It synthesizes deep technical journalism, authoritative engineering tutorials, and an interactive directory of AI tools, custom GPTs, prompt templates, and software benchmarks into a single, unified ecosystem.

### Task 2: Target Audience
1. **Software Engineers & Architects**: Seeking in-depth tutorials, system design breakdowns, and code blueprints.
2. **AI Practitioners & Prompt Engineers**: Searching for tested prompts, custom GPTs, model benchmarks, and API tools.
3. **Tech Founders & Indie Hackers**: Looking for stack comparisons, developer tooling, and growth case studies.
4. **Technology Enthusiasts & Analysts**: Following emerging tech breakthroughs, industry trends, and in-depth tech journalism.

### Tasks 3 & 4: Audience Personas
- **Primary Persona: Alex Vance (The Senior Full-Stack / AI Engineer)**:
  - *Age*: 29 | *Goal*: Automate dev workflows, discover production-ready AI tools, and share technical articles to build industry authority.
  - *Pain Points*: Fragmented tool directories, shallow AI clickbait, lack of code-verified prompt repositories.
- **Secondary Persona: Maya Lin (The Tech Founder / Product Lead)**:
  - *Age*: 34 | *Goal*: Benchmark SaaS stacks, track AI industry movements, and discover tools that cut MVP time-to-market.
  - *Pain Points*: Overwhelming noise in AI releases, lack of real user reviews and transparent pricing breakdowns.

### Tasks 5–7: Brand Mission, Vision & Values
- **Mission (Task 5)**: To scout, demystify, and curate the forefront of technology, empowering builders to create the future with clarity and speed.
- **Vision (Task 6)**: To become the world's most trusted developer knowledge base and AI resource engine.
- **Values (Task 7)**:
  - *Rigorous Authenticity*: Zero hallucinated fluff; every tutorial and benchmark is tested.
  - *Builder First*: Code blocks, copyable prompts, and live demos over generic theory.
  - *Empowering Openness*: Community-driven creator rewards, open discourse, and transparent editorial policies.

### Tasks 8–11: Brand Personality, Voice & Tagline
- **Personality (Task 8)**: Intelligent, visionary, pragmatic, precise, cutting-edge, and accessible.
- **Voice (Task 9)**: Authoritative yet inviting, technical without unnecessary jargon, analytical and action-oriented.
- **Editorial Tone (Task 10)**: Informative, objective, respectful of reader time, and packed with actionable takeaways.
- **Tagline (Task 11)**: *"The Frontier of Tech, Scouted for Builders."*

---

## 2. Visual Identity & Brand Assets (Tasks 12–16)

### Task 12: Primary Logo
- **Concept**: A stylized hexagonal radar / scout aperture interlaced with modern terminal brackets `[ / > ]` representing discovery and code.
- **Lockup**: Vector icon mark on the left (40x40px), followed by wordmark **Tech**`Scout` where `Tech` is set in Bold Inter/Outfit with an electric violet gradient and `Scout` is in clean white/slate.
- **Aspect Ratio**: 4.5 : 1 (Horizontal navbar lockup).

### Task 13: Secondary Logo & Stacked Mark
- Compact square badge layout (1:1 aspect ratio) for social avatars, splash screens, and mobile header collapsing.

### Task 14: Favicon & PWA Icons
- Multi-resolution SVG & PNG formats: 16x16, 32x32, 48x48, 180x180 (Apple Touch Icon), 192x192, and 512x512 (PWA Manifest).
- Features the high-contrast glowing Cyan/Violet scout emblem on a deep obsidian (`#0B0F19`) background.

### Tasks 15 & 16: Logo Usage Rules
1. **Minimum Clear Space**: 50% of the icon's width must surround the logo on all four sides.
2. **Minimum Size**: 120px wide in digital interfaces, 24px height in compact navbars.
3. **Prohibited Modifications**: Do not stretch, rotate, alter gradient angles, or place on non-contrast backgrounds.

---

## 3. Design System & CSS Variables (Tasks 17–25)

The entire platform uses native CSS Custom Properties (`:root` tokens) supporting seamless Dark and Light themes with zero external compile step.

```css
/* ==========================================================================
   TECHSCOUT DESIGN TOKENS — ROOT VARIABLES
   ========================================================================== */
:root {
  /* Primary & Accent Colors (Task 17 & 18) */
  --ts-primary: #6366f1;          /* Electric Indigo */
  --ts-primary-hover: #4f46e5;
  --ts-primary-light: #818cf8;
  --ts-primary-glow: rgba(99, 102, 241, 0.35);

  --ts-accent: #06b6d4;           /* Cyber Cyan */
  --ts-accent-hover: #0891b2;
  --ts-accent-light: #22d3ee;
  --ts-accent-glow: rgba(6, 182, 212, 0.35);

  --ts-secondary: #ec4899;        /* Neon Pink */
  --ts-secondary-glow: rgba(236, 72, 153, 0.35);

  --ts-success: #10b981;          /* Emerald Green */
  --ts-success-glow: rgba(16, 185, 129, 0.3);
  --ts-warning: #f59e0b;          /* Amber */
  --ts-danger: #ef4444;           /* Crimson Red */
  --ts-info: #3b82f6;             /* Royal Blue */

  /* Dark Theme Surfaces (Default) */
  --ts-bg-main: #07090e;          /* Deep Obsidian Void */
  --ts-bg-surface: #0f172a;       /* Slate Dark Base */
  --ts-bg-surface-elevated: #1e293b; /* Card Surface */
  --ts-bg-surface-hover: #334155;
  --ts-bg-glass: rgba(15, 23, 42, 0.75);
  --ts-bg-glass-card: rgba(30, 41, 59, 0.6);

  /* Typography Colors */
  --ts-text-primary: #f8fafc;     /* Bright White/Slate */
  --ts-text-secondary: #94a3b8;   /* Muted Slate */
  --ts-text-tertiary: #64748b;    /* Dark Muted */
  --ts-text-accent: #38bdf8;
  --ts-border-main: rgba(148, 163, 184, 0.12);
  --ts-border-focus: rgba(99, 102, 241, 0.5);

  /* Typography Scale (Tasks 19-22) */
  --ts-font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  --ts-font-heading: 'Outfit', 'Inter', sans-serif;
  --ts-font-mono: 'JetBrains Mono', 'Fira Code', monospace;

  --ts-text-xs: 0.75rem;    /* 12px */
  --ts-text-sm: 0.875rem;   /* 14px */
  --ts-text-base: 1rem;     /* 16px */
  --ts-text-lg: 1.125rem;   /* 18px */
  --ts-text-xl: 1.25rem;    /* 20px */
  --ts-text-2xl: 1.5rem;    /* 24px */
  --ts-text-3xl: 1.875rem;  /* 30px */
  --ts-text-4xl: 2.25rem;   /* 36px */
  --ts-text-5xl: 3rem;      /* 48px */
  --ts-text-6xl: 3.75rem;   /* 60px */

  /* Spacing Scale (Task 23) */
  --ts-space-1: 0.25rem;   /* 4px */
  --ts-space-2: 0.5rem;    /* 8px */
  --ts-space-3: 0.75rem;   /* 12px */
  --ts-space-4: 1rem;      /* 16px */
  --ts-space-5: 1.25rem;   /* 20px */
  --ts-space-6: 1.5rem;    /* 24px */
  --ts-space-8: 2rem;      /* 32px */
  --ts-space-10: 2.5rem;   /* 40px */
  --ts-space-12: 3rem;     /* 48px */
  --ts-space-16: 4rem;     /* 64px */
  --ts-space-20: 5rem;     /* 80px */

  /* Border Radius (Task 24) */
  --ts-radius-sm: 6px;
  --ts-radius-md: 10px;
  --ts-radius-lg: 16px;
  --ts-radius-xl: 24px;
  --ts-radius-full: 9999px;

  /* Shadows & Glass Elevation (Task 25) */
  --ts-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
  --ts-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -2px rgba(0, 0, 0, 0.3);
  --ts-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -4px rgba(0, 0, 0, 0.4);
  --ts-shadow-glow: 0 0 25px var(--ts-primary-glow);
  --ts-shadow-cyan-glow: 0 0 25px var(--ts-accent-glow);

  /* Transitions & Animation Curves */
  --ts-transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
  --ts-transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
  --ts-transition-smooth: 400ms cubic-bezier(0.16, 1, 0.3, 1);

  /* Layout Grids */
  --ts-max-width: 1320px;
  --ts-header-height: 72px;
  --ts-sidebar-width: 280px;
}

/* Light Theme Variable Overrides */
[data-theme="light"] {
  --ts-bg-main: #f8fafc;
  --ts-bg-surface: #ffffff;
  --ts-bg-surface-elevated: #f1f5f9;
  --ts-bg-surface-hover: #e2e8f0;
  --ts-bg-glass: rgba(255, 255, 255, 0.85);
  --ts-bg-glass-card: rgba(255, 255, 255, 0.7);

  --ts-text-primary: #0f172a;
  --ts-text-secondary: #475569;
  --ts-text-tertiary: #94a3b8;
  --ts-border-main: rgba(15, 23, 42, 0.08);
  --ts-shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
  --ts-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
  --ts-shadow-lg: 0 10px 20px -3px rgba(0, 0, 0, 0.1);
}
```

---

## 4. Typography System (Tasks 19–22)

### Heading Hierarchy (Task 20)
- **H1 / Display**: `font-family: var(--ts-font-heading); font-size: var(--ts-text-5xl); font-weight: 800; letter-spacing: -0.025em; line-height: 1.15;`
- **H2 / Section Title**: `font-family: var(--ts-font-heading); font-size: var(--ts-text-3xl); font-weight: 700; letter-spacing: -0.02em; line-height: 1.25;`
- **H3 / Card Title**: `font-family: var(--ts-font-heading); font-size: var(--ts-text-xl); font-weight: 600; line-height: 1.35;`
- **H4 / Subtitle**: `font-family: var(--ts-font-sans); font-size: var(--ts-text-lg); font-weight: 600; line-height: 1.4;`

### Body Typography (Task 21)
- **Body Regular**: `font-family: var(--ts-font-sans); font-size: var(--ts-text-base); line-height: 1.75; color: var(--ts-text-secondary);`
- **Lead Paragraph**: `font-size: var(--ts-text-lg); line-height: 1.65; color: var(--ts-text-primary); font-weight: 400;`

### UI Typography (Task 22)
- **Buttons / Badges / Nav**: `font-family: var(--ts-font-sans); font-size: var(--ts-text-sm); font-weight: 600; letter-spacing: 0.01em; text-transform: none;`
- **Code & Prompts**: `font-family: var(--ts-font-mono); font-size: var(--ts-text-sm); line-height: 1.6;`

---

## 5. Iconography, Media & Thumbnail Standards (Tasks 26–30)

### Task 26: Icon System
- Integrated via **Lucide Icons CDN**.
- Default stroke width: `1.75px`. Standard sizes: `16px` (inline/badges), `20px` (buttons/navigation), `24px` (feature icons), `32px` (hero badges).

### Tasks 27 & 28: Illustration & Image Style
- **Visual Vibe**: Futuristic cyber-mesh gradients, isometric 3D wireframes, and dark obsidian glass surfaces with subtle inner neon glows.
- **Aspect Ratios**: 16:9 for article hero images (1200x675px), 1:1 for tool logos & author avatars (400x400px), 2:1 for campaign banners (1200x600px).
- **Optimization**: Converted automatically to WebP/AVIF with responsive `srcset` (`400w`, `800w`, `1200w`).

### Task 29: Thumbnail Style Rules
- Deep gradient backdrop (`linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%)`).
- Category badge placed top-left; estimated reading time badge placed top-right.
- Subtle `0 0 1px rgba(255,255,255,0.15)` border with hover card lift (`translateY(-4px)` + cyan/indigo shadow bloom).

---

## 6. Standard CDN Resource Inclusions

All TechScout views include these curated, high-performance CDNs:

```html
<!-- Google Fonts (Outfit, Inter, JetBrains Mono) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<!-- Chart.js 4.x -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<!-- Prism.js Syntax Highlighting & Copy Plugin -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

<!-- Toastify & SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DOMPurify (XSS Protection for dynamic render) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.0.8/purify.min.js"></script>
```
