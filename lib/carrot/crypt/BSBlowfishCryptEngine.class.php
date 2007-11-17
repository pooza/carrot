<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage crypt
 */

BSController::includeLegacy('/pear/Crypt/Blowfish.php');

/**
 * Blowfish暗号
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSBlowfishCryptEngine extends Crypt_Blowfish implements BSCryptEngine {
	private $salt;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $salt ソルト
	 */
	public function __construct ($salt = null) {
		if (!$salt) {
			if (!defined('BS_CRYPT_SALT')) {
				throw new BSCryptException('BS_CRYPT_SALTが未定義です。');
			}
			$salt = BS_CRYPT_SALT;
		}
		parent::Crypt_Blowfish($salt);
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
	 * ソルトを設定する
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
	 * ソルトを設定する
	 *
	 * @access public
	 * @param string $salt ソルト
	 */
	public function setKey ($salt) {
		$this->salt = $salt;
		return parent::setKey($salt);
	}
}

/* vim:set tabstop=4 ai: */
?>