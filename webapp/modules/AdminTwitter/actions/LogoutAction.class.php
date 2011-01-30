<?php
/**
 * Logoutアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminTwitter
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class LogoutAction extends BSAction {
	public function execute () {
		$account = BSAuthorRole::getInstance()->getTwitterAccount();
		$account->logout();
		return $this->getModule()->redirect();
	}
}

/* vim:set tabstop=4: */
