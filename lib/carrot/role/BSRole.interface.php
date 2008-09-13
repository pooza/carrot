<?php
/**
 * @package org.carrot-framework
 * @subpackage role
 */

/**
 * ロール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
interface BSRole {

	/**
	 * メールアドレスを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return BSMailAddress メールアドレス
	 * @static
	 */
	static public function getMailAddress ($language = 'ja');

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string 名前
	 * @static
	 */
	static public function getName ($language = 'ja');

	/**
	 * JabberIDを返す
	 *
	 * @access public
	 * @return BSJabberID JabberID
	 * @static
	 */
	static public function getJabberID ();
}

/* vim:set tabstop=4 ai: */
?>