<?php

class LayoutUsageDCA extends Backend {
	
	public function getUsageButton($row, $href, $label, $title, $icon, $attributes) {
		$intUsage = $this->Database->prepare('
			SELECT COUNT(*) AS cnt FROM tl_page WHERE includeLayout = 1 AND layout = ?
		')->execute($row['id'])->cnt;
		if($row['fallback']) $intUsage .= '*';
		
		return sprintf(
			'<a href="%s" title="%s"%s>(%s)</a> ', 
			$this->addToUrl($href . '&id=' . $row['id']),
			sprintf($GLOBALS['TL_LANG']['tl_layout']['backboneit_layoutusage'], $row['name'], $row['id']),
			$attributes,
			$intUsage
		);
	}
	
	protected function __construct() {
		parent::__construct();
	}
	
	private static $objInstance;
	
	public static function getInstance() {
		if(!isset(self::$objInstance))
			self::$objInstance = new self();
			
		return self::$objInstance;
	}
	
}
