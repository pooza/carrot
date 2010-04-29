<?php
/**
 * Loginアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminTwitter
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class LoginAction extends BSAction {
	public function execute () {
		$service = new BSTwitterService;
		$service->login($this->request['verifier']);
		return $this->getModule()->redirect();
	}
}

/* vim:set tabstop=4: */
