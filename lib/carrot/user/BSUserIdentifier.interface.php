<?php
/**
 * @package org.carrot-framework
 * @subpackage user
 */

/**
 * ユーザー識別
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
interface BSUserIdentifier {

	/**
	 * ユーザーIDを返す
	 *
	 * @access public
	 * @return string ユーザーID
	 */
	public function getUserID ();

	/**
	 * 認証
	 *
	 * @access public
	 * @params string $password パスワード
	 * @return boolean 正しいユーザーならTrue
	 */
	public function auth ($password = null);

	/**
	 * 認証時に与えられるクレデンシャルを返す
	 *
	 * @access public
	 * @return BSArray クレデンシャルの配列
	 */
	public function getCredentials ();
}

/* vim:set tabstop=4: */
