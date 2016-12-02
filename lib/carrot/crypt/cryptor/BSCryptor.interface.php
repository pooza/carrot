<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage crypt.cryptor
 */

/**
 * 暗号化器
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSCryptor {

	/**
	 * @access public
	 * @param string $salt ソルト
	 */
	public function __construct ($salt = null);

	/**
	 * 暗号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string 暗号化された文字列
	 */
	public function encrypt ($value);

	/**
	 * 複号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string 複号化された文字列
	 */
	public function decrypt ($value);

	/**
	 * ソルトを返す
	 *
	 * @access public
	 * @return string ソルト
	 */
	public function getSalt ();

	/**
	 * ソルトを設定
	 *
	 * @access public
	 * @param string $salt ソルト
	 */
	public function setSalt ($salt);
}

/* vim:set tabstop=4: */
