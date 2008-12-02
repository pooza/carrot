<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * クレデンシャル認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSecurityFilter extends BSFilter {
	public function execute (BSFilterChain $filters) {
		if (!$this->user->hasCredential($this->getCredential())) {
			if ($this->request->isAjax() || $this->request->isFlash()) {
				return $this->controller->getNotFoundAction()->forward();
			} else {
				return $this->controller->getSecureAction()->forward();
			}
		}
		$filters->execute();
	}

	private function getCredential () {
		if ($credential = $this['credential']) {
			return $credential;
		} else {
			return $this->action->getCredential();
		}
	}
}

/* vim:set tabstop=4 ai: */
?>