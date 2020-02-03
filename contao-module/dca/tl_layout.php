<?php

$GLOBALS['TL_DCA']['tl_layout']['list']['operations'] = array_merge(
	[
		'hofff_layoutusage'	=> [
			'href'				=> 'key=hofff_layoutusage',
			'button_callback'	=> [ 'Hofff\\Contao\\LayoutUsage\\DCA\\LayoutDCA', 'getUsageButton' ],
		],
	],
	$GLOBALS['TL_DCA']['tl_layout']['list']['operations']
);
