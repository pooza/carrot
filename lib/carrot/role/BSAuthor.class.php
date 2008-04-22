<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage role
 */

/**
 * 発行者に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
class BSAuthor implements BSRole {
	const EMAIL = BS_AUTHOR_EMAIL;
	const PASSWORD = BS_AUTHOR_PASSWORD;
	const JID = BS_AUTHOR_JID;

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
		if (defined('BS_AUTHOR_NAME')) {
			return BS_AUTHOR_NAME;
		}
		return BSTranslator::getInstance()->translate('app_name', $language);
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
	 * 簡易発行者認証
	 *
	 * @access public
	 * @param string $email メールアドレス
	 * @param string $password パスフレーズ
	 * @return boolean 認証OKならTrue
	 * @static
	 */
	public static function auth ($email, $password) {
		return ($email == self::EMAIL) && ($password == self::PASSWORD);
	}
}

/* vim:set tabstop=4 ai: */
?>