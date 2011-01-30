<?php
/**
 * Testアクション
 *
 * @package org.carrot-framework
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class TestAction extends BSAction {
	public function execute () {
		BSTestManager::getInstance()->execute();
	}
}

/* vim:set tabstop=4: */
