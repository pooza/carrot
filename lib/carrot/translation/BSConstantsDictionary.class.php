<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage translation
 */

/**
 * 定数辞書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSConstantsDictionary.class.php 5 2007-07-25 08:04:01Z pooza $
 */
class BSConstantsDictionary implements BSDictionary {

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		return get_class($this);
	}

	/**
	 * 翻訳して返す
	 *
	 * @access public
	 * @param string $label ラベル
	 * @param string $language 言語
	 * @return string 翻訳された文字列
	 */
	public function translate ($label, $language) {
		$labels = array(
			strtoupper($label),
			strtoupper($label . '_' . $language),
		);
		foreach ($labels as $label) {
			if (defined($label)) {
				return constant($label);
			}
		}
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return '定数辞書';
	}
}

/* vim:set tabstop=4 ai: */
?>