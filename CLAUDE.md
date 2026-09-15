# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Install dependencies (first-time setup)
composer install
npm install

# Development (starts PHP server on :8888 + Vite dev server on :5173)
npm run dev

# Production build (outputs to public/dist)
npm run build

# Local production preview
npm run preview
```

Visit `localhost:8888` during development. Vite's dev server is for assets only.

## Architecture

This is a **Kirby CMS 5 + Vite 5** multi-page starter using a public folder structure (webroot is `public/`).

### Directory layout

| Path | Purpose |
|---|---|
| `public/` | Webroot — `index.php` entry, `.htaccess`, built assets in `public/dist/` |
| `site/` | Kirby application: templates, snippets, blueprints, plugins, config |
| `src/` | Frontend source — JS and SCSS compiled by Vite |
| `kirby/` | Kirby core (vendored, don't edit) |
| `content/` | Flat-file content (excluded from git) |
| `storage/` | Sessions and cache (excluded from git) |

### Vite & asset loading

- `vite.config.js` uses `vite-plugin-kirby` with `src/` as root and `public/dist` as output.
- Entry points are globbed: `src/main.js`, `src/scss/main.scss`, `src/templates/*.{js,scss}`, `src/scss/templates/*.scss`.
- `site/config/vite.config.php` is **auto-generated** — never edit it manually.
- In templates, `page-structure.php` automatically loads shared assets (`main.js`) and tries to load a template-specific bundle (e.g. `templates/home.js`) using `vite()->js(..., try: true)`.
- To add a new template's JS/CSS, create `src/templates/<name>.js` (and optionally `src/assets/scss/<name>.scss` imported from it).

### Page shell (slots)

Every Kirby template wraps content in the shared shell:

```php
<?php snippet('page-structure', slots: true) ?>
<?php slot('default') ?> ... <?php endslot() ?>
<?php endsnippet() ?>
```

Available named slots: `head`, `header`, `default`, `footer`, `foot`. The shell lives in `site/snippets/page-structure.php`.

### Grid system

The CSS grid uses `.kb-grid` (24-column) with fraction utility classes:

```html
<!-- Full width → half at md → third at lg -->
<div class="col-1-1 col-1-2-md col-1-3-lg">
```

Fractions: `1-1`, `3-4`, `2-3`, `1-2`, `1-3`, `1-4`, `1-6`, `1-8`, `1-12` — with optional breakpoint suffixes `-xs/-sm/-md/-lg/-xl`.

When rendering Kirby layout fields, use `kbResponsiveClassesFromFraction($fraction)` (from `site/plugins/kb-helpers`) to convert Kirby's `"1/3"` notation to the matching responsive CSS classes. The breakpoint denominator mapping is defined in `KB_GRID_BP_MAP` and must stay in sync with `$fractions` in `src/assets/scss/utilities/grid.scss`.

### SCSS structure (`src/assets/scss/`)

- `abstracts/` — breakpoints (`$breakpoints` map), mixins, type scale
- `base/` — reset, typography, links, font-face
- `utilities/` — CSS variables, grid utilities, general utility classes
- `components/` — arrows, buttons, gallery, images, writer-field
- `layout/` — global defaults, header, footer shells
- `main.scss` — barrel file that `@forward`s everything above
- `home.scss` (and other template files) — template-specific styles, imported from `src/templates/<name>.js`

Breakpoints use `width >` (min-width equivalent). All breakpoint keys (`xs sm md lg xl`) must match between `abstracts/breakpoints.scss` and `utilities/grid.scss`.

### Kirby config

- `site/config/config.php` — production defaults (`debug: false`, panel CSS path)
- `site/config/config.localhost.php` — local overrides (`debug: true`); additional environment files follow the pattern `config.<domain>.php`

### Blueprints

Organized by type under `site/blueprints/`:
- `fields/` — reusable field definitions (writer, layout, alignment, mobile-display, site-icon)
- `pages/` — default, error, legal page schemas
- `files/` — image, PDF, SVG with required alt/caption metadata
- `tabs/` — shared `settings` and `media` tab partials
- `sections/` — shared panel sections (e.g. user-info)
- `users/` — admin, editor, default role permissions

### Plugins

- `site/plugins/kirby-vite` — Vite/Kirby integration (exposes `vite()` helper)
- `site/plugins/kb-helpers` — project utilities; add new helpers to `lib/` and they are auto-available globally
