<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage string.translate
 */

/**
 * 翻訳を行わない辞書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSRawDictionary implements BSDictionary {

	/**
	 * 翻訳して返す
	 *
	 * @access public
	 * @param string $label ラベル
	 * @param string $language 言語
	 * @return string 翻訳された文字列
	 */
	public function translate ($label, $language) {
		return $label;
	}

	/**
	 * 辞書の名前を返す
	 *
	 * @access public
	 * @return string 辞書の名前
	 */
	public function getDictionaryName () {
		return get_class($this);
	}
}

