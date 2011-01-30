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
			$expire = BSDate::getNow()->setAttribute('hour', '+' . BS_COOKIE_CHECKER_HOURS);
			$this->user->setAttribute($this->getCookieName(), true, $expire);
		} else {
			if (BSString::isBlank($this->user->getAttribute($this->getCookieName()))) {
				$this->request->setError('cookie', $this['cookie_error']);
			}
		}
	}

	/**
	 * テスト用Cookieの名前を返す
	 *
	 * @access private
	 * @return string テスト用Cookieの名前
	 */
	private function getCookieName () {
		if (!$this->cookieName) {
			$this->cookieName = BSCrypt::getDigest($this->controller->getName('en'));
		}
		return $this->cookieName;
	}
}

/* vim:set tabstop=4: */
