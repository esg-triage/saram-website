# Hosting Setup — saramdigitech.com

## Overview

Static multi-page HTML site hosted on GitHub Pages. No build step, no framework — browsers load files directly.

```
Edit files locally → branch → PR → merge → GitHub Pages auto-deploys → live
```

**Live URL (GitHub Pages):** https://esg-triage.github.io/saram-website/  
**Custom domain:** saramdigitech.com  
**Repo:** https://github.com/esg-triage/saram-website

---

## Site structure

```
index.html              ← Home  →  saramdigitech.com/
sap-ariba/
  index.html            ← SAP Ariba Consulting  →  saramdigitech.com/sap-ariba/
esg/
  index.html            ← ESG Consulting  →  saramdigitech.com/esg/
contact/
  index.html            ← Contact  →  saramdigitech.com/contact/
robots.txt              ← Points crawlers to sitemap.xml
sitemap.xml             ← 4 pages, saramdigitech.com URLs
CNAME                   ← Custom domain (add when going live)
assets/
  site.css              ← Shared stylesheet (all pages)
  logo.png              ← Logo — nav, footer, favicon, OG image
  og-cover.png          ← 1200×630 social preview image (add before launch)
docs/
  hosting-setup.md      ← This file
```

Pages use folder/index.html so no `.html` extension appears in the URL. GitHub Pages serves `folder/index.html` automatically when the path `folder/` is requested. Subpages reference assets with `../assets/` (one level up).

---

## Design system

### Tokens (`assets/site.css`)

| Token        | Value      | Used for                     |
|--------------|------------|------------------------------|
| `--paper`    | `#F5F6F8`  | Default page background      |
| `--paper2`   | `#ECEEF2`  | Alternate section background |
| `--teal-tint`| `#EAF3F4`  | ESG section background       |
| `--ink`      | `#232133`  | Body text / dark hero bg     |
| `--ink2`     | `#5C5A70`  | Secondary / muted text       |
| `--line`     | `rgba(35,33,51,0.12)` | Borders           |
| `--ariba`    | `#3F3D9E`  | SAP Ariba primary            |
| `--ariba2`   | `#5E60C9`  | SAP Ariba accent             |
| `--esg`      | `#0F8A93`  | ESG primary                  |
| `--esg2`     | `#23A8B6`  | ESG accent                   |
| `--nav-h`    | `76px`     | Navigation bar height        |

### Fonts (Google Fonts CDN)

| Family               | Weights   | Role                         |
|----------------------|-----------|------------------------------|
| Bricolage Grotesque  | 700, 800  | All headings (H1–H4)         |
| Hanken Grotesk       | 400–700   | Body text                    |
| Space Mono           | 400, 700  | Eyebrow labels, step numbers |

---

## Navigation

The nav uses a **3-column CSS Grid** (`1fr auto 1fr`) so the links are optically centred regardless of logo width:

- **Col 1 (1fr, left):** `.nav-logo` — `logo.png`, links to `/` (root from subpages: `../`)
- **Col 2 (auto, centre):** `.desk-nav` — Home / SAP Ariba / ESG Consulting
- **Col 3 (1fr, right):** `.nav-right` — "Talk to us" pill button + mobile hamburger

The active page receives `class="active"` on the matching `.desk-nav` link. On the Contact page, `.talk-btn` gets `class="talk-btn active"` (turns ariba-blue).

At ≤880 px the desktop nav hides and the hamburger reveals a full-screen mobile menu (`.mob-nav`).

There is no dropdown. The old Divisions dropdown was removed in PR #7.

---

## Page-by-page content guide

### `index.html` — Home (`/`)

| Section | Background | Notes |
|---------|-----------|-------|
| Hero | `--ink` dark, radial glow blob | H1 with `.grad-text` spans; 3 CTAs: `.btn-ariba`, `.btn-esg`, `.btn-ghost` |
| Two practices | `--paper` | 2-col `.grid2` of `.pcard.ariba` and `.pcard.esg` |
| How we engage | `.bridge` dark | 3-step `.step-row` (Scope / Build / Sustain) |
| About | `--paper2` | `.divhead` 2-col: text + `.quote` blockquote |
| CTA | `--paper` | Centred, links to `contact/` |
| Footer | `--ink` | 4-col grid: logo/desc, Practices, Company, Reach us |

### `sap-ariba/index.html` — SAP Ariba Consulting (`/sap-ariba/`)

| Section | Background | Notes |
|---------|-----------|-------|
| Hero | `--paper`, ariba glow blob | Eyebrow "Practice 01 · SAP Ariba"; `.chip.ariba` tags |
| What we do | `--paper2` | Intro + 2×2 `.svc-cell` grid (inline border, ariba top-strip) |
| CTA | `--paper` | "Planning an SAP Ariba programme?" → `.btn-ariba` |
| Footer | `--ink` | Same 4-col pattern |

### `esg/index.html` — ESG Consulting (`/esg/`)

| Section | Background | Notes |
|---------|-----------|-------|
| Hero | `--paper`, esg glow blob | Eyebrow "Practice 02 · Corporate ESG"; italic tagline |
| What we deliver | `.bridge` dark | 4 `.bridge-point` items; `.grad-text` in H2 |
| Industries | `--teal-tint` | 12 `.chip.esg` sector chips |
| Training | `--paper2` | `.divhead` 2-col; 7 inline pill buttons; link to `../contact/` |
| CTA | `.bridge` dark | `.btn-esg` |
| Footer | `--ink` | Same 4-col pattern |

### `contact/index.html` — Contact (`/contact/`)

| Section | Background | Notes |
|---------|-----------|-------|
| Contact | `--paper2` | `.contact-grid` 2-col: info left, form right |
| Left | — | Eyebrow, H1, description, phone + email links |
| Right | `.cf-wrap` (white card) | `<form id="cf">` with name/company/email/practice/message |
| Footer | `--ink` | Same 4-col pattern |

Nav state on this page: `.talk-btn.active` (ariba blue).

---

## Contact form backend

The form POSTs to `https://saramdigitech.com/contact.php`.  
That PHP file lives on Hostinger (`public_html/contact.php`) — **it is not in this repo**.

The `<form>` element has both `action` (no-JS fallback) and a JS `fetch()` handler that shows an inline success state without a page reload.

`contact.php` on Hostinger:
- Validates name, email, message
- Sets CORS headers for `esg-triage.github.io` and `saramdigitech.com`
- Sends to `info@saramdigitech.com` via `mail()`
- Returns `{"ok": true}` on success or `{"ok": false, "error": "..."}` on failure

If the Hostinger domain is not yet live, form submissions fail and show an inline error message.

---

## SEO / meta

Every page includes:

```html
<link rel="canonical" href="https://saramdigitech.com/[page]">
<link rel="icon" type="image/png" href="assets/logo.png">
<meta property="og:type" content="website">
<meta property="og:url" content="https://saramdigitech.com/[page]">
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta property="og:image" content="https://saramdigitech.com/assets/og-cover.png">
<meta name="twitter:card" content="summary_large_image">
```

**Before launch:** create `assets/og-cover.png` at 1200×630 px. Until then `og:image` will resolve to a missing URL (harmless but won't show a rich preview on social).

`robots.txt` allows all crawlers and points to `https://saramdigitech.com/sitemap.xml`.  
`sitemap.xml` lists all 4 pages with priority 1.0 / 0.9 / 0.7.

---

## Development workflow

```bash
# 1. Start from a clean main branch
git checkout main && git pull

# 2. Create a feature branch
git checkout -b feat/your-change

# 3. Edit HTML / CSS files

# 4. Test locally
#    Open index.html directly in browser for the home page.
#    For subpages, run a simple server so relative paths resolve correctly:
#      python3 -m http.server 8000
#    Then visit http://localhost:8000

# 5. Commit
git add index.html sap-ariba/index.html esg/index.html contact/index.html assets/site.css
git commit -m "describe the change"

# 6. Push and open PR
git push origin feat/your-change
gh pr create --base main --title "Brief title"

# 7. Merge → live on GitHub Pages within ~30 s
```

All CSS lives in `assets/site.css`. Avoid inline styles except for one-off layout values (hero padding, section backgrounds) — reusable patterns belong in the stylesheet.

---

## Custom domain setup

### Step 1 — CNAME file

```bash
echo "saramdigitech.com" > CNAME
git add CNAME && git commit -m "chore: add custom domain CNAME"
git push origin main
```

### Step 2 — DNS on Hostinger

**A records** — Host `@`, all four GitHub Pages IPs:

| Type | Host | Value            | TTL  |
|------|------|-----------------|------|
| A    | @    | 185.199.108.153 | 3600 |
| A    | @    | 185.199.109.153 | 3600 |
| A    | @    | 185.199.110.153 | 3600 |
| A    | @    | 185.199.111.153 | 3600 |

**CNAME** — www subdomain:

| Type  | Host | Value                  | TTL  |
|-------|------|------------------------|------|
| CNAME | www  | esg-triage.github.io   | 3600 |

Remove any conflicting A / CNAME records left over from the old WordPress site.

### Step 3 — GitHub Pages settings

Settings → Pages → Custom domain → enter `saramdigitech.com` → Save.  
Wait for DNS check to pass, then tick **Enforce HTTPS**.

### Step 4 — contact.php CORS update

Once the domain is live, update `$allowed_origins` in `contact.php` on Hostinger:

```php
$allowed_origins = [
  'https://esg-triage.github.io',
  'https://saramdigitech.com',
  'https://www.saramdigitech.com',
];
```

### Verification checklist

- [ ] `https://saramdigitech.com` loads with valid HTTPS
- [ ] `https://www.saramdigitech.com` redirects to root domain
- [ ] `https://esg-triage.github.io/saram-website/` still works
- [ ] Contact form submits → email arrives at `info@saramdigitech.com`
- [ ] No mixed-content warnings in dev tools

---

## PR history

| PR | Description |
|----|-------------|
| #1 | Initial documentation |
| #2 | Saram Digitech website redesign (Draftcode bundle) |
| #3 | Fix sticky header and Divisions dropdown |
| #4 | Click-based dropdown (closed — reverted in favour of CSS hover) |
| #5 | CSS `:hover` dropdown fix |
| #6 | Convert to multi-page site (current structure) |
| #7 | Content refresh v2: wireframe copy applied verbatim, nav centred (CSS grid), dead CSS removed, favicon + OG tags + canonical added, sitemap.xml + robots.txt fixed, contact form `action` fallback added |
| #8 | Clean URL restructure: pages moved to `folder/index.html` pattern — no `.html` in URLs |
