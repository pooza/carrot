<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * Cookieのサポートをチェックするフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCookieFilter extends BSFilter {
	private $cookieName;

	public function initialize ($parameters = array()) {
		$this['cookie_error'] = 'Cookie機能が有効でない、又はセッションのタイムアウトです。';
		return parent::initialize($parameters);
	}

	public function execute () {
		if (!$this->request->isCLI()
			&& !$this->request->isAjax()
			&& !$this->request->isFlash()
			&& !$this->request->getUserAgent()->isMobile()) {

			$methods = new BSArray;
			$methods[] = BSRequest::HEAD;
			$methods[] = BSRequest::GET;

			if ($methods->isContain($this->request->getMethod())) {
				$expire = BSDate::getNow()->setAttribute('hour', '+1');
				$this->user->setAttribute($this->getCookieName(), true, $expire);
			} else {
				if (!$this->user->getAttribute($this->getCookieName())) {
					$this->request->setError('cookie', $this['cookie_error']);
				}
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
			$this->cookieName = BSCrypt::getSHA1($this->controller->getName('en'));
		}
		return $this->cookieName;
	}
}

/* vim:set tabstop=4: */
