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
		if (BSArray::isArray($value)) {
			$body =  new BSArray;
			foreach ($value as $key => $item) {
				$body[] = sprintf('%s:%s', $key, self::quote($item));
			}
			return '{' . $body->join(', ') . '}';
		} else {
			$value = trim($value);
			switch (BSString::toLower($value)) {
				case null:
				case 'null':
					return 'null';
				case 'true':
					return 'true';
				case 'false':
					return 'false';
				default:
					if (is_numeric($value)) {
						return $value;
					} else {
						$value = str_replace("\\", "\\\\", $value);
						$value = str_replace("'", "\\'", $value);
						return "'" . $value . "'";
					}
			}
		}
	}
}

/* vim:set tabstop=4: */
