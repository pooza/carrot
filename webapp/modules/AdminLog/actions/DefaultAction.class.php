<?php
/**
 * Defaultアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminLog
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DefaultAction extends BSAction {
	public function execute () {
		return $this->controller->forwardTo($this->getModule()->getAction('Browse'));
	}
}

/* vim:set tabstop=4 ai: */
?>