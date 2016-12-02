<?php
/**
 * Defaultアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class DefaultAction extends BSAction {
	public function execute () {
		return $this->getModule()->getAction('DatabaseList')->forward();
	}

	public function handleError () {
		return $this->execute();
	}
}

/* vim:set tabstop=4: */
