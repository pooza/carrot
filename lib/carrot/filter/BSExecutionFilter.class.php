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
		$action = $this->controller->getAction();

		if ($action->getRequestMethods() & $this->request->getMethod()) {
			if ($file = $action->getValidationFile()) {
				require(BSConfigManager::getInstance()->compile($file));
			}
			$action->registerValidators();
			if (BSValidatorManager::getInstance()->execute() && $action->validate()) {
				$view = $action->execute();
			} else {
				$view = $action->handleError();
			}
		} else {
			$view = $action->getDefaultView();
		}

		if ($view != BSView::NONE) {
			$view = $action->getView($view);
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