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
		if ($file instanceof BSFile) {
			return new BSFlashFile($file->getPath());
		}
		if (BSArray::isArray($file)) {
			$params = new BSArray($file);
			$module = BSController::getInstance()->getModule();
			if ($record = $module->searchRecord($params)) {
				if ($file = $record->getAttachment($params['size'])) {
					return self::getFile($file);
				}
			}
			return null;
		} 
		return new BSFlashFile($file);
	}
}

/* vim:set tabstop=4: */
