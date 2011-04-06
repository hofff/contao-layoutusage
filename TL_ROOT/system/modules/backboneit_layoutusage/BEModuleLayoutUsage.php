<?php

class BEModuleLayoutUsage extends BackendModule {
		
	protected $strTemplate = 'be_backboneit_layoutusage';
	
	protected $arrLayout;
	
	protected $arrUsages = array();
	
	public function __construct() {
		parent::__construct();
	}
	
	public function generateFromDC($objDC) {
		$this->setLayout($objDC->id);
		return $this->generate();
	}
	
	public function setLayout($varLayout) {
		if(is_object($varLayout)) {
			$this->arrLayout = $varLayout->row();
		} else {
			$this->arrLayout = $this->Database->prepare('
				SELECT id, name, fallback FROM tl_layout WHERE id = ?
			')->execute($varLayout)->row();
		}
	}
	
	public function generate() {
		if(!$this->arrLayout)
			$this->redirect($this->getReferer());
		
		$objPages = $this->Database->prepare('
			SELECT id, title, type, published, start, stop, hide, protected FROM tl_page WHERE includeLayout = 1 AND layout = ?
		')->execute($this->arrLayout['id']);
		
		if(!$this->arrLayout['fallback'] && !$objPages->numRows)
			$this->redirect($this->getReferer());
			
		while($objPages->next()) {
			$arrPage = $objPages->row();
			$arrPage['inherited'] = $this->getInheritedCount($objPages->id);
			$this->arrUsages[] = $arrPage;
		}
		
		return parent::generate();
	}
	
	protected function compile() {
		$this->loadLanguageFile('tl_page');
		include_once TL_ROOT . '/system/modules/backend/dca/tl_page.php';
		$objPageDCA = new tl_page();
		
		$intFallback = $this->arrLayout['fallback'] ? $this->getInheritedCount(0) : 0;
		$intCount = count($this->arrUsages) + $intFallback;
		
		foreach($this->arrUsages as &$arrUsage) {
			$intCount += $arrUsage['inherited'];
			
			$arrUsage['icon'] = $objPageDCA->addIcon($arrUsage, null, null, '', true);
			$arrUsage['link'] = sprintf(
				$GLOBALS['TL_LANG']['tl_layout']['backboneit_layoutusage_page'],
				$arrUsage['title'],
				$arrUsage['id']
			);
			
			$arrUsage['inheritedText'] = sprintf(
				$GLOBALS['TL_LANG']['tl_layout']['backboneit_layoutusage_inherited'],
				$arrUsage['inherited']
			);
			
			$arrUsage['treeHref'] = $this->Environment->script . '?do=page&amp;node=' . $arrUsage['id'];
			$arrUsage['treeTitle'] = sprintf(
				$GLOBALS['TL_LANG']['tl_layout']['backboneit_layoutusage_pagetree'],
				$arrUsage['title'],
				$arrUsage['id']
			);
			
			$arrUsage['editIcon'] = $this->generateImage('edit.gif', $arrUsage['linkTitle']);
			
			$arrUsage['editHref'] = $this->Environment->script . '?do=page&amp;act=edit&amp;id=' . $arrUsage['id'];
			$arrUsage['editTitle'] = sprintf($GLOBALS['TL_LANG']['tl_page']['edit'][1], $arrUsage['id']);
		}
			
		$this->Template->setData(array(
			'count'		=> count($this->arrUsages),
			'wholecount'=> $intCount,
			'fallback'	=> $intFallback,
			'usages'	=> $this->arrUsages,
			'headline'	=> sprintf(
				$GLOBALS['TL_LANG']['tl_layout']['backboneit_layoutusage_headline'],
				$this->arrLayout['name'],
				$this->arrLayout['id']
			),
			'backLink'	=> $this->getReferer(true)
		));
	}
	
	protected function getInheritedCount($intID) {
		$arrPIDs = array($intID);
		$intCount = 0;
		
		while($arrPIDs) {
			$arrPIDs = $this->Database->execute('
				SELECT id FROM tl_page WHERE includeLayout = \'\' AND pid IN (' . implode(',', $arrPIDs) . ')
			')->fetchEach('id');
			$intCount += count($arrPIDs);
		}
		
		return $intCount;
	}
	
}
