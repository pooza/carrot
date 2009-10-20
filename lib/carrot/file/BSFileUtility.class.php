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
}

/* vim:set tabstop=4: */
