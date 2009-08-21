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
			if ($path = $params['src']) {
				return self::getFile($path);
			}
			$module = BSController::getInstance()->getModule();
			if ($record = $module->searchRecord($params)) {
				if ($file = $record->getAttachment($params['size'])) {
					return self::getFile($file);
				}
			}
			return null;
		} 

		if (BSUtility::isPathAbsolute($path = $file)) {
			return new BSFlashFile($path);
		} else {
			foreach (array('carrotlib', 'www', 'root') as $dir) {
				$dir = BSController::getInstance()->getDirectory($dir);
				if ($entry = $dir->getEntry($path, 'BSFlashFile')) {
					return $entry;
				}
			}
		}
	}
}

/* vim:set tabstop=4: */
