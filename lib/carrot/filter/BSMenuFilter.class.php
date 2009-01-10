<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * メニュー構築フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMenuFilter extends BSFilter {
	private $menu = array();

	public function execute (BSFilterChain $filters) {
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
				$menuitem = new BSArray($menuitem);
				if ($menuitem['module']) {
					$module = $this->controller->getModule($menuitem['module']);
					if (!$menuitem['title']) {
						$menuitem['title'] = $module->getConfig('title');
					}
					if (!$this->user->hasCredential($module->getCredential())) {
						continue;
					}
				}
				$this->menu[] = $menuitem;
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
			$this['name'],
			BSString::pascalize($this->getModule()->getPrefix()),
			BSString::underscorize($this->getModule()->getPrefix()),
		);
		foreach ($names as $name) {
			if ($file = BSConfigManager::getConfigFile('menu/' . $name)) {
				return $file;
			}
		}
		throw new BSConfigException('メニューファイルが見つかりません。');
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

/* vim:set tabstop=4: */
