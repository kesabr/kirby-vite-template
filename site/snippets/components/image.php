<?php

/**
 * Responsive image.
 *
 * The single place that knows about srcset, sizes, lazy loading and intrinsic
 * dimensions. Use it for every image on the site — image blocks, plain image
 * fields, hero images — so they all behave the same way.
 *
 *   snippet('components/image', ['file' => $page->image()])
 *   snippet('components/image', [
 *       'file'    => $file,
 *       'ratio'   => '16/9',   // sets the box; combine with crop
 *       'crop'    => true,     // true = fill the box, false = fit inside it
 *       'sizes'   => '(min-width: 48rem) 50vw, 100vw',
 *       'loading' => 'eager',  // for anything above the fold
 *   ])
 *
 * Always emits width/height, so the browser reserves the space before the image
 * loads. That is what keeps the layout from shifting — no CSS or JS needed.
 *
 * @var \Kirby\Cms\File|null $file
 */

// Type check rather than `?? null`: Kirby renders snippets through
// F::loadIsolated(string $file, array $data), so when the caller passes no
// `file` key, `$file` is still in scope — bound to this snippet's own path.
$file    = ($file ?? null) instanceof \Kirby\Cms\File ? $file : null;
$src     = $src     ?? null;   // external URL, when there is no Kirby file
$alt     = $alt     ?? null;
$ratio   = $ratio   ?? null;
$crop    = $crop    ?? true;
$sizes   = $sizes   ?? '100vw';
$widths  = $widths  ?? option('kb.image.widths', [400, 800, 1200, 1600, 2000]);
$loading = $loading ?? 'lazy';
$class   = $class   ?? null;

// Parse "16/9" once. Anything else is treated as no ratio.
$ratioW = $ratioH = null;

if ($ratio && preg_match('!^\s*(\d+)\s*/\s*(\d+)\s*$!', (string) $ratio, $matches)) {
    $ratioW = (int) $matches[1];
    $ratioH = (int) $matches[2];
}

// Cast first: an empty writer/text field stringifies to '', so it falls back
// to the file's own alt text rather than counting as "alt was provided".
$altText = (string) ($alt ?? '');

if ($altText === '' && $file) {
    $altText = (string) $file->alt();
}

// SVGs and external URLs can't be resized — emit them as they are.
$resizable = $file && $file->isResizable();

?>
<?php if ($resizable) : ?>
    <?php

    // One thumb per requested width, never upscaling past the original.
    $thumbs = [];

    foreach ($widths as $width) {
        if ($file->width() && $width > $file->width()) {
            continue;
        }

        $options = ['width' => $width];

        // Only crop the thumbs themselves when the image should fill the box.
        if ($ratioW && $crop) {
            $options['height'] = (int) round($width * $ratioH / $ratioW);
            $options['crop']   = true;
        }

        $thumbs[$width . 'w'] = $options;
    }

    // Original smaller than every step in the list.
    if (empty($thumbs)) {
        $thumbs[$file->width() . 'w'] = ['width' => $file->width()];
    }

    $largest = max(array_map('intval', array_keys($thumbs)));
    $default = $file->thumb($thumbs[$largest . 'w']);

    // With a ratio the box is the ratio, cropped or not — that's what gets reserved.
    $boxWidth  = $ratioW ? $largest : $default->width();
    $boxHeight = $ratioW ? (int) round($largest * $ratioH / $ratioW) : $default->height();

    ?>
    <img
        src="<?= $default->url() ?>"
        srcset="<?= $file->srcset($thumbs) ?>"
        sizes="<?= esc($sizes, 'attr') ?>"
        width="<?= $boxWidth ?>"
        height="<?= $boxHeight ?>"
        alt="<?= esc($altText, 'attr') ?>"
        loading="<?= esc($loading, 'attr') ?>"
        decoding="async"
        <?= $class ? 'class="' . esc($class, 'attr') . '"' : '' ?>>

<?php elseif ($file) : ?>
    <img
        src="<?= $file->url() ?>"
        <?= $file->width() ? 'width="' . $file->width() . '" height="' . $file->height() . '"' : '' ?>
        alt="<?= esc($altText, 'attr') ?>"
        loading="<?= esc($loading, 'attr') ?>"
        decoding="async"
        <?= $class ? 'class="' . esc($class, 'attr') . '"' : '' ?>>

<?php elseif ($src) : ?>
    <img
        src="<?= esc($src, 'attr') ?>"
        alt="<?= esc($altText, 'attr') ?>"
        loading="<?= esc($loading, 'attr') ?>"
        decoding="async"
        <?= $class ? 'class="' . esc($class, 'attr') . '"' : '' ?>>
<?php endif ?>
