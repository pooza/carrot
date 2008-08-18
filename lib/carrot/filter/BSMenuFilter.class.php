<?php
/**
 * @package org.carrot-framework
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
		$this->request->setAttribute('title', $this->getModule()->getConfig('description'));
		$this->request->setAttribute('menu', $this->getMenu());
		$filters->execute();
	}

	/**
	 * メニュー配列を返す
	 *
	 * @access private
	 * @return string[][] メニュー配列
	 */
	private function getMenu () {
		if (!$this->menu) {
			$config = array();
			require(BSConfigManager::getInstance()->compile($this->getMenuFile()));
			foreach ($config as $menuitem) {
				if ($menuitem['title'] == '---') {
					$this->menu[] = $menuitem;
				} else if (isset($menuitem['module'])) {
					$module = $this->controller->getModule($menuitem['module']);
					if ($this->getModule()->getName() == $module->getName()) {
						$menuitem['on'] = true;
					}
					$credential = $module->getCredential();
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
	 * メニューファイルを返す
	 *
	 * @access private
	 * @return BSConfigFile メニューファイル
	 */
	private function getMenuFile () {
		$names = array(
			$this->getParameter('name'),
			BSString::pascalize($this->getModule()->getPrefix()),
			BSString::underscorize($this->getModule()->getPrefix()),
		);
		foreach ($names as $name) {
			if ($file = BSConfigManager::getConfigFile('menu/' . $name)) {
				return $file;
			}
		}
	}

	/**
	 * 呼ばれたモジュールを返す
	 *
	 * @access private
	 * @return BSConfigFile メニューファイル
	 */
	private function getModule () {
		return $this->controller->getModule();
	}
}

/* vim:set tabstop=4 ai: */
?>