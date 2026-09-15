<?php

/** @var Kirby\Cms\Page $page */
/** @var Kirby\Cms\Site $site */
/** @var Kirby\Cms\Page $slots */
?>

<!DOCTYPE html>
<html lang="<?= $kirby->language() ? $kirby->language()->code() : 'en' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= $site->title() ?></title>

    <?php snippet('parts/favicons') ?>

    <?= vite()->js('main.js', ['defer' => true]) ?>
    <?= vite()->css('main.js') ?>

    <?= vite()->js("templates/{{ page.template }}.js", ['defer' => true], try: true) ?>
    <?= vite()->css("templates/{{ page.template }}.js", try: true) ?>

    <?php if ($head = $slots->head()) : ?>
        <?= $head ?>
    <?php endif ?>

</head>

<body>

    <header>
        <?= snippet('parts/header') ?>

        <?php if ($header = $slots->header()) : ?>
            <?= $header ?>
        <?php endif ?>
    </header>

    <main>
        <?php if ($main = $slots->default()) : ?>
            <?= $main ?>
        <?php endif ?>
    </main>

    <footer>
        <?= snippet('parts/footer') ?>
        <?php if ($footer = $slots->footer()) : ?>
            <?= $footer ?>
        <?php endif ?>
    </footer>

    <?php if ($foot = $slots->foot()) : ?>
        <?= $foot ?>
    <?php endif ?>

</body>

</html>