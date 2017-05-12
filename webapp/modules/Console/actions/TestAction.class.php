<?php
/**
 * Testアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class TestAction extends BSAction {
	public function execute () {
		BSTestManager::getInstance()->execute($this->request['id']);
	}
}

