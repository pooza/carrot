<?php
/**
 * @package org.carrot-framework
 * @subpackage crypt
 */

BSUtility::includeFile('pear/Crypt/Rc4.php');

/**
 * RC4暗号
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSRc4CryptEngine extends Crypt_RC4 implements BSCryptEngine {

	/**
	 * @access public
	 * @param string $salt ソルト
	 */
	public function __construct ($salt = null) {
		if (!$salt) {
			$salt = BSCrypt::getDefaultSalt();
		}
		parent::Crypt_RC4();
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
		@$this->crypt($value);
		return $value;
	}

	/**
	 * 複号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string 複号化された文字列
	 */
	public function decrypt ($value) {
		@parent::decrypt($value);
		return $value;
	}

	/**
	 * ソルトを返す
	 *
	 * @access public
	 * @return string ソルト
	 */
	public function getSalt () {
		return $this->_key;
	}

	/**
	 * ソルトを設定
	 *
	 * keyのエイリアス
	 *
	 * @access public
	 * @param string $salt ソルト
	 */
	public function setSalt ($salt) {
		return @$this->key($salt);
	}
}

/* vim:set tabstop=4 ai: */
?>