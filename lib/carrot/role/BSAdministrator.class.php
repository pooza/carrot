<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage role
 */

/**
 * 管理者に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSAdministrator implements BSRole {
	private static $networks = array();
	const EMAIL = BS_ADMIN_EMAIL;
	const PASSWORD = BS_ADMIN_PASSWORD;
	const JID = BS_ADMIN_JID;

	/**
	 * メールアドレスを取得
	 *
	 * @access public
	 * @param string $language 言語
	 * @return BSMailAddress メールアドレス
	 * @static
	 */
	public static function getMailAddress ($language = 'ja') {
		return new BSMailAddress(self::EMAIL, self::getName($language));
	}

	/**
	 * 名前を取得
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string 名前
	 * @static
	 */
	public static function getName ($language = 'ja') {
		return BSTranslator::getInstance()->translate('app_name', $language) . ' 管理者';
	}

	/**
	 * JabberIDを取得
	 *
	 * @access public
	 * @return BSJabberID JabberID
	 * @static
	 */
	public static function getJabberID () {
		return new BSJabberID(self::JID);
	}

	/**
	 * 許可されたネットワークを取得
	 *
	 * @access public
	 * @return BSNetwork[] 許可されたネットワークの配列、全て許可なら空配列
	 * @static
	 */
	public static function getAllowedNetworks () {
		if (!defined('BS_ADMIN_NETWORKS')) {
			return array();
		}

		if (!self::$networks) {
			$networks = explode(',', BS_ADMIN_NETWORKS);
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
	public static function auth ($email, $password) {
		return ($email == self::EMAIL) && (BSCrypt::getMD5($password) == self::PASSWORD);
	}
}

/* vim:set tabstop=4 ai: */
?>