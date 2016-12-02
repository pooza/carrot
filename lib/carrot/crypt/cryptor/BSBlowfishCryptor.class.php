<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage crypt.cryptor
 */

BSUtility::includeFile('pear/Crypt/Blowfish');

/**
 * Blowfish暗号
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSBlowfishCryptor extends Crypt_Blowfish implements BSCryptor {
	private $salt;

	/**
	 * @access public
	 * @param string $salt ソルト
	 */
	public function __construct ($salt = BS_CRYPT_SALT) {
		parent::Crypt_Blowfish($salt);
	}

	/**
	 * 暗号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string 暗号化された文字列
	 */
	public function encrypt ($value) {
		if (BSString::isBlank($value)) {
			return; // 空文字列をCrypt_Blowfishに渡すと、E_NOTICEが発生する。
		}
		return parent::encrypt($value);
	}

	/**
	 * 複号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string 複号化された文字列
	 */
	public function decrypt ($value) {
		if (BSString::isBlank($value)) {
			return; // 空文字列をCrypt_Blowfishに渡すと、E_NOTICEが発生する。
		}
		return parent::decrypt($value);
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
	 * setKeyのエイリアス
	 *
	 * @access public
	 * @param string $salt ソルト
	 */
	public function setSalt ($salt) {
		$this->setKey($salt);
	}

	/**
	 * ソルトを設定
	 *
	 * @access public
	 * @param string $salt ソルト
	 */
	public function setKey ($salt) {
		$this->salt = $salt;
		return parent::setKey($salt);
	}
}

/* vim:set tabstop=4: */
