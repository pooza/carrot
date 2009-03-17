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

	public function execute () {
		$this->request->setAttribute('menu', $this->getMenu());
	}

	/**
	 * メニュー配列を返す
	 *
	 * @access private
	 * @return string[][] メニュー配列
	 */
	private function getMenu () {
		if (!$this->menu) {
			$prevItemIsSeparator = true;
			$config = array();
			require(BSConfigManager::getInstance()->compile($this->getMenuFile()));
			foreach ($config as $menuitem) {
				$menuitem = new BSArray($menuitem);
				if (!BSString::isBlank($menuitem['separator'])) {
					if ($prevItemIsSeparator) {
						continue;
					}
					$prevItemIsSeparator = true;
				} else {
					$prevItemIsSeparator = false;
				}
				if (!BSString::isBlank($menuitem['module'])) {
					$module = $this->controller->getModule($menuitem['module']);
					if (!$this->user->hasCredential($module->getCredential())) {
						continue;
					}
					if (BSString::isBlank($menuitem['title'])) {
						$menuitem['title'] = $module->getMenuTitle();
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
		$names = new BSArray(array(
			$this['name'],
			BSString::pascalize($this->getModule()->getPrefix()),
			BSString::underscorize($this->getModule()->getPrefix()),
		));
		foreach ($names as $name) {
			if ($file = BSConfigManager::getConfigFile('menu/' . $name)) {
				return $file;
			}
		}
		throw new BSConfigException('メニュー(%s)が見つかりません。', $names->join('|'));
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
