# Hosting Setup — saramdigitech.com

## Overview

The site was migrated from a WordPress/Elementor installation on Hostinger to a
static HTML site hosted on GitHub Pages. No PHP or database is involved.

## How it works

```
Edit code locally → git push (via PR) → GitHub Pages auto-deploys → live site
```

The GitHub repo holds plain HTML/CSS/JS/images. GitHub Pages serves those files
directly as a public website.

**Live URL:** https://esg-triage.github.io/saram-website/  
**Custom domain (future):** saramdigitech.com  
**Repo:** https://github.com/esg-triage/saram-website

---

## What we did (one-time migration)

### 1. Crawled the live WordPress site
Used `wget` to download every page from `saramdigitech.com` as rendered HTML,
along with all referenced CSS, JS, fonts, and images.

```bash
wget --mirror --convert-links --adjust-extension --page-requisites --no-parent \
  -P site-crawl https://saramdigitech.com/
```

### 2. Cleaned the repo
Removed all WordPress PHP files that had been downloaded from Hostinger
(wp-config.php, wp-admin/, wp-includes/, etc.) — none of these are needed
for static hosting.

### 3. Committed the static files
The crawled HTML/CSS/JS/images were committed to the `main` branch.

Pages captured:
- `/` — Home
- `/about-us/` — About Us
- `/contact-us/` — Contact Us
- `/services/` — Services
- `/services/data-analytics/` — Data Analytics
- `/services/sap-ariba/` — SAP Ariba

### 4. Enabled GitHub Pages
```bash
gh api repos/esg-triage/saram-website/pages -X POST \
  --field 'source[branch]=main' \
  --field 'source[path]=/'
```

GitHub Pages detects `index.html` at the repo root and serves all files as a
static website with automatic HTTPS.

---

## Development workflow (ongoing)

All changes go through a branch → PR → merge workflow. Never push directly to `main`.

```bash
# 1. Create a branch for your change
git checkout -b your-branch-name

# 2. Make changes to HTML/CSS/JS files

# 3. Commit
git add <files>
git commit -m "describe what changed"

# 4. Push and open a PR
git push origin your-branch-name
gh pr create --base main --title "your title"

# 5. Merge the PR → site auto-updates within ~30 seconds
```

---

## Future: custom domain

To point `saramdigitech.com` to GitHub Pages:

1. Add a `CNAME` file to the repo root containing `saramdigitech.com`
2. In Hostinger DNS, add/update records:
   - `A` records pointing to GitHub Pages IPs:
     `185.199.108.153`, `185.199.109.153`, `185.199.110.153`, `185.199.111.153`
   - Or a `CNAME` record: `www` → `esg-triage.github.io`
3. In GitHub repo Settings → Pages → Custom domain, enter `saramdigitech.com`
4. Enable "Enforce HTTPS"
