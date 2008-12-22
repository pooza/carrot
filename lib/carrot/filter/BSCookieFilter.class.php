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
	public function execute (BSFilterChain $filters) {
		if (!$this->request->isCLI()
			&& !$this->request->isAjax()
			&& !$this->request->isFlash()
			&& !$this->request->getUserAgent()->isMobile()) {

			switch ($this->request->getMethod()) {
				case BSRequest::HEAD:
				case BSRequest::GET:
					$expire = BSDate::getNow()->setAttribute('hour', '+1');
					$this->user->setAttribute(BSUser::getTestCookieName(), true, $expire);
					break;
				default:
					if (!$this->user->getAttribute(BSUser::getTestCookieName())) {
						$this->request->setError('cookie', 'Cookieを受け入れる様にして下さい。');
					}
					break;
			}
		}
		$filters->execute();
	}
}

/* vim:set tabstop=4: */
