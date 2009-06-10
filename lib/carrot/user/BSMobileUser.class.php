<?php
/**
 * @package org.carrot-framework
 * @subpackage user
 */

/**
 * ケータイユーザー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMobileUser extends BSUser {
	static private $instance;

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSMobileUser インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * ログイン
	 *
	 * @access public
	 * @param BSUserIdentifier $id ユーザーIDを含んだオブジェクト
	 * @param string $password パスワード
	 * @return boolean 成功ならTrue
	 */
	public function login (BSUserIdentifier $identifier = null, $password = null) {
		if (!$identifier) {
			$identifier = BSRequest::getInstance()->getUserAgent();
		}
		return parent::login($identifier, $password);
	}
}

/* vim:set tabstop=4: */
