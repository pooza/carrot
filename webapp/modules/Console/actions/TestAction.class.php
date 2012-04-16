<?php
/**
 * Testアクション
 *
 * @package org.carrot-framework
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class TestAction extends BSAction {
	public function execute () {
		BSTestManager::getInstance()->execute($this->request['id']);
	}
}

/* vim:set tabstop=4: */
