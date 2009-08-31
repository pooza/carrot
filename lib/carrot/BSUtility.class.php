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
	 * @access private
	 */
	private function __construct () {
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
	 * ユニークなIDを生成して返す
	 *
	 * @access public
	 * @return string ユニークなID
	 * @static
	 */
	static public function getUniqueID () {
		return BSCrypt::getSHA1(
			BSDate::getNow('YmdHis') . uniqid(BSNumeric::getRandom(), true)
		);
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
			$file = BSString::stripControlCharacters($file);
			if (!self::isPathAbsolute($file)) {
				$file = BS_LIB_DIR . DIRECTORY_SEPARATOR . $file;
			}
			$file = new BSFile($file);
		}
		if (!$file->isReadable()) {
			throw new BSFileException('"%s"はインクルードできません。', $file);
		}

		if ($config = ini_get('display_errors')) {
			ini_set('display_errors', 0);
		}
		require_once($file->getPath());
		if ($config) {
			ini_set('display_errors', 1);
		}
	}

	/**
	 * オブジェクトメソッドを実行
	 *
	 * @access public
	 * @param object $object オブジェクト
	 * @param string $method 関数名
	 * @param mixed[] $values 引数
	 * @return mixed メソッドの返値
	 * @static
	 */
	static public function executeMethod ($object, $method, $values) {
		if (!method_exists($object, $method)) {
			throw new BSMagicMethodException(
				'クラス"%s"のメソッド"%s"が未定義です。',
				get_class($object),
				$method
			);
		}
		return call_user_func_array(array($object, $method), $values);
	}
}

/* vim:set tabstop=4: */
