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
