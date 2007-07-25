<?php
/**
 * Logoutアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: LogoutAction.class.php 34 2006-05-08 08:35:43Z pooza $
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