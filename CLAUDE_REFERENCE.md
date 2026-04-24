# Good Assumptions — Claude Reference

*Stable reference for any Claude session touching GA projects. Read alongside SESSION_START.md for content workflow specifics.*

---

## How to Start a New Claude Session on GA Work

### Option A — Copy-paste this exact prompt to open any GA session:

```
You are working on the Good Assumptions project.

Before we start, please do the following in order:

1. Use the github-gahomepage MCP connector to read the file CLAUDE_REFERENCE.md from the repo goodassumptions/GAHomePage (branch: main). This is your stack and role reference.

2. Use the github-gahomepage MCP connector to read SESSION_START.md from the same repo. This is your content workflow reference.

3. Use the Supabase MCP connector to run these two queries against project vfnspmpwcmgrcditcwpr:

   -- Locked decisions (read these first, never re-litigate):
   SELECT category, key, value, rationale FROM cms.ga_locks ORDER BY category, key;

   -- Current content state:
   SELECT slug, kind, status, title, git_path, last_promoted_at, updated_at
   FROM cms.ga_content
   ORDER BY
     CASE status WHEN 'review' THEN 1 WHEN 'draft' THEN 2 WHEN 'locked' THEN 3 WHEN 'published' THEN 4 END,
     updated_at DESC;

4. Confirm what you loaded and then ask what we're working on today.
```

### Option B — Minimal prompt for theme/design-only sessions:

```
You are working on the Good Assumptions WordPress theme.

Read CLAUDE_REFERENCE.md from github repo goodassumptions/GAHomePage using the github-gahomepage MCP connector, then ask what we're working on.
```

---

## The Two Products

**Good Assumptions** (`goodassumptions.com`) — Behavioral marketing education and content brand. Credibility engine and publishing arm for SAEO. GEO-first content strategy.

**SAEO.ai** (`saeo.ai`) — Behavioral marketing intelligence platform. Problem localization engine (Strategy / Application / Execution / Operations layers) and collision intelligence system.

Good Assumptions is the public-facing brand. SAEO is the product it supports.

---

## Live Stack

| Layer | What | Where |
|---|---|---|
| Site endpoint | WordPress.com | goodassumptions.com |
| Theme | Child theme on Twenty Twenty-Four | WordPress.com admin |
| Theme source | GAHomePage repo `/theme/` | github.com/goodassumptions/GAHomePage |
| Content pipeline | Supabase `cms` schema | Project `vfnspmpwcmgrcditcwpr` |
| Content output | Markdown files | GAHomePage repo `/content/` |

---

## Abandoned Explorations — Do Not Reference

- **Payload CMS** (`payload-website-starter` repo) — explored, abandoned
- **Wix** — explored, abandoned
- **Directus** — explored, abandoned

WordPress.com is the canonical publishing endpoint. Do not suggest switching.

---

## GitHub Repos + MCP Connectors

| Repo | Purpose | MCP Connector |
|---|---|---|
| `goodassumptions/GAHomePage` | Theme files + promoted content | `github-gahomepage` |
| `goodassumptions/saeo-edge` | SAEO edge functions | `github-saeo-edge` |
| Cross-repo reads only | Read-only | `github-read` |

**Always use `github-gahomepage` for GAHomePage reads and writes.** The generic `github-write` connector does not have access to this repo.

---

## Theme File Map

All theme files live in `GAHomePage/theme/`. These are the canonical source — changes here get copy-pasted into WordPress.com admin.

| File | Purpose |
|---|---|
| `theme/style.css` | All custom CSS — design tokens, component styles, animations |
| `theme/theme.json` | Block editor settings — typography, colors, spacing scale |
| `theme/functions.php` | PHP — enqueues, custom blocks, GA-specific hooks |
| `theme/ga-animations.js` | Scroll and interaction animations |
| `theme/patterns/` | Block patterns registered for the editor |
| `theme/parts/` | Template parts (header, footer) |
| `theme/templates/` | Full page templates |

### How theme changes work

1. Claude edits the file in `GAHomePage/theme/` via `github-gahomepage`
2. Dwayne copies the changed file content into WordPress.com admin (Appearance → Theme File Editor, or via the child theme upload)
3. Live site updates immediately — no build step

---

## Design System

- **Primary green:** `#7FE042` (GA monogram lime/chartreuse)
- **Background:** Dark navy near-black (`#0a0f1e` approx)
- **Typography:** Bold sans-serif, high contrast
- **Parent theme:** Twenty Twenty-Four
- Design tokens defined in `theme/theme.json` as CSS custom properties, extended in `theme/style.css`

---

## Content Workflow (summary — full detail in SESSION_START.md)

```
draft → review → locked → published
```

| Status | Body canonical where | Edit how |
|---|---|---|
| `draft` | Supabase `cms.ga_content.copy_md` | Claude + Dwayne in chat |
| `review` | Supabase `cms.ga_content.copy_md` | Claude revisions on feedback |
| `locked` | GitHub `GAHomePage/content/[kind]/[slug].md` | Git only |
| `published` | GitHub `GAHomePage/content/[kind]/[slug].md` | Git only |

Lock is a one-way door. Once promoted to GitHub, Supabase row is historical record only.

Key tables: `cms.ga_content`, `cms.ga_locks`

---

## Claude's Role

- Design and content editor for the WordPress site
- Build assistant for the GA theme (`style.css`, `theme.json`, `functions.php`, patterns)
- Content pipeline operator (Supabase → GitHub promotion workflow)
- GEO-first content strategy partner

Not a Wix editor. Not a Payload CMS assistant. Not an infrastructure engineer.

---

## Supabase Project

- **Project ref:** `vfnspmpwcmgrcditcwpr`
- **Schema:** `cms`
- **Key tables:** `cms.ga_content`, `cms.ga_locks`
- **MCP connector:** Supabase (connected in Claude.ai)
- **DB connection (if needed directly):** Transaction pooler `aws-0-us-east-1.pooler.supabase.com:6543`
