<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * アクション実行
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSExecutionFilter extends BSFilter {
	public function execute (BSFilterChain $filters) {
		if (!$method = $this->request->getMethod()) {
			//HEAD又は未定義メソッドの場合、GETとしてふるまう。
			$method = BSRequest::GET;
		}

		if ($view = $this->executeAction($method)) {
			$this->executeView($view);
		}
	}

	/**
	 * アクションを実行する
	 *
	 * @access private
	 * @param integer $method メソッド、BSRequest::GET等
	 * @return string ビュー名、ビューが必要ない場合は空文字列
	 */
	private function executeAction ($method) {
		if (($this->action->getRequestMethods() & $method) == false) {
			return $this->action->getDefaultView();
		}

		if ($file = $this->action->getValidationFile()) {
			require(BSConfigManager::getInstance()->compile($file));
		}
		$this->action->registerValidators();

		if (!BSValidateManager::getInstance()->execute() || !$this->action->validate()) {
			return $this->action->handleError();
		}
		return $this->action->execute();
	}

	/**
	 * ビューを実行する
	 *
	 * @access private
	 * @param integer $name ビュー名
	 */
	private function executeView ($name) {
		if ((!$instance = $this->action->getView($name)) || !$instance->initialize()) {
			throw new BSRenderException(
				'%sのビュー "%s" が初期化できません。',
				$this->action->getModule(),
				$name
			);
		}
		$instance->execute();
		$instance->render();
	}
}

/* vim:set tabstop=4 ai: */
?>