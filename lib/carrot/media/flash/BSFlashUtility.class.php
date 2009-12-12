<?php
/**
 * @package org.carrot-framework
 * @subpackage media.flash
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
		$element = new BSFlashObjectElement;
		if ($url) {
			$element->setURL($url);
		}
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
		return BSMediaFile::search($file, 'BSFlashFile');
	}
}

/* vim:set tabstop=4: */
