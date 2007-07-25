<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage actor
 */

/**
 * アクター
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
interface BSActor {

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