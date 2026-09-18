<?php
declare(strict_types=1);

/**
 * Kirby layout -> kb-grid column classes
 *
 * Maps a whole layout row at once rather than each column on its own. That is
 * deliberate: a single fraction has no fixed responsive behaviour. `1/6` wants to
 * be half width on a phone in a six-column row, but full width in "1/6, 2/3, 1/6".
 * Mapping per column cannot keep a row adding up to 1 at every breakpoint — mapping
 * per layout can, and you can read the table and check it.
 *
 * Keys are the column widths of one layout, joined by ",", exactly as Kirby stores
 * them (it does not reduce "2/4" to "1/2", so both appear below).
 *
 * Keep in sync with:
 *   - `layouts:` in site/blueprints/fields/layout.yml
 *   - $fractions in src/assets/scss/utilities/grid.scss
 */
const KB_LAYOUT_CLASSES = [

    // one column
    '1/1' => ['col-1-1'],

    // halves
    '1/2,1/2' => [
        'col-1-1 col-1-2-sm',
        'col-1-1 col-1-2-sm',
    ],

    // thirds
    '1/3,1/3,1/3' => [
        'col-1-1 col-1-3-md',
        'col-1-1 col-1-3-md',
        'col-1-1 col-1-3-md',
    ],
    '1/3,2/3' => [
        'col-1-1 col-1-3-md',
        'col-1-1 col-2-3-md',
    ],
    '2/3,1/3' => [
        'col-1-1 col-2-3-md',
        'col-1-1 col-1-3-md',
    ],

    // quarters — two per row at sm, four at md
    '1/4,1/4,1/4,1/4' => [
        'col-1-1 col-1-2-sm col-1-4-md',
        'col-1-1 col-1-2-sm col-1-4-md',
        'col-1-1 col-1-2-sm col-1-4-md',
        'col-1-1 col-1-2-sm col-1-4-md',
    ],
    '1/4,1/2,1/4' => [
        'col-1-1 col-1-4-md',
        'col-1-1 col-1-2-md',
        'col-1-1 col-1-4-md',
    ],
    '1/4,1/4,2/4' => [
        'col-1-1 col-1-2-sm col-1-4-md',
        'col-1-1 col-1-2-sm col-1-4-md',
        'col-1-1 col-1-2-md',
    ],
    '1/2,1/4,1/4' => [
        'col-1-1 col-1-2-md',
        'col-1-1 col-1-2-sm col-1-4-md',
        'col-1-1 col-1-2-sm col-1-4-md',
    ],
    '3/4,1/4' => [
        'col-1-1 col-3-4-md',
        'col-1-1 col-1-4-md',
    ],
    '1/4,3/4' => [
        'col-1-1 col-1-4-md',
        'col-1-1 col-3-4-md',
    ],

    // sixths — two per row on mobile, three at sm, six at md
    '1/6,1/6,1/6,1/6,1/6,1/6' => [
        'col-1-2 col-1-3-sm col-1-6-md',
        'col-1-2 col-1-3-sm col-1-6-md',
        'col-1-2 col-1-3-sm col-1-6-md',
        'col-1-2 col-1-3-sm col-1-6-md',
        'col-1-2 col-1-3-sm col-1-6-md',
        'col-1-2 col-1-3-sm col-1-6-md',
    ],
    '1/6,2/3,1/6' => [
        'col-1-1 col-1-6-md',
        'col-1-1 col-2-3-md',
        'col-1-1 col-1-6-md',
    ],
    '5/6,1/6' => [
        'col-1-1 col-5-6-md',
        'col-1-1 col-1-6-md',
    ],
    '1/6,5/6' => [
        'col-1-1 col-1-6-md',
        'col-1-1 col-5-6-md',
    ],
];

/**
 * Return one class string per column of the given layout.
 *
 * Unknown layouts stack full width, which is always safe. In debug mode they also
 * raise a notice, so adding a layout to the blueprint without adding it here fails
 * loudly instead of silently rendering a stack.
 *
 * @return string[] one entry per column, in order
 */
function kbGridClasses(\Kirby\Cms\Layout $layout): array
{
    $columns = $layout->columns();

    $widths = [];
    foreach ($columns as $column) {
        $widths[] = (string) $column->width();
    }

    $signature = implode(',', $widths);

    if (isset(KB_LAYOUT_CLASSES[$signature])) {
        return KB_LAYOUT_CLASSES[$signature];
    }

    if (kirby()->option('debug') === true) {
        trigger_error(
            'kbGridClasses(): no entry for layout "' . $signature . '". '
            . 'Add it to KB_LAYOUT_CLASSES in '
            . 'site/plugins/kb-helpers/lib/kbGridClasses.php — '
            . 'columns are stacking full width in the meantime.',
            E_USER_NOTICE
        );
    }

    return array_fill(0, max(count($widths), 1), 'col-1-1');
}
