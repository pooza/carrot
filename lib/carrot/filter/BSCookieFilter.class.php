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

			if ($methods->isIncluded($this->request->getMethod())) {
				$expire = BSDate::getNow()->setAttribute('hour', '+1');
				$this->user->setAttribute(BSUser::getTestCookieName(), true, $expire);
			} else {
				if (!$this->user->getAttribute(BSUser::getTestCookieName())) {
					$this->request->setError('cookie', $this['cookie_error']);
				}
			}
		}
	}
}

/* vim:set tabstop=4: */
