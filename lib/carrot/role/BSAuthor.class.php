<?php
/**
 * @package org.carrot-framework
 * @subpackage role
 */

/**
 * 発行者に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSAuthor implements BSRole {

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
			BSController::getInstance()->getConstant('AUTHOR_EMAIL'),
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
		if ($name = BSController::getInstance()->getConstant('AUTHOR_NAME')) {
			return $name;
		}
		return BSController::getInstance()->getName($language);
	}

	/**
	 * JabberIDを返す
	 *
	 * @access public
	 * @return BSJabberID JabberID
	 * @static
	 */
	static public function getJabberID () {
		if ($jid = BSController::getInstance()->getConstant('AUTHOR_JID')) {
			return new BSJabberID($jid);
		}
	}

	/**
	 * 発行者認証
	 *
	 * @access public
	 * @param string $email メールアドレス
	 * @param string $password パスフレーズ
	 * @return boolean 認証OKならTrue
	 * @static
	 */
	static public function auth ($email, $password) {
		$constants = BSConstantHandler::getInstance();

		if ($email != $constants['AUTHOR_EMAIL']) {
			return false;
		} else if (!BSCrypt::getInstance()->auth($constants['AUTHOR_PASSWORD'], $password)) {
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */
