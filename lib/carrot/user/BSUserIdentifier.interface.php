<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage user
 */

/**
 * ユーザー識別
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
	 * @param string $password パスワード
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

