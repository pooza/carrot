<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * アクション実行
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSExecutionFilter extends BSFilter {
	public function execute () {
		if (!BS_DEBUG && $this->action->isCacheable()) {
			$manager = BSRenderManager::getInstance();
			$action = $this->action;
			if ($manager->hasCache($action) && ($view = $manager->getCache($action))) {
				$this->doView($view);
			} else {
				if ($view = $this->doAction()) {
					$manager->cache($this->doView($view));
				} else {
					$this->doView($view);
				}
			}
		} else {
			$this->doView($this->doAction());
		}
		return true;
	}

	private function doAction () {
		if ($this->action->isExecutable()) {
			if ($file = $this->action->getValidationFile()) {
				BSConfigManager::getInstance()->compile($file);
			}
			$this->action->registerValidators();
			if (!BSValidateManager::getInstance()->execute() || !$this->action->validate()) {
				return $this->action->handleError();
			}
			return $this->action->execute();
		} else {
			return $this->action->getDefaultView();
		}
	}

	private function doView ($view) {
		if (!($view instanceof BSView)) {
			$view = $this->action->getView($view);
		}
		if (!$view->initialize()) {
			throw new BSViewException($view . 'が初期化できません。');
		}
		$view->execute();
		$view->render();
		return $view;
	}

	/**
	 * 二度目も実行するか
	 *
	 * @access public
	 * @return string フィルタ名
	 */
	public function isMultiExecutable () {
		return true;
	}
}

/* vim:set tabstop=4: */
