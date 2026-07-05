# Hosting Setup — saramdigitech.com

## Overview

The site is a static multi-page HTML site hosted on GitHub Pages. No PHP,
database, or build step is involved — browsers load files directly.

```
Edit files locally → branch → PR → merge → GitHub Pages auto-deploys → live
```

**Live URL:** https://esg-triage.github.io/saram-website/  
**Custom domain (future):** saramdigitech.com  
**Repo:** https://github.com/esg-triage/saram-website

---

## Site structure

```
index.html          ← Home page (hero, practice cards, engagement model, about)
sap-ariba.html      ← SAP Ariba Consulting practice page
esg.html            ← ESG Consulting practice page (GHG, PCF, Training Academy)
contact.html        ← Contact page with form
robots.txt
assets/
  site.css          ← Shared stylesheet for all pages
  logo.png          ← Saram Digitech logo (used in nav + footer)
docs/
  hosting-setup.md  ← This file
```

### Design tokens (in `assets/site.css`)

| Token       | Value       | Used for                       |
|-------------|-------------|--------------------------------|
| `--ariba`   | `#3F3D9E`   | SAP Ariba primary              |
| `--ariba2`  | `#5E60C9`   | SAP Ariba accent               |
| `--esg`     | `#0F8A93`   | ESG primary                    |
| `--esg2`    | `#23A8B6`   | ESG accent                     |
| `--ink`     | `#232133`   | Body text / dark backgrounds   |
| `--ink2`    | `#5C5A70`   | Secondary text                 |
| `--paper`   | `#F5F6F8`   | Default page background        |
| `--paper2`  | `#ECEEF2`   | Alternate section background   |
| `--teal-tint`| `#EAF3F4`  | ESG section background         |

### Fonts (Google Fonts CDN)

- **Hanken Grotesk** — body text (400 / 500 / 600 / 700)
- **Bricolage Grotesque** — headings (700 / 800)
- **Space Mono** — labels, eyebrows, step numbers (400 / 700)

---

## Contact form backend

The contact form on `contact.html` POSTs to `https://saramdigitech.com/contact.php`.
That PHP file lives on Hostinger (`public_html/contact.php`) — **it is not in this repo**.

If the Hostinger domain is not yet live, form submissions fail silently and show an
inline error message. The form still works on the GitHub Pages URL once the PHP
endpoint is reachable.

`contact.php` on Hostinger:
- Validates name, email, message fields
- Sets CORS headers for `esg-triage.github.io` and `saramdigitech.com`
- Sends email to `info@saramdigitech.com` via `mail()`
- Returns `{"ok": true}` on success or `{"ok": false, "error": "..."}` on failure

---

## Development workflow

All changes go through a branch → PR → merge workflow.

```bash
# 1. Create a branch
git checkout main && git pull
git checkout -b feat/your-change

# 2. Edit HTML / CSS files

# 3. Commit
git add <files>
git commit -m "describe the change"

# 4. Push and open a PR
git push origin feat/your-change
gh pr create --base main --title "Brief title"

# 5. Merge the PR → live within ~30 seconds
```

### Testing locally

Open any `.html` file directly in a browser — no server needed. The site uses
relative paths (`assets/site.css`, `assets/logo.png`) so navigation between pages
works from the filesystem.

For the contact form to work locally you need the Hostinger domain live (or use
a local PHP server).

---

## Navigation between pages

Each page links to the others using relative paths:

| From any page | Target          |
|---------------|-----------------|
| Logo click    | `index.html`    |
| SAP Ariba nav | `sap-ariba.html`|
| ESG nav       | `esg.html`      |
| Contact nav   | `contact.html`  |
| About section | `index.html#about` |
| Engagement    | `index.html#engagement` |

The Divisions nav item has a hover dropdown linking to both practice pages.

---

## Future: custom domain

To point `saramdigitech.com` to GitHub Pages:

1. Add a `CNAME` file at the repo root containing:
   ```
   saramdigitech.com
   ```
2. In Hostinger DNS, add A records pointing to GitHub Pages IPs:
   ```
   185.199.108.153
   185.199.109.153
   185.199.110.153
   185.199.111.153
   ```
   Or a CNAME record: `www` → `esg-triage.github.io`
3. In GitHub → Settings → Pages → Custom domain, enter `saramdigitech.com`
4. Enable "Enforce HTTPS"

Once the domain switches, update `contact.php` on Hostinger to also allow
`https://saramdigitech.com` as a CORS origin (it likely already does).

---

## Migration history

| PR | Description |
|----|-------------|
| #1 | Initial documentation |
| #2 | Saram Digitech website redesign (Draftcode bundle) |
| #3 | Fix sticky header and Divisions dropdown |
| #4 | Click-based dropdown (closed — reverted in favour of CSS hover) |
| #5 | CSS `:hover` dropdown fix |
| #6 | Convert to multi-page site (current structure) |
