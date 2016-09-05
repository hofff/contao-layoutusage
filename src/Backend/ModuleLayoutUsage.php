<?php

namespace Hofff\Contao\LayoutUsage\Backend;

use Contao\BackendModule;
use Contao\Controller;
use Contao\Database;
use Contao\DataContainer;
use Contao\System;
use tl_page;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class ModuleLayoutUsage extends BackendModule {

	/**
	 * @var array
	 */
	protected $layout;

	/**
	 * @var array
	 */
	protected $usages;

	/**
	 * @param DataContainer $dc
	 */
	public function __construct(DataContainer $dc = null) {
		parent::__construct($dc);
	}

	/**
	 * @param integer $layout
	 * @return void
	 */
	public function setLayout($layout) {
		$sql = 'SELECT id, name FROM tl_layout WHERE id = ?';
		$this->layout = Database::getInstance()->prepare($sql)->execute($layout)->row();
	}

	/**
	 * @param DataContainer $dc
	 * @return string
	 */
	public function generateFromDC($dc) {
		$this->setLayout($dc->id);
		return $this->generate();
	}

	/**
	 * @see \Contao\BackendModule::generate()
	 */
	public function generate() {
		if(!$this->layout) {
			Controller::redirect(System::getReferer());
			return;
		}

		$this->usages = self::getUsages($this->layout['id']);
		if(!$this->usages) {
			Controller::redirect(System::getReferer());
			return;
		}

		$this->strTemplate = 'be_hofff_layoutusage';

		return parent::generate();
	}

	/**
	 * @see \Contao\BackendModule::compile()
	 */
	protected function compile() {
		System::loadLanguageFile('tl_page');
		Controller::loadDataContainer('tl_page');

		$pageDCA = new tl_page;
		$usages = $this->usages;
		$count = count($usages);

		foreach($usages as &$usage) {
			$count += $usage['inherited'];
			$usage['icon'] = $pageDCA->addIcon($usage, null, null, '', true);
		}
		unset($usage);

		$this->Template->layout = $this->layout;
		$this->Template->usages = $usages;
		$this->Template->count = $count;
	}

	/**
	 * @param integer $id
	 * @return array
	 */
	public static function getUsages($id) {
		$usages = [];

		$sql = <<<SQL
SELECT
	id,
	title,
	type,
	published,
	start,
	stop,
	hide,
	protected
FROM
	tl_page
WHERE
	includeLayout = 1
	AND layout = ?
SQL;
		$pages = Database::getInstance()->prepare($sql)->execute($id);

		while($pages->next()) {
			$page = $pages->row();
			$page['inherited'] = self::getInheritedCount($page['id']);
			$usages[] = $page;
		}

		return $usages;
	}

	/**
	 * @param integer $id
	 * @return integer
	 */
	protected static function getInheritedCount($id) {
		$db = Database::getInstance();
		$pids = [ $id ];
		$count = 0;

		while($pids) {
			$sql = 'SELECT id FROM tl_page WHERE includeLayout = \'\' AND pid IN (' . implode(',', $pids) . ')';
			$pids = $db->execute($sql)->fetchEach('id');
			$count += count($pids);
		}

		return $count;
	}

}
