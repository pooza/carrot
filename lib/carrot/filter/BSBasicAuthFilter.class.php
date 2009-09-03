<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * BASIC認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSBasicAuthFilter extends BSFilter {

	/**
	 * 認証
	 *
	 * @access private
	 * @return 許可されたらTrue
	 */
	private function isAuthenticated () {
		if (BSString::isBlank($password = $this->controller->getEnvironment('PHP_AUTH_PW'))) {
			return false;
		}
		if (!BSCrypt::getInstance()->auth($this['password'], $password)) {
			return false;
		}

		if (!BSString::isBlank($this['user_id'])) {
			return ($this['user_id'] == $this->controller->getEnvironment('PHP_AUTH_USER'));
		}
		return true;
	}

	public function initialize ($parameters = array()) {
		$this['user_id'] = $this->controller->getConstant('ADMIN_EMAIL');
		$this['password'] = $this->controller->getConstant('ADMIN_PASSWORD');
		$this['realm'] = $this->controller->getHost()->getName();
		return parent::initialize($parameters);
	}

	public function execute () {
		if (!$this->isAuthenticated()) {
			$this->controller->setHeader(
				'WWW-Authenticate',
				sprintf('Basic realm=\'%s\'', $this['realm'])
			);
			$this->controller->setHeader('Status', BSHTTP::getStatus(401));
			$this->controller->putHeaders();
			return true;
		}
	}
}

/* vim:set tabstop=4: */
