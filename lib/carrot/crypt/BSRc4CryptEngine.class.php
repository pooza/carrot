<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage crypt
 */

BSController::includeLegacy('/pear/Crypt/Rc4.php');

/**
 * RC4暗号
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSRc4CryptEngine extends Crypt_RC4 implements BSCryptEngine {

	/**
	 * コンストラクタ
	 *
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
	 * @todo $valueを参照渡しにしないとStrict standardエラーが出るが、とりあえず放置。
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
	 * ソルトを設定する
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