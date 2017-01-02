<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage crypt.cryptor
 */

/**
 * OpenSSL暗号
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @obsoleted
 */
class BSOpenSSLTraditionalCryptor implements BSCryptor {

	/**
	 * @access public
	 */
	public function __construct () {
		if (!extension_loaded('openssl')) {
			throw new BSCryptException('opensslモジュールがロードされていません。');
		}
	}

	/**
	 * 暗号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string 暗号化された文字列
	 */
	public function encrypt ($value) {
		return openssl_encrypt($value, 'AES-128-ECB', BS_CRYPT_SALT, OPENSSL_RAW_DATA);
	}

	/**
	 * 複号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string 複号化された文字列
	 */
	public function decrypt ($value) {
		return openssl_decrypt($value, 'AES-128-ECB', BS_CRYPT_SALT, OPENSSL_RAW_DATA);
	}
}

/* vim:set tabstop=4: */
