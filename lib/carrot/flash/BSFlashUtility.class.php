<?php
/**
 * @package org.carrot-framework
 * @subpackage flash
 */

/**
 * Flashユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFlashUtility {

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * object要素を返す
	 *
	 * @access public
	 * @param BSHTTPRedirector FlashムービーのURL $url
	 * @return BSXMLElement object要素
	 * @static
	 */
	static public function getObjectElement (BSHTTPRedirector $url = null) {
		$element = new BSXMLElement('object');
		$element->setAttribute('width', '100%');
		$element->setAttribute('height', '100%');
		$element->setAttribute('type', BSMIMEType::getType('swf'));
		$element->createElement('p', 'Flash Player ' . BS_FLASH_PLAYER_VER . ' 以上が必要です。');

		if ($url) {
			$element->setAttribute('data', $url->getContents());
			$param = $element->createElement('param');
			$param->setAttribute('name', 'movie');
			$param->setAttribute('value', $url->getContents());
		}

		$param = $element->createElement('param');
		$param->setAttribute('name', 'wmode');
		$param->setAttribute('value', 'transparent');

		return $element;
	}

	/**
	 * Flashムービーファイルを返す
	 *
	 * @access public
	 * @param mixed パラメータ配列、BSFile、ファイルパス文字列
	 * @return BSFlashFile Flashムービーファイル
	 * @static
	 */
	static public function getFile ($file) {
		return BSFileUtility::search($file, 'BSFlashFile');
	}
}

/* vim:set tabstop=4: */
