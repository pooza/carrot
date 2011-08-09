<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * Cookieのサポートをチェックするフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSCookieFilter extends BSFilter {
	private $cookieName;

	public function initialize ($params = array()) {
		$this['cookie_error'] = 'Cookie機能が有効でない、又はセッションのタイムアウトです。';
		return parent::initialize($params);
	}

	public function execute () {
		if (!$this->request->isEnableCookie()) {
			return;
		}

		$methods = new BSArray;
		$methods[] = 'HEAD';
		$methods[] = 'GET';

		if ($methods->isContain($this->request->getMethod())) {
			$expire = BSDate::getNow()->setParameter('hour', '+' . BS_COOKIE_CHECKER_HOURS);
			$this->user->setAttribute($this->getCookieName(), true, $expire);
		} else {
			if (BSString::isBlank($this->user->getAttribute($this->getCookieName()))) {
				$this->request->setError('cookie', $this['cookie_error']);
			}
		}
	}

	private function getCookieName () {
		if (!$this->cookieName) {
			$this->cookieName = BSCrypt::digest($this->controller->getName('en'));
		}
		return $this->cookieName;
	}
}

/* vim:set tabstop=4: */
