<?php

namespace Hofff\Contao\LayoutUsage\DCA;

use Contao\Backend;
use Contao\Database;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class LayoutDCA extends Backend {

	/**
	 * @param array $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 */
	public function getUsageButton($row, $href, $label, $title, $icon, $attributes) {
		$sql = 'SELECT COUNT(*) AS cnt FROM tl_page WHERE includeLayout = 1 AND layout = ?';
		$usage = Database::getInstance()->prepare($sql)->execute($row['id'])->cnt;

		return sprintf(
			'<a href="%s" title="%s"%s>(%s)</a> ',
			$this->addToUrl($href . '&id=' . $row['id']),
			sprintf($GLOBALS['TL_LANG']['tl_layout']['hofff_layoutusage'], $row['name'], $row['id']),
			$attributes,
			$usage
		);
	}

}
