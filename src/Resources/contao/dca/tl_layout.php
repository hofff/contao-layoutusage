<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_layout']['list']['operations'] = array_merge(
    [
        'hofff_layoutusage_btn' => [
            'href' => 'key=hofff_layoutusage',
            'icon' => null,
        ],
    ],
    $GLOBALS['TL_DCA']['tl_layout']['list']['operations'] ?? [],
);
