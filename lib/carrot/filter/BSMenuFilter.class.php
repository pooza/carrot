<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * メニュー構築フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSMenuFilter extends BSFilter {
	private $menu = array();

	public function execute (BSFilterChain $filters) {
		$module = $this->controller->getModule();
		$this->request->setAttribute('title', $module->getConfig('description'));
		$this->request->setAttribute('menu', $this->getMenu());
		$filters->execute();
	}

	/**
	 * メニュー配列を取得
	 *
	 * @access private
	 * @return string[][] メニュー配列
	 */
	private function getMenu () {
		if (!$this->menu) {
			$config = array();
			require(BSConfigManager::getInstance()->compile($this->getMenuFile()));
			foreach ($config as $menuitem) {
				if (isset($menuitem['module'])) {
					$moduleMenu = $this->controller->getModule($menuitem['module']);
					$moduleCurrent = $this->controller->getModule();
					if ($moduleCurrent->getName() == $moduleMenu->getName()) {
						$menuitem['on'] = true;
					}

					$credential = $moduleMenu->getCredential();
					if (!$credential || $this->user->hasCredential($credential)) {
						$this->menu[] = $menuitem;
					}
				} else if (isset($menuitem['href'])) {
					$this->menu[] = $menuitem;
				}
			}
		}
		return $this->menu;
	}

	/**
	 * メニューファイルを取得
	 *
	 * @access private
	 * @return BSConfigFile メニューファイル
	 */
	private function getMenuFile () {
		return $this->controller->getDirectory('menu')->getEntry(
			$this->getParameter('name'),
			'BSConfigFile'
		);
	}
}

/* vim:set tabstop=4 ai: */
?>