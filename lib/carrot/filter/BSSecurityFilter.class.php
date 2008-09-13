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
			return $this->controller->forwardTo($this->controller->getSecureAction());
		}
		$filters->execute();
	}

	private function getCredential () {
		if ($credential = $this->getParameter('credential')) {
			return $credential;
		} else {
			return $this->action->getCredential();
		}
	}
}

/* vim:set tabstop=4 ai: */
?>