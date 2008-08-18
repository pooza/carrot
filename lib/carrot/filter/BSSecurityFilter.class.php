<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * クレデンシャル認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
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
			return $this->controller->getAction()->getCredential();
		}
	}
}

/* vim:set tabstop=4 ai: */
?>