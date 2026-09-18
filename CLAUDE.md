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
- Entry points are globbed: `src/main.js` and `src/templates/*.{js,scss}`.
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

Available named slots: `head`, `default`, `foot`. The shell lives in `site/snippets/page-structure.php`.

`site/templates/default.php` is the scaffold to copy — it names the slots in a comment and fills only `default`, so there is no boilerplate to delete. `site/templates/home.php` is the worked example showing `.container` and a `.writer-field`.

### Grid system

The CSS grid uses `.kb-grid` (24-column) with fraction utility classes:

```html
<!-- Full width → half at md → third at lg -->
<div class="col-1-1 col-1-2-md col-1-3-lg">
```

Fractions: `1-1`, `3-4`, `2-3`, `1-2`, `1-3`, `1-4`, `1-6`, `1-8`, `1-12` — with optional breakpoint suffixes `-xs/-sm/-md/-lg/-xl`.

When rendering Kirby layout fields, use `kbGridClasses($layout)` (from `site/plugins/kb-helpers`), which returns one class string per column. It maps a **whole layout row** at once, not each column separately — a single fraction has no fixed responsive behaviour (`1/6` wants to be half width on a phone in a six-column row, but full width in `1/6, 2/3, 1/6`), so per-column mapping cannot keep a row adding up to 1 at every breakpoint.

The table is `KB_LAYOUT_CLASSES` in `lib/kbGridClasses.php`, keyed by the column widths joined with `,` exactly as Kirby stores them (it does not reduce `2/4` to `1/2`). It must stay in sync with `layouts:` in `site/blueprints/fields/layout.yml` and `$fractions` in `src/assets/scss/utilities/grid.scss`. Adding a layout to the blueprint without adding it here stacks the columns full width and raises a notice in debug mode.

### SCSS structure (`src/assets/scss/`)

- `abstracts/` — breakpoints (`$breakpoints` map), mixins, type scale
- `base/` — reset, typography, links, font-face
- `utilities/` — CSS variables, grid utilities, general utility classes
- `components/` — images, writer-field
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
- `files/` — image (alt + caption), PDF, SVG
- `tabs/` — shared `settings` and `media` tab partials
- `sections/` — shared panel sections (e.g. user-info)
- `users/` — default role

### Plugins

- `site/plugins/kirby-vite` — Vite/Kirby integration (exposes `vite()` helper)
- `site/plugins/kb-helpers` — project utilities; `index.php` requires every file in `lib/`, so a new helper dropped there is globally available with no registration

## Images

All images render through `site/snippets/components/image.php`. It is the only place that knows about srcset, sizes, lazy loading and intrinsic dimensions — use it everywhere so images behave consistently:

```php
<?php snippet('components/image', [
    'file'    => $page->image(),
    'ratio'   => '16/9',   // optional; sets the box
    'crop'    => true,     // true = fill the box, false = fit inside it
    'sizes'   => '(min-width: 48rem) 50vw, 100vw',
    'loading' => 'eager',  // for anything above the fold
]) ?>
```

It always emits `width`/`height`, so the browser reserves space and the layout doesn't shift — no aspect-ratio CSS or JS needed. srcset widths default to `kb.image.widths` in `site/config/config.php`. SVGs and external URLs skip thumb generation.

`site/snippets/blocks/image.php` handles only what is block-specific (ratio, crop, link, caption) and delegates the `<img>` to that snippet. The ratio reaches CSS as an inline `--ratio` custom property, consumed by `figure[style*="--ratio"]` in `components/images.scss`.

## Fonts

Font files go in `src/assets/fonts/` and are referenced from `src/assets/scss/base/font-face.scss` using the `@` alias (`url('@/assets/fonts/…')`). Vite runs them through its asset pipeline — hashed into `public/dist/assets/` with the `url()` rewritten. An absolute `/assets/fonts/…` path is passed through untouched and will 404.
