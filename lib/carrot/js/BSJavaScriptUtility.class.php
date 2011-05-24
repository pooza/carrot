<?php
/**
 * @package org.carrot-framework
 * @subpackage js
 */

/**
 * JavaScriptユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJavaScriptUtility {

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * 文字列のクォート
	 *
	 * @access public
	 * @param string $value 置換対象
	 * @return string 置換結果
	 * @static
	 */
	static public function quote ($value) {
		$serializer = new BSJSONSerializer;
		return $serializer->encode($value);
	}
}

/* vim:set tabstop=4: */
