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
	public function initialize ($parameters = array()) {
		$this['ignore_actions'] = array();
		$this['credential'] = $this->action->getCredential();
		return parent::initialize($parameters);
	}

	public function execute () {
		if ($this->isIgnoreAction($this->action)) {
			return;
		}
		if (!$this->user->hasCredential($this['credential'])) {
			if ($this->request->isAjax() || $this->request->isFlash()) {
				return $this->controller->getNotFoundAction()->forward();
			} else {
				return $this->controller->getAction()->handleDenied();
			}
		}
	}

	private function isIgnoreAction (BSAction $action) {
		$names = new BSArray($this['ignore_actions']);
		return $names->isIncluded($action->getName());
	}
}

/* vim:set tabstop=4: */
