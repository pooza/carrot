<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * ユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSUtility {

	/**
	 * ファイル名からクラス名を返す
	 *
	 * @access public
	 * @param string $filename ファイル名
	 * @return string クラス名
	 * @static
	 */
	static public function extractClassName ($filename) {
		if (self::isPathAbsolute($filename)) {
			$filename = basename($filename);
		}
		if (preg_match('/(.*?)\.(class|interface)\.php/', $filename, $matches)) {
			return $matches[1];
		}
	}

	/**
	 * 絶対パスか？
	 *
	 * @access public
	 * @param string $path パス
	 * @return boolean 絶対パスならTrue
	 * @static
	 */
	static public function isPathAbsolute ($path) {
		if ($path[0] == DIRECTORY_SEPARATOR) {
			return true;
		}

		$pattern = '/^[a-zA-Z]:' . preg_quote(DIRECTORY_SEPARATOR, '/') . '.+/';
		if (preg_match($pattern, $path)) {
			return true;
		}

		return false;
	}
}

/* vim:set tabstop=4 ai: */
?>