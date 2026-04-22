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

---

## The model in one paragraph

Pre-lock, Supabase is canonical. Drafts and reviews live in `cms.ga_content`.
When a piece is ready, it locks and promotes to GitHub `/content/`. After
promotion, **GitHub is canonical for the body** — future revisions happen by
editing the markdown file directly in git, not by re-editing the Supabase row.
Git history is the version history; there is no manual version integer.
Supabase remains canonical forever for cross-cutting decisions (`cms.ga_locks`)
and for the historical drafting record of every piece.

---

## Session-start queries

### 1. Load all locked decisions

```sql
SELECT category, key, value, rationale
FROM cms.ga_locks
ORDER BY category, key;
```

These are the calls that drive every piece — voice, mix, reader, vocab, IA,
cadence, visual, process, positioning. Read first. Never re-litigate a lock
unless explicitly asked to.

### 2. Current content state

```sql
SELECT slug, kind, status, title, git_path, last_promoted_at, updated_at
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

`git_path` and `last_promoted_at` tell you which pieces have been promoted to
GitHub and where they live. `null` in these fields = Supabase-only (pre-lock).

### 3. Detect drift between Supabase and GitHub

```sql
SELECT slug, status, updated_at, last_promoted_at,
       (updated_at > last_promoted_at) AS supabase_ahead
FROM cms.ga_content
WHERE last_promoted_at IS NOT NULL
  AND updated_at > last_promoted_at;
```

A row showing up here means Supabase has changes that haven't made it to git.
Either re-promote, or — if the changes are stale and git is now canonical —
revert the Supabase row to match git, since git is the source of truth post-lock.

### 4. Pull a specific piece

```sql
SELECT slug, kind, status, title, url_path, copy_md, meta, decisions, notes,
       git_path, last_promoted_at, last_promoted_sha
FROM cms.ga_content
WHERE slug = '[slug]';
```

---

## Workflow lifecycle

```
   ┌─────────┐    ┌─────────┐    ┌──────────┐    ┌───────────────┐
   │  draft  │───▶│ review  │───▶│  locked  │───▶│   published   │
   └─────────┘    └─────────┘    └──────────┘    └───────────────┘
        │              │              │                   │
   brief +        full draft     promote to         site is live;
   structure      in copy_md     GitHub at          git history is
   stored in     status moves    content/[kind]/    the version
   cms.ga_       to 'review'     [slug].md          history from
   content                       record git_path,   here on
                                 last_promoted_at,
                                 last_promoted_sha
                                 back to Supabase
```

| Status | Body lives where (canonically) | Editable by |
|---|---|---|
| `draft` | Supabase `copy_md` | Claude + Dwayne in chat |
| `review` | Supabase `copy_md` | Claude (revisions on Dwayne feedback) |
| `locked` | **GitHub** `/content/[kind]/[slug].md` | git only |
| `published` | **GitHub** `/content/[kind]/[slug].md` | git only |

The lock is a one-way door. Once a piece is locked and promoted, **the
Supabase row becomes a historical record**. Edits go to git.

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
| `copy_md` | markdown source — canonical pre-lock, historical post-lock |
| `copy_html` | optional rendered HTML |
| `meta` | jsonb — eyebrow, ctas, byline, reading_time, related, tags |
| `decisions` | jsonb — snapshot of which `ga_locks` drove this piece |
| `notes` | working notes, redlines, todos (not for publication) |
| `git_path` | path in GAHomePage repo where the piece was promoted |
| `last_promoted_at` | when the row was last written to GitHub |
| `last_promoted_sha` | commit SHA of the most recent promotion |

No `version` column — git tracks revisions.

### `cms.ga_locks` (global decisions — always canonical in Supabase)

| column | what it holds |
|---|---|
| `id` | uuid PK |
| `category` | `voice` \| `mix` \| `reader` \| `vocab` \| `ia` \| `cadence` \| `visual` \| `process` \| `positioning` |
| `key` | short identifier within category |
| `value` | the locked decision in plain text |
| `rationale` | why we landed here |

Both tables have `created_at` / `updated_at` and an `updated_at` trigger.

---

## Lock + promote (the one ship step)

When a piece is ready to lock:

```sql
-- 1. Lock the row
UPDATE cms.ga_content
   SET status = 'locked',
       updated_at = now()
 WHERE slug = '[slug]';
```

```
2. Write file to GitHub via the github-gahomepage MCP connector:
   path:    content/[kind]/[slug].md
   body:    YAML frontmatter from meta + copy_md from the row
   commit:  "content: promote [slug] from cms.ga_content (status=locked)"

3. Capture the response — file SHA and commit SHA.
```

```sql
-- 4. Record the bridge back to Supabase
UPDATE cms.ga_content
   SET git_path          = 'content/[kind]/[slug].md',
       last_promoted_at  = now(),
       last_promoted_sha = '[commit sha from step 3]'
 WHERE slug = '[slug]';
```

After this, the piece is canonically in GitHub. Future revisions happen there.

---

## Connector convention

Each repo has its own MCP connector named `github-[reponame]`.
For this repo: **`github-gahomepage`**.

The generic `github-write` connector does **not** have access to this repo.
The generic `github-read` connector works for cross-repo reads only. Always
prefer `github-gahomepage` for both reads and writes on this repo.

| Repo | Connector |
|---|---|
| `goodassumptions/GAHomePage` | `github-gahomepage` |
| SAEO edge functions | `github-saeo-edge` |
| (cross-repo reads) | `github-read` |

New repos in the constellation get their own `github-[reponame]` connector.

---

## Working group

Backlog and decision-log items related to GA content workflow are tagged
under working group **`WG_GA_Content`** in the cross-platform `zwhat_next`
backlog table.
