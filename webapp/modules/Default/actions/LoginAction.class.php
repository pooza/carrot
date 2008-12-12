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
	const DEFAULT_MODULE_NAME = 'AdminLog';

	public function execute () {
		$this->user->addCredential('Admin');
		if ($this->controller->isDebugMode()) {
			$this->user->addCredential('Develop');
		}

		$url = new BSURL($this->controller->getConstant('ROOT_URL_HTTPS'));
		$url['path'] = '/' . self::DEFAULT_MODULE_NAME . '/';
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
		if (!BSAdministrator::auth($this->request['email'], $this->request['password'])) {
			$this->request->setError('password', 'ユーザー又はパスワードが違います。');
		}
		return !$this->request->hasErrors();
	}

	public function getRequestMethods () {
		return BSRequest::POST;
	}
}

/* vim:set tabstop=4: */
