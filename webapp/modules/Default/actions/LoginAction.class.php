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
		$this->controller->removeCookie(BSCookieHandler::getTestCookieName());
		$this->user->addCredential('Admin');
		if ($this->controller->isDebugMode()) {
			$this->user->addCredential('Develop');
		}

		$url = new BSURL(BS_ROOT_URL_HTTPS);
		$url->setAttribute('path', '/' . self::DEFAULT_MODULE_NAME . '/');
		return $this->controller->redirect($url);
	}

	public function getDefaultView () {
		$this->request->clearAttributes();
		$this->user->clearAttributes();
		$this->user->clearCredentials();
		$this->controller->setCookie(BSCookieHandler::getTestCookieName(), true);
		return BSView::INPUT;
	}

	public function handleError () {
		return $this->getDefaultView();
	}

	public function validate () {
		if (!$this->controller->getCookie(BSCookieHandler::getTestCookieName())) {
			$this->request->setError('cookie', 'Cookieを受け入れる設定にして下さい。');
		}
		if (!BSAdministrator::auth($this->request['email'], $this->request['password'])) {
			$this->request->setError('password', 'ユーザー又はパスワードが違います。');
		}
		return (count($this->request->getErrors()) == 0);
	}

	public function getRequestMethods () {
		return BSRequest::POST;
	}
}

/* vim:set tabstop=4 ai: */
?>