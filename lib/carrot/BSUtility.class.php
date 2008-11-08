<?php
/**
 * @package org.carrot-framework
 */

/**
 * ユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSUtility {

	/**
	 * 絶対パスか？
	 *
	 * @access public
	 * @param string $path パス
	 * @return boolean 絶対パスならTrue
	 * @static
	 */
	static public function isPathAbsolute ($path) {
		if (strpos($path, '..') !== false) {
			return false;
		} else if ($path[0] == DIRECTORY_SEPARATOR) {
			return true;
		}

		$pattern = '/^[a-z]:' . preg_quote(DIRECTORY_SEPARATOR, '/') . '.+/i';
		if (preg_match($pattern, $path)) {
			return true;
		}

		return false;
	}

	/**
	 * エラーチェックなしでインクルード
	 *
	 * @access public
	 * @param string $path インクルードするファイルのパス、又はBSFileオブジェクト
	 * @static
	 */
	static public function includeFile ($file) {
		if (($file instanceof BSFile) == false) {
			if (!self::isPathAbsolute($file)) {
				$file = BSController::getInstance()->getPath('lib') . DIRECTORY_SEPARATOR . $file;
			}
			$file = new BSFile($file);
		}
		if (!$file->isReadable()) {
			throw new BSFileException('"%s"はインクルード出来ません。', $file);
		}

		if ($config = ini_get('display_errors')) {
			ini_set('display_errors', 0);
		}
		require_once($file->getPath());
		if ($config) {
			ini_set('display_errors', 1);
		}
	}
}

/* vim:set tabstop=4 ai: */
?>