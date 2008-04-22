<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage role
 */

/**
 * ロール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSRole.interface.php 212 2008-04-20 03:02:06Z pooza $
 * @abstract
 */
interface BSRole {

	/**
	 * メールアドレスを取得
	 *
	 * @access public
	 * @param string $language 言語
	 * @return BSMailAddress メールアドレス
	 * @static
	 */
	public static function getMailAddress ($language = 'ja');

	/**
	 * 名前を取得
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string 名前
	 * @static
	 */
	public static function getName ($language = 'ja');

	/**
	 * JabberIDを取得
	 *
	 * @access public
	 * @return BSJabberID JabberID
	 * @static
	 */
	public static function getJabberID ();
}

/* vim:set tabstop=4 ai: */
?>