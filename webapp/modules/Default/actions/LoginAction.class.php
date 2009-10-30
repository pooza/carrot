<?php
/**
 * Loginアクション
 *
 * @package __PACKAGE__
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class LoginAction extends BSAction {
	public function execute () {
		$url = BSURL::getInstance($this->controller->getAttribute('ROOT_URL_HTTPS'));
		$url['path'] = '/AdminLog/';
		return $url->redirect();
	}

	public function getDefaultView () {
		$this->request->clearAttributes();
		$this->user->clearAttributes();
		$this->user->clearCredentials();
		return BSView::INPUT;
	}

	public function handleError () {
		return $this->getDefaultView();
	}

	public function validate () {
		$role = BSAdministratorRole::getInstance();
		$email = BSMailAddress::getInstance($this->request['email']);
		if ($email->getContents() != $role->getMailAddress()->getContents()) {
			$this->request->setError('email', 'ユーザー又はパスワードが違います。');
		} else if (!$this->user->login($role, $this->request['password'])) {
			$this->request->setError('password', 'ユーザー又はパスワードが違います。');
		}
		return !$this->request->hasErrors();
	}
}

/* vim:set tabstop=4: */
