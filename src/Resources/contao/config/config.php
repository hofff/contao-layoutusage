<?php

declare(strict_types=1);

use Hofff\Contao\LayoutUsage\Backend\ModuleLayoutUsage;

$GLOBALS['BE_MOD']['design']['themes']['hofff_layoutusage'] = [ModuleLayoutUsage::class, 'generateFromDC'];
