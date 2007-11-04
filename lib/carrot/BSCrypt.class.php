<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * 暗号化に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSCrypt {

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// インスタンス化は禁止
	}

	/**
	 * md5ダイジェストを返す
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @return string ダイジェスト文字列
	 * @static
	 */
	public static function getMD5 ($str) {
		if (extension_loaded('hash')) {
			return hash('md5', $str);
		} else {
			return md5($str);
		}
	}

	/**
	 * sha1ダイジェストを返す
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @return string ダイジェスト文字列
	 * @static
	 */
	public static function getSHA1 ($str) {
		if (extension_loaded('hash')) {
			return hash('sha1', $str);
		} else {
			return sha1($str);
		}
	}

	/**
	 * RC4で暗号化/複号した文字列を返す
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @return string 暗号化/複号された文字列
	 * @static
	 */
	public static function getRC4 ($str) {
		BSController::includeLegacy('/pear/Crypt/Rc4.php');
		$engine = new Crypt_RC4;
		$key = BS_CRYPT_SALT;
		@$engine->key($key);
		@$engine->crypt($str);
		return $str;
	}
}

/* vim:set tabstop=4 ai: */
?>