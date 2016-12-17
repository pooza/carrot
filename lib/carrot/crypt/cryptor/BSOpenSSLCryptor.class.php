<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage crypt.cryptor
 */

/**
 * OpenSSL暗号
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSOpenSSLCryptor implements BSCryptor {
	private $salt;

	/**
	 * @access public
	 * @param string $salt ソルト
	 */
	public function __construct ($salt = BS_CRYPT_SALT) {
		if (!extension_loaded('openssl')) {
			throw new BSCryptException('opensslモジュールがロードされていません。');
		}
		$this->setSalt($salt);
	}

	/**
	 * 暗号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string 暗号化された文字列
	 */
	public function encrypt ($value) {
		return openssl_encrypt($value, BS_CRYPT_METHOD, $this->getSalt(), true);
	}

	/**
	 * 複号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string 複号化された文字列
	 */
	public function decrypt ($value) {
		return openssl_decrypt($value, BS_CRYPT_METHOD, $this->getSalt(), true);
	}

	/**
	 * ソルトを返す
	 *
	 * @access public
	 * @return string ソルト
	 */
	public function getSalt () {
		return $this->salt;
	}

	/**
	 * ソルトを設定
	 *
	 * @access public
	 * @param string $salt ソルト
	 */
	public function setSalt ($salt) {
		$this->salt = $salt;
	}
}

/* vim:set tabstop=4: */
