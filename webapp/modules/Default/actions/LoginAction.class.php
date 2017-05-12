<?php
/**
 * Loginアクション
 *
 * @package __PACKAGE__
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class LoginAction extends BSAction {
	public function execute () {
		return BSModule::getInstance('AdminLog')->redirect();
	}

	public function getDefaultView () {
		$this->user->logout();
		return BSView::INPUT;
	}

	public function handleError () {
		return $this->getDefaultView();
	}

	public function validate () {
		$role = BSAdministratorRole::getInstance();
		$email = BSMailAddress::create($this->request['email']);
		if ($email->getContents() != $role->getMailAddress()->getContents()) {
			$this->request->setError('email', 'ユーザー又はパスワードが違います。');
		} else if (!$this->user->login($role, $this->request['password'])) {
			$this->request->setError('password', 'ユーザー又はパスワードが違います。');
		}
		return !$this->request->hasErrors();
	}
}

