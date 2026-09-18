<?php
/* MAIN SNIPPET * must be loaded in every template */
snippet('page-structure', slots: true)
?>

<?php slot('default') ?>

<div class="container">
    <div class="m-bottom-md writer-field">
        <?= $page->text() ?>
    </div>

    <a class="link" href="/">
        ← Back to Home
    </a>
</div>

<?php endslot() ?>

<?php endsnippet() ?>