<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * メニュー構築フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMenuFilter extends BSFilter {
	private $menu;

	public function execute () {
		$this->request->setAttribute('menu', $this->getMenu());
	}

	private function getMenu () {
		if (!$this->menu) {
			$this->menu = new BSArray;
			$separator = true; //次の仕切りを無視するか？
			foreach (BSConfigManager::getInstance()->compile($this->getMenuFile()) as $values) {
				if ($menuitem = $this->getMenuItem($values)) {
					if (BSString::isBlank($menuitem['separator'])) {
						$separator = false;
					} else {
						if ($separator) {
							continue;
						}
						$separator = true;
					}
					$this->menu[] = $menuitem;
				}
			}
		}
		return $this->menu;
	}

	private function getMenuItem ($values) {
		$values = new BSArray($values);
		if (!BSString::isBlank($values['module'])) {
			if (!$module = $this->controller->getModule($values['module'])) {
				$message = new BSStringFormat('モジュール "%s" がありません。');
				$message[] = $values['module'];
				throw new BSConfigException($message);
			}
			if (BSString::isBlank($values['title'])) {
				$values['title'] = $module->getMenuTitle();
			}
			if (BSString::isBlank($values['credential'])) {
				$values['credential'] = $module->getCredential();
			}
		}
		if (!BSString::isBlank($values['href'])) {
			$url = BSURL::create();
			$url['path'] = $values['href'];
			if(BSString::isBlank($value = $url->getParameter(BSUserAgent::ACCESSOR))) {
				$useragent = $this->request->getUserAgent();
			} else {
				$useragent = BSUserAgent::create($value);
			}
			$url->setParameters($useragent->getQuery());
			$values['href'] = $url->getFullPath();
		}
		if ($this->user->hasCredential($values['credential'])) {
			return $values;
		}
	}

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

		$message = new BSStringFormat('メニュー (%s)が見つかりません。');
		$message[] = $names->join('|');
		throw new BSConfigException($message);
	}

	private function getModule () {
		return $this->controller->getModule();
	}
}

/* vim:set tabstop=4: */
