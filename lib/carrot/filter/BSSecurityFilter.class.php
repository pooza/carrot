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
		$action = $this->controller->getAction();
		if ($action->getCredential() !== null) {
			$credential = $action->getCredential();
		} else {
			$credential = $this->getParameter('credential');
		}

		if ($credential && !$this->user->hasCredential($credential)) {
			return $this->controller->forwardTo($this->controller->getSecureAction());
		}
		$filters->execute();
	}
}

/* vim:set tabstop=4 ai: */
?>