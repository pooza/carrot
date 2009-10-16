<?php
/**
 * @package org.carrot-framework
 * @subpackage file
 */

ini_set('auto_detect_line_endings', true);

/**
 * ファイルユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFileUtility {

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * 一時ファイルを生成して返す
	 *
	 * @access public
	 * @param string $suffix 拡張子
	 * @param string $class クラス名
	 * @return BSFile 一時ファイル
	 * @static
	 */
	static public function getTemporaryFile ($suffix = null, $class = 'BSFile') {
		$dir = BSController::getInstance()->getDirectory('tmp');
		$name = BSUtility::getUniqueID() . $suffix;
		if (!$file = $dir->createEntry($name, $class)) {
			throw new BSFileException('一時ファイルが生成できません。');
		}
		return $file;
	}

	/**
	 * 探す
	 *
	 * @access public
	 * @param mixed パラメータ配列、BSFile、ファイルパス文字列
	 * @return BSFile ファイル
	 * @static
	 */
	static public function search ($file, $class = 'BSFile') {
		if ($file instanceof BSFile) {
			return new $class($file->getPath());
		}
		if (BSArray::isArray($file)) {
			$params = new BSArray($file);
			if ($path = $params['src']) {
				return self::getFile($path);
			}
			$module = BSController::getInstance()->getModule();
			if ($record = $module->searchRecord($params)) {
				if ($file = $record->getAttachment($params['size'])) {
					return self::search($file, $class);
				}
			}
			return null;
		} 

		if (BSUtility::isPathAbsolute($path = $file)) {
			return new BSMovieFile($path);
		} else {
			foreach (array('carrotlib', 'www', 'root') as $dir) {
				$dir = BSController::getInstance()->getDirectory($dir);
				if ($entry = $dir->getEntry($path, $class)) {
					return $entry;
				}
			}
		}
	}
}

/* vim:set tabstop=4: */
