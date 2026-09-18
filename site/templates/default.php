<?php
/* MAIN SNIPPET * must be loaded in every template */
/* Slots: head, default, foot — see site/snippets/page-structure.php */
snippet('page-structure', slots: true)
?>

<?php slot('default') ?>

<?php endslot() ?>

<?php endsnippet() ?>
