<?php

/**
 * Image block.
 *
 * Only handles what is specific to a block — ratio, crop, link, caption.
 * The <img> itself (srcset, sizes, lazy loading, dimensions) comes from
 * `components/image`, so block images behave like every other image on the site.
 *
 * @var \Kirby\Cms\Block $block
 */

use Kirby\Toolkit\Str;

$caption = $block->caption();
$link    = $block->link();
$crop    = $block->crop()->isTrue();
$ratio   = $block->ratio()->or('auto')->value();

$file = null;
$src  = null;

if ($block->location() == 'web') {
    $src = $block->src()->value();
} else {
    $file = $block->image()->toFile();
}

$hasRatio = $ratio !== 'auto';

?>
<?php if ($file || $src) : ?>
<figure class="image"<?= $hasRatio
    ? ' style="--ratio: ' . esc($ratio, 'attr') . '" data-crop="' . ($crop ? 'true' : 'false') . '"'
    : '' ?>>

    <?php if ($link->isNotEmpty()) : ?>
        <a href="<?= Str::esc($link->toUrl()) ?>">
    <?php endif ?>

    <?php snippet('components/image', [
        'file'  => $file,
        'src'   => $src,
        'alt'   => $block->alt()->value(),
        'ratio' => $hasRatio ? $ratio : null,
        'crop'  => $crop,
    ]) ?>

    <?php if ($link->isNotEmpty()) : ?>
        </a>
    <?php endif ?>

    <?php if ($caption->isNotEmpty()) : ?>
        <figcaption>
            <?= $caption ?>
        </figcaption>
    <?php endif ?>

</figure>
<?php endif ?>
