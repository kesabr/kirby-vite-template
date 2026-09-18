<?php
/* MAIN SNIPPET * must be loaded in every template */
snippet('page-structure', slots: true)
?>

<?php slot('default') ?>

<div class="container">
    <h1 class="m-bottom-md"><?= $page->title() ?></h1>

    <div class="writer-field">
        <?= $page->text() ?>
    </div>
</div>

<?php endslot() ?>

<?php endsnippet() ?>