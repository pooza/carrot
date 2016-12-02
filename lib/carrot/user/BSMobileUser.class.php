<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage user
 */

/**
 * ケータイユーザー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMobileUser extends BSUser {

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
