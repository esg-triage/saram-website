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

## Custom domain setup

This section covers everything needed to point `saramdigitech.com` to GitHub Pages.

> **Before you start:** if the WordPress site is still live and serving real
> traffic, plan a maintenance window. DNS propagation can take up to 48 hours,
> during which the old site may be intermittently unreachable. If WordPress is
> already inactive, you can proceed immediately.

---

### Step 1 — Add a CNAME file to the repo

Create a file called `CNAME` at the repo root (no extension) containing exactly:

```
saramdigitech.com
```

Commit and push it to `main`. This tells GitHub Pages which custom domain to
serve the site on.

```bash
echo "saramdigitech.com" > CNAME
git add CNAME
git commit -m "chore: add custom domain CNAME"
git push origin main
```

---

### Step 2 — Update DNS on Hostinger

Log in to Hostinger → Domains → Manage → DNS / Nameservers.

You need **both** sets of records so the root domain and www both work:

**A records (root domain `saramdigitech.com`)**

Add four A records, each with host `@` and the following values:

| Type | Host | Points to         | TTL  |
|------|------|-------------------|------|
| A    | @    | 185.199.108.153   | 3600 |
| A    | @    | 185.199.109.153   | 3600 |
| A    | @    | 185.199.110.153   | 3600 |
| A    | @    | 185.199.111.153   | 3600 |

**CNAME record (www subdomain)**

| Type  | Host | Points to              | TTL  |
|-------|------|------------------------|------|
| CNAME | www  | esg-triage.github.io   | 3600 |

If a conflicting A record or CNAME for `@` or `www` already exists from the old
WordPress setup, delete or replace it with the values above.

---

### Step 3 — Configure GitHub Pages

1. Go to the repo on GitHub → **Settings** → **Pages**
2. Under **Custom domain**, enter `saramdigitech.com` and click **Save**
3. GitHub will run a DNS check — it may show a warning for a few minutes until
   DNS propagates
4. Once the check passes, tick **Enforce HTTPS**

After this, `https://saramdigitech.com` and `https://www.saramdigitech.com`
will both serve the GitHub Pages site.

---

### Step 4 — Update contact.php on Hostinger

The contact form POSTs to `https://saramdigitech.com/contact.php`. Once the
domain is live on GitHub Pages, the PHP file is no longer served from the root
— it needs to live on a subdomain or be accessed via its direct Hostinger URL.

**Recommended approach: use an `api.` subdomain**

1. In Hostinger DNS, add a CNAME for `api`:

   | Type  | Host | Points to                     | TTL  |
   |-------|------|-------------------------------|------|
   | CNAME | api  | `<your-hostinger-server>.com` | 3600 |

   (Check your Hostinger panel for the server hostname, e.g. `server123.web-hosting.com`)

2. Update `contact.html` in the repo — change the fetch URL from:
   ```js
   fetch('https://saramdigitech.com/contact.php', ...)
   ```
   to:
   ```js
   fetch('https://api.saramdigitech.com/contact.php', ...)
   ```

3. Update `$allowed_origins` in `contact.php` on Hostinger to include the new domain:
   ```php
   $allowed_origins = [
     'https://esg-triage.github.io',
     'https://saramdigitech.com',
     'https://www.saramdigitech.com',
   ];
   ```

---

### Verification checklist

After DNS propagates (allow up to 48 hours, usually faster):

- [ ] `https://saramdigitech.com` loads the site with a valid HTTPS certificate
- [ ] `https://www.saramdigitech.com` redirects to the root domain
- [ ] `https://esg-triage.github.io/saram-website/` still works (GitHub keeps the old URL active)
- [ ] Contact form submits successfully and email arrives at `info@saramdigitech.com`
- [ ] No mixed-content warnings in browser dev tools

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
