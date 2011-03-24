<?php
/**
 * @package org.carrot-framework
 * @subpackage css
 */

/**
 * CSSユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSCSSUtility {

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * トリミング
	 *
	 * MS Wordからのコピペと思われるプロパティ等を削除
	 *
	 * @access public
	 * @param string $value 置換対象
	 * @param BSArray $properties 削除対象プロパティ名の配列
	 * @return string 置換結果
	 * @static
	 */
	static public function trim ($value, BSArray $properties = null) {
		$value = BSString::stripControlCharacters($value);
		if (!$properties) {
			$properties = new BSArray;
			$properties[] = 'font';
			$properties[] = 'font-size';
			$properties[] = 'font-family';
		}
		foreach ($properties as $property) {
			$value = mb_ereg_replace($property . ' ?: ?[^;"]+([;"])', '\\1', $value);
		}
		$value = str_replace('class="MsoNormal"', null, $value);
		$value = str_replace('color: windowtext', null, $value);
		$value = mb_ereg_replace(' *style="[ ;]+" *', null, $value);
		return $value;
	}
}

/* vim:set tabstop=4: */
