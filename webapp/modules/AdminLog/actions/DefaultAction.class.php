<?php
/**
 * Defaultアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminLog
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class DefaultAction extends BSAction {
	public function execute () {
		return $this->getModule()->getAction('Browse')->forward();
	}

	public function handleError () {
		return $this->execute();
	}
}

/* vim:set tabstop=4: */
