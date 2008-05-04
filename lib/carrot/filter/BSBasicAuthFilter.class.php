<?php
/**
 * @package jp.co.b-shock.carrot
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
		$userid = $this->controller->getEnvironment('PHP_AUTH_USER');
		$password = BSCrypt::getMD5($this->controller->getEnvironment('PHP_AUTH_PW'));

		// パスフレーズのチェックは必須
		if ($password != $this->getParameter('password')) {
			return false;
		}
		// ユーザーIDは、指定されている場合のみチェックする
		if ($this->getParameter('userid') && ($userid != $this->getParameter('userid'))) {
			return false;
		}

		return true;
	}

	public function initialize ($parameters = null) {
		if (!is_array($parameters)) {
			$parameters = array();
		}
		$default = array(
			'userid' => BSAdministrator::EMAIL,
			'password' => BSAdministrator::PASSWORD,
			'realm' => 'Please enter "User ID" and "Password".',
		);
		$parameters = array_merge($default, $parameters);

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