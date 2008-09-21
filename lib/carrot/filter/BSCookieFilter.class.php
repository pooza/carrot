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
		if (!$this->request->isCLI() && !$this->request->getUserAgent()->isMobile()) {
			$name = BSCookieHandler::getTestCookieName();
			switch ($this->request->getMethod()) {
				case BSRequest::GET:
					$this->controller->setCookie($name, true);
					break;
				default:
					if (!$this->controller->getCookie($name)) {
						$this->request->setError('cookie', 'Cookieを受け入れる様にして下さい。');
					}
					break;
			}
		}
		$filters->execute();
	}
}

/* vim:set tabstop=4 ai: */
?>