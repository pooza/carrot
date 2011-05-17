<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * BASIC認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSBasicAuthFilter extends BSFilter {
	private function isAuthenticated () {
		if (BSString::isBlank($password = $this->controller->getAttribute('PHP_AUTH_PW'))) {
			return;
		}
		if (!BSCrypt::getInstance()->auth($this['password'], $password)) {
			return;
		}

		if (!BSString::isBlank($this['user_id'])) {
			return ($this['user_id'] == $this->controller->getAttribute('PHP_AUTH_USER'));
		}
		return BSController::COMPLETED;
	}

	public function initialize ($params = array()) {
		$this['user_id'] = $this->controller->getAttribute('ADMIN_EMAIL');
		$this['password'] = $this->controller->getAttribute('ADMIN_PASSWORD');
		$this['realm'] = $this->controller->getHost()->getName();
		return parent::initialize($params);
	}

	public function execute () {
		if (!$this->isAuthenticated()) {
			BSView::putHeader('WWW-Authenticate: Basic realm=\'' . $this['realm'] . '\'');
			BSView::putHeader('Status: ' . BSHTTP::getStatus(401));
			return true;
		}
	}
}

/* vim:set tabstop=4: */
