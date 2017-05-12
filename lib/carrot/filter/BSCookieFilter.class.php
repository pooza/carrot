<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * Cookieのサポートをチェックするフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSCookieFilter extends BSFilter {
	private $cookieName;

	public function initialize ($params = []) {
		$this['cookie_error'] = 'Cookie機能が有効でない、又はセッションのタイムアウトです。';
		return parent::initialize($params);
	}

	public function execute () {
		if (!$this->request->isEnableCookie()) {
			return;
		}

		$this->cookieName = BSCrypt::digest($this->controller->getName('en'));
		switch ($this->request->getMethod()) {
			case 'HEAD':
			case 'GET':
				$time = BSDate::getNow()->setParameter('hour', '+' . BS_COOKIE_CHECKER_HOURS);
				$this->user->setAttribute($this->cookieName, true, $time);
				break;
			default:
				if (BSString::isBlank($this->user->getAttribute($this->cookieName))) {
					$this->request->setError('cookie', $this['cookie_error']);
				}
				break;
		}
	}
}

