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

	public function execute (FilterChain $filters) {
		$module = $this->controller->getModuleProfile();
		$this->request->setAttribute('title', $module->getConfig('DESCRIPTION'));
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
			$menu = array();
			// $menuへの代入
			require_once(ConfigCache::checkConfig($this->getMenuFile()->getPath()));

			foreach ($menu as $menuitem) {
				if (isset($menuitem['module'])) {
					$module = $this->controller->getModuleProfile($menuitem['module']);
					if ($this->controller->getModuleName() == $module->getName()) {
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
	 * メニューファイルを取得
	 *
	 * @access private
	 * @return BSFile メニューファイル
	 */
	private function getMenuFile () {
		$name = $this->getParameter('name');
		if (!$file = $this->controller->getDirectory('menu')->getEntry($name)) {
			throw new BSFileException('メニューファイル"%s"が見つかりません。', $name);
		}
		return $file;
	}
}

/* vim:set tabstop=4 ai: */
?>