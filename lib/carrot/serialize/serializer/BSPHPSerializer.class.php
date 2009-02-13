<?php
/**
 * @package org.carrot-framework
 * @subpackage serialize.serializer
 */

/**
 * PHPシリアライザー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPHPSerializer implements BSSerializer {

	/**
	 * 利用可能か？
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function isEnable () {
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
}

/* vim:set tabstop=4: */
