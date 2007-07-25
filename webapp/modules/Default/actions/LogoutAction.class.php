<?php
/**
 * Logoutアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class LogoutAction extends BSAction {
	public function execute () {
		$this->request->clearAttributes();
		$this->user->clearAttributes();
		$this->user->setAuthenticated(false);
		$this->user->clearCredentials();
		return $this->controller->forward(MO_LOGIN_MODULE, MO_LOGIN_ACTION);
	}
}

/* vim:set tabstop=4 ai: */
?>