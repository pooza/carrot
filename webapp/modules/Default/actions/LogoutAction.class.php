<?php
/**
 * Logoutアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class LogoutAction extends BSAction {
	public function execute () {
		$this->user->logout();
		return $this->getModule()->getAction('Login')->redirect();
	}
}

/* vim:set tabstop=4: */
