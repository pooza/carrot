<?php
/**
 * @package org.carrot-framework
 * @subpackage serialize.serializer
 */

/**
 * PHPシリアライザー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPHPSerializer implements BSSerializer {

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		return true;
	}

	/**
	 * シリアライズされた文字列を返す
	 *
	 * @access public
	 * @param mixed $value 対象
	 * @return string シリアライズされた文字列
	 */
	public function encode ($value) {
		return serialize($value);
	}

	/**
	 * シリアライズされた文字列を元に戻す
	 *
	 * @access public
	 * @param string $value 対象
	 * @return mixed もとの値
	 */
	public function decode ($value) {
		return unserialize($value);
	}

	/**
	 * サフィックスを返す
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getSuffix () {
		return '.serialized';
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return 'PHPシリアライザー';
	}
}

/* vim:set tabstop=4: */
