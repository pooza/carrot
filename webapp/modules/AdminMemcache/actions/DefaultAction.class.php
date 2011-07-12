<?php
/**
 * Defaultアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminMemcache
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class DefaultAction extends BSAction {
	public function execute () {
		return $this->getModule()->getAction('Summary')->forward();
	}

	public function handleError () {
		return $this->execute();
	}
}

/* vim:set tabstop=4: */
