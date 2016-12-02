<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage user.role
 */

/**
 * ロール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
interface BSRole extends BSUserIdentifier {

	/**
	 * メールアドレスを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return BSMailAddress メールアドレス
	 */
	public function getMailAddress ($language = 'ja');

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string 名前
	 */
	public function getName ($language = 'ja');
}

/* vim:set tabstop=4: */
