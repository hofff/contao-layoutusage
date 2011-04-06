<?php

$GLOBALS['TL_DCA']['tl_layout']['list']['operations'] = array_merge(
	array(
		'backboneit_layoutusage' => array(
			'label'               => &$GLOBALS['TL_LANG']['tl_layout']['backboneit_layoutusage'],
			'href'                => 'key=backboneit_layoutusage',
			'button_callback'     => array('LayoutUsageDCA', 'getUsageButton')
		)
	),
	$GLOBALS['TL_DCA']['tl_layout']['list']['operations']
);
