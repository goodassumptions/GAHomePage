# Good Assumptions — Claude Reference

*Stable reference for any Claude session touching GA projects. Read alongside SESSION_START.md for content workflow specifics.*

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

## Abandoned Explorations (do not reference)

- **Payload CMS** (`payload-website-starter` repo) — explored, abandoned
- **Wix** — explored, abandoned
- **Directus** — explored, abandoned

WordPress.com is the canonical publishing endpoint going forward. Do not suggest switching.

---

## GitHub Repos + MCP Connectors

| Repo | Purpose | MCP Connector |
|---|---|---|
| `goodassumptions/GAHomePage` | Theme files + promoted content | `github-gahomepage` |
| `goodassumptions/saeo-edge` | SAEO edge functions | `github-saeo-edge` |
| Cross-repo reads | Read-only access | `github-read` |

**Always use `github-gahomepage` for GAHomePage reads and writes.** The generic `github-write` connector does not have access to this repo.

---

## Theme File Map

All theme files live in `GAHomePage/theme/`. These are the canonical source — changes here get copy-pasted into WordPress.com admin.

| File | Purpose |
|---|---|
| `style.css` | All custom CSS — design tokens, component styles, animations |
| `theme.json` | Block editor settings — typography, colors, spacing scale |
| `functions.php` | PHP — enqueues, custom blocks, GA-specific hooks |
| `ga-animations.js` | Scroll and interaction animations |
| `patterns/` | Block patterns registered for the editor |
| `parts/` | Template parts (header, footer) |
| `templates/` | Full page templates |

---

## Design System Basics

- **Primary color:** Lime/chartreuse green `#7FE042` (GA monogram color)
- **Background:** Dark navy/near-black
- **Typography:** Bold sans-serif, high contrast
- **Parent theme:** Twenty Twenty-Four
- Design tokens live in `theme.json` and as CSS custom properties in `style.css`

---

## Content Workflow (summary — full detail in SESSION_START.md)

```
draft → review → locked → published
```

- **Pre-lock:** Supabase `cms.ga_content` is canonical. Body in `copy_md`.
- **On lock:** Promote markdown to `GAHomePage/content/[kind]/[slug].md` via `github-gahomepage`. Record `git_path`, `last_promoted_at`, `last_promoted_sha` back to Supabase.
- **Post-lock:** GitHub is canonical for the body. Supabase row becomes historical record.

Key tables: `cms.ga_content`, `cms.ga_locks`

---

## Claude's Role

- Design and content editor for the WordPress site
- Build assistant for the GA theme (style.css, theme.json, functions.php, patterns)
- Content pipeline operator (Supabase → GitHub promotion workflow)
- GEO-first content strategy partner

Not a Wix editor. Not a Payload CMS assistant. Not an infrastructure engineer (Supabase is already set up and stable).

---

## Session Start Checklist

1. Read `SESSION_START.md` (this repo root) for content workflow state
2. Run session-start queries to load `cms.ga_locks` and `cms.ga_content` state
3. Check `cms.ga_content` for any `supabase_ahead` drift
4. Proceed with the task

---

## Supabase Project

- **Project ref:** `vfnspmpwcmgrcditcwpr`
- **Schema:** `cms`
- **Key tables:** `ga_content`, `ga_locks`
- MCP connector: Supabase MCP (connected)
