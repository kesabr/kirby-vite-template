<?php

use Kirby\Cms\App as Kirby;

// Register the plugin with Kirby
Kirby::plugin('kesabr/kb-helpers', [
    // Plugin options and extensions can be defined here
]);

// Every PHP file in lib/ is loaded automatically — drop a new helper in there and
// it is globally available, no registration needed.
foreach (glob(__DIR__ . '/lib/*.php') as $helper) {
    require_once $helper;
}
