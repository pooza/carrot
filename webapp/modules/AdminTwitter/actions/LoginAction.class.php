<?php
/**
 * Loginアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminTwitter
 * @author 小石達也 <tkoishi@b-shock.co.jp>

 */
class LoginAction extends BSAction {
	public function execute () {
		$account = BSAuthorRole::getInstance()->getTwitterAccount();
		$account->login($this->request['verifier']);
		return $this->getModule()->redirect();
	}
}

/* vim:set tabstop=4: */
