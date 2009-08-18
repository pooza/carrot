<?php
/**
 * @package org.carrot-framework
 * @subpackage js
 */

/**
 * JavaScriptユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSJavaScriptUtility {

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * script要素を返す
	 *
	 * @access public
	 * @return BSXMLElement script要素
	 * @static
	 */
	static public function getScriptElement () {
		$element = new BSXMLElement('script');
		$element->setAttribute('type', 'text/javascript');
		$element->setRawMode(true);
		return $element;
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
