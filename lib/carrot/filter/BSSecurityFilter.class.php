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
		$this['credential'] = $this->action->getCredential();
		return parent::initialize($parameters);
	}

	public function execute () {
		if (!$this->user->hasCredential($this['credential'])) {
			if ($this->request->isAjax() || $this->request->isFlash()) {
				return $this->controller->getNotFoundAction()->forward();
			}
			return $this->controller->getAction()->deny();
		}
	}
}

/* vim:set tabstop=4: */
