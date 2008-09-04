<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * アクション実行
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSExecutionFilter extends BSFilter {
	public function execute (BSFilterChain $filters) {
		if ($this->action->getRequestMethods() & $this->request->getMethod()) {
			if ($file = $this->action->getValidationFile()) {
				require(BSConfigManager::getInstance()->compile($file));
			}
			$this->action->registerValidators();
			if (BSValidatorManager::getInstance()->execute() && $this->action->validate()) {
				$view = $this->action->execute();
			} else {
				$view = $this->action->handleError();
			}
		} else {
			$view = $this->action->getDefaultView();
		}

		if ($view != BSView::NONE) {
			$view = $this->action->getView($view);
			if ($view->initialize()) {
				$view->execute();
				$view->render();
			} else {
				throw new BSException('%sの%sが初期化できません。', $module, $view);
			}
		}
	}
}

/* vim:set tabstop=4 ai: */
?>