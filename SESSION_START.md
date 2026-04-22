# SESSION START — Good Assumptions Content Workflow
*For Claude — read this at the top of any session involving GA content work.*

---

## What this repo is

Good Assumptions is the framework brand and educational/credibility engine for
[SAEO.ai](https://saeo.ai). This repo holds:

1. The WordPress child theme (`/theme/`)
2. The build-ready content output (`/content/`) of an authoring workflow that
   lives in **Supabase** (`cms` schema, project `vfnspmpwcmgrcditcwpr`)
3. Canonical visual reference (`/design/`)
4. Exploration mockups (`/ideas/`)

The repo is the **build surface**. Authoring happens in Supabase. When a piece
moves to `status='locked'` in `cms.ga_content`, it gets promoted here as a
markdown file under `/content/`.

---

## Session-start queries

Run these at the top of any session to load workflow state.

### 1. Load all locked decisions

```sql
SELECT category, key, value, rationale
FROM cms.ga_locks
ORDER BY category, key;
```

These are the calls that drive every piece. Voice, mix, reader, vocab, IA,
cadence, visual, process, positioning. Read these first; never re-litigate
a lock unless explicitly asked.

### 2. Current content state

```sql
SELECT slug, kind, status, title, version, updated_at
FROM cms.ga_content
ORDER BY
  CASE status
    WHEN 'review'    THEN 1
    WHEN 'draft'     THEN 2
    WHEN 'locked'    THEN 3
    WHEN 'published' THEN 4
  END,
  updated_at DESC;
```

Tells you what's in flight (review/draft) vs. what's done (locked/published).

### 3. Pull a specific piece

```sql
SELECT slug, kind, status, title, url_path, copy_md, meta, decisions, notes, version
FROM cms.ga_content
WHERE slug = '[slug]';
```

---

## Workflow lifecycle for a single piece

```
   ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌──────────────┐
   │  draft  │───▶│ review  │───▶│ locked  │───▶│  published   │
   └─────────┘    └─────────┘    └─────────┘    └──────────────┘
        │              │              │                │
   brief +        full draft     copy_md       /content/[kind]/
   structure      in copy_md     frozen,       [slug].md
   stored in     status moves    promoted      lives here in repo,
   cms.ga_       to 'review'     to GitHub     WP build consumes
   content
```

| Status | Lives where | Editable by |
|---|---|---|
| `draft` | Supabase only | Claude + Dwayne |
| `review` | Supabase only | Claude (revisions on Dwayne feedback) |
| `locked` | Supabase + GitHub `/content/` | No one (frozen — bump `version` to revise) |
| `published` | Supabase + GitHub + live site | Same as locked |

---

## Schema cheat sheet

### `cms.ga_content` (per-piece content)

| column | what it holds |
|---|---|
| `id` | uuid PK |
| `slug` | URL-safe identifier — `homepage`, `manifesto`, `four-layers` |
| `kind` | `page` \| `article` \| `framework-primer` \| `method-page` \| `system` |
| `status` | `draft` \| `review` \| `locked` \| `published` |
| `title` | display title |
| `url_path` | final URL — e.g. `/framework/four-layers/` |
| `copy_md` | markdown source of truth |
| `copy_html` | optional rendered HTML |
| `meta` | jsonb — eyebrow, ctas, byline, reading_time, related, tags |
| `decisions` | jsonb — snapshot of which `ga_locks` drove this piece |
| `notes` | working notes, redlines, todos (not for publication) |
| `version` | int — increments on every meaningful revision |

### `cms.ga_locks` (global decisions)

| column | what it holds |
|---|---|
| `id` | uuid PK |
| `category` | `voice` \| `mix` \| `reader` \| `vocab` \| `ia` \| `cadence` \| `visual` \| `process` \| `positioning` |
| `key` | short identifier within category |
| `value` | the locked decision in plain text |
| `rationale` | why we landed here |

Both tables have `created_at` / `updated_at` and an `updated_at` trigger.

---

## Lock promotion (the only "ship" step)

When a piece is ready to lock:

1. Update `cms.ga_content` row: `status='locked'`, `version` bumped, `updated_at` auto.
2. Write the file to GitHub at `/content/[kind]/[slug].md` using the
   `github-gahomepage` MCP connector. The file body is the `copy_md` field,
   optionally with frontmatter pulled from `meta`.
3. WordPress build picks up the file from `/content/` and renders it into
   the live site.

**Connector convention.** Each repo has its own MCP connector named
`github-[reponame]`. For this repo, use `github-gahomepage`. The generic
`github-write` connector does NOT have access to this repo. The generic
`github-read` connector does, but only for reads. Always prefer
`github-gahomepage` for any operation on this repo.

---

## Working group

Backlog and decision-log items related to GA content workflow are tagged
under working group **`WG_GA_Content`** in `zwhat_next` (the cross-platform
backlog table in the SAEO project). When surfacing a TODO from this work,
log it under that working group.

---

## Sibling repos / connectors

| Repo | Connector | Scope |
|---|---|---|
| `goodassumptions/GAHomePage` | `github-gahomepage` | This repo. GA site, theme, content. |
| `[saeo edge functions repo]` | `github-saeo-edge` | SAEO platform edge functions. |
| (general read across allowlisted repos) | `github-read` | Cross-repo reads. |

If a new repo gets added to the constellation, expect a new
`github-[reponame]` connector to be wired up alongside it.
