<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Backboneit_layoutusage
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'BEModuleLayoutUsage' => 'system/modules/backboneit_layoutusage/BEModuleLayoutUsage.php',
	'LayoutUsageDCA'      => 'system/modules/backboneit_layoutusage/LayoutUsageDCA.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_backboneit_layoutusage' => 'system/modules/backboneit_layoutusage/templates',
));
