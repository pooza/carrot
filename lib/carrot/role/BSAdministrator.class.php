<?php
/**
 * @package org.carrot-framework
 * @subpackage role
 */

/**
 * 管理者に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSAdministrator implements BSRole {
	static private $networks = array();

	/**
	 * メールアドレスを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return BSMailAddress メールアドレス
	 * @static
	 */
	static public function getMailAddress ($language = 'ja') {
		return new BSMailAddress(
			BSController::getInstance()->getConstant('ADMIN_EMAIL'),
			self::getName($language)
		);
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string 名前
	 * @static
	 */
	static public function getName ($language = 'ja') {
		return BSController::getInstance()->getName($language) . ' 管理者';
	}

	/**
	 * JabberIDを返す
	 *
	 * @access public
	 * @return BSJabberID JabberID
	 * @static
	 */
	static public function getJabberID () {
		if (!BSString::isBlank($jid = BSController::getInstance()->getConstant('ADMIN_JID'))) {
			return new BSJabberID($jid);
		}
	}

	/**
	 * 許可されたネットワークを返す
	 *
	 * @access public
	 * @return BSNetwork[] 許可されたネットワークの配列、全て許可なら空配列
	 * @static
	 */
	static public function getAllowedNetworks () {
		$constants = BSConstantHandler::getInstance();
		if (!$constants->hasParameter('ADMIN_NETWORKS')) {
			return array();
		}

		if (!self::$networks) {
			$networks = explode(',', $constants['ADMIN_NETWORKS']);
			$networks[] = '127.0.0.1/32';
			foreach ($networks as $net) {
				self::$networks[$net] = new BSNetwork($net);
			}
		}
		return self::$networks;
	}

	/**
	 * 管理者認証
	 *
	 * @access public
	 * @param string $email メールアドレス
	 * @param string $password パスフレーズ
	 * @return boolean 認証OKならTrue
	 * @static
	 */
	static public function auth ($email, $password) {
		$constants = BSConstantHandler::getInstance();

		if ($email != $constants['ADMIN_EMAIL']) {
			return false;
		} else if (!BSCrypt::getInstance()->auth($constants['ADMIN_PASSWORD'], $password)) {
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */
