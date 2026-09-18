<?php

/**
 * Renders a layout field as kb-grid rows.
 *
 * snippet('components/layout')                        // uses $page->layout()
 * snippet('components/layout', ['layouts' => $field]) // any layout field
 */

$layouts ??= $page->layout();

?>

<div class="layout container">

  <?php foreach ($layouts->toLayouts() as $layout) : ?>

    <?php $classes = kbGridClasses($layout) ?>

    <div class="layout__row | kb-grid">
      <?php foreach ($layout->columns() as $index => $column) : ?>

        <div class="layout__column <?= $classes[$index] ?? 'col-1-1' ?>">

          <?php foreach ($column->blocks() as $block): ?>
            <div class="block-type-<?= $block->type() ?>">
              <?= $block ?>
            </div>
          <?php endforeach ?>

        </div>

      <?php endforeach ?>
    </div>

  <?php endforeach ?>

</div>
