<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * BASIC認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
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
		$id = $this->controller->getEnvironment('PHP_AUTH_USER');
		$password = BSCrypt::getMD5($this->controller->getEnvironment('PHP_AUTH_PW'));

		if ($password != $this->getParameter('password')) {
			return false;
		}
		if ($this->getParameter('user_id') && ($id != $this->getParameter('user_id'))) {
			return false;
		}

		return true;
	}

	public function initialize ($parameters = array()) {
		$this->setParameter('user_id', BS_ADMIN_EMAIL);
		$this->setParameter('password', BS_ADMIN_PASSWORD);
		$this->setParameter('realm', BSController::getInstance()->getServerHost()->getName());
		return parent::initialize($parameters);
	}

	public function execute (BSFilterChain $filters) {
		if (!$this->isAuthenticated()) {
			$realm = $this->getParameter('realm');
			$this->controller->sendHeader('WWW-Authenticate: Basic realm=\'' . $realm . '\'');
			$this->controller->sendHeader('HTTP/1.0 401 Unauthorized');
			exit;
		}
		$filters->execute();
	}
}

/* vim:set tabstop=4 ai: */
?>