<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage translation
 */

/**
 * 辞書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSDictionary.interface.php 5 2007-07-25 08:04:01Z pooza $
 */
interface BSDictionary {

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName ();

	/**
	 * 翻訳して返す
	 *
	 * @access public
	 * @param string $label ラベル
	 * @param string $language 言語
	 * @return string 翻訳された文字列
	 */
	public function translate ($label, $language);
}

/* vim:set tabstop=4 ai: */
?>