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
		try {
			$account = BSAuthorRole::getInstance()->getTwitterAccount();
			$account->login($this->request['verifier']);
		} catch (Exception $e) {
			$this->request->setError('twitter', $e->getMessage());
		}
		return $this->getModule()->redirect();
	}
}

/* vim:set tabstop=4: */
