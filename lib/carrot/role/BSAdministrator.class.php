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
		return new BSMailAddress(BS_ADMIN_EMAIL, self::getName($language));
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
		return new BSJabberID(BS_ADMIN_JID);
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
	 * 簡易管理者認証
	 *
	 * @access public
	 * @param string $email メールアドレス
	 * @param string $password パスフレーズ
	 * @return boolean 認証OKならTrue
	 * @static
	 */
	static public function auth ($email, $password) {
		return ($email == BS_ADMIN_EMAIL) && (BSCrypt::getMD5($password) == BS_ADMIN_PASSWORD);
	}
}

/* vim:set tabstop=4 ai: */
?>