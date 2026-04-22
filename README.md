# Good Assumptions

The site, the theme, and the content workflow for **goodassumptions.com**.

Good Assumptions is the framework brand and educational/credibility engine
for [SAEO.ai](https://saeo.ai). GA teaches the diagnostic — where a marketing
problem lives, the threshold moment where it activates, the bias driving it,
and the choice vector shaping the response. SAEO is the AI tool that runs the
diagnostic on real situations.

This repo holds the WordPress child theme and the build-ready content output
of an authoring workflow that lives in Supabase.

---

## Repo structure

```
GAHomePage/
├── README.md            ← this file
├── SESSION_START.md     ← session-start query for Claude / future contributors
│
├── theme/               ← the WordPress child theme (parent: Twenty Twenty-Four)
│   ├── style.css            CSS variables, design system, component styles
│   ├── theme.json           palette, typography, spacing, custom templates
│   ├── functions.php        enqueues, pattern registration, block styles
│   ├── ga-animations.js     reveal animations, nav active state, anchor sync
│   └── patterns/            block patterns registered with the theme
│       ├── article-cta.php
│       ├── concept-card.php
│       ├── entity-badge-row.php
│       ├── saeo-diagnostic-banner.php
│       └── section-header.php
│
├── content/             ← build-ready output from the authoring workflow
│   ├── pages/               framework, method, about, saeo, homepage
│   └── articles/            flagships, future pillars, case studies
│
├── design/              ← canonical visual reference (kept, not built from)
│   ├── design-system.html   the canonical design system mockup
│   └── GA_Brand_Design_Brief.docx
│
└── ideas/               ← exploration mockups, no longer authoritative
    └── …                seed material to mine from, not source of truth
```

---

## Authoring workflow

The repo is the **build surface**. Authoring happens in Supabase
(`cms` schema, project `vfnspmpwcmgrcditcwpr`). When a piece is locked,
it gets promoted to `/content/` here as the build-ready file that
WordPress consumes.

Three storage surfaces, one job each:

| Surface | Job |
|---|---|
| **Supabase** `cms.ga_content` + `cms.ga_locks` | Source of truth for in-progress work and locked decisions |
| **Inline previews** (chat, design-system tokens) | Visual sign-off before lock |
| **GitHub** `/content/` | Build-ready output that flows into WordPress |

Read `SESSION_START.md` for the exact queries that load the workflow state.

---

## Theme

WordPress child theme on Twenty Twenty-Four. Three custom font families
(DM Serif Display, DM Sans, DM Mono), two color registers (GA editorial
teal/gold/clay/parchment for teaching surfaces, SAEO mark colors
coral/teal for the SAEO handoff and product surfaces), and a dark
canvas as the default ground.

To deploy the theme, copy the contents of `/theme/` into your WP themes
folder as `wp-content/themes/good-assumptions/`. Make sure the parent
theme (`twentytwentyfour`) is also installed.
