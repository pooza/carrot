<?php
/**
 * @package org.carrot-framework
 * @subpackage serialize.serializer
 */

/**
 * JSONシリアライザー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJSONSerializer implements BSSerializer {

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		return extension_loaded('json');
	}

	/**
	 * シリアライズされた文字列を返す
	 *
	 * @access public
	 * @param mixed $value 対象
	 * @return string シリアライズされた文字列
	 */
	public function encode ($value) {
		$value = BSString::convertEncoding($value, 'utf-8');
		return json_encode($value);
	}

	/**
	 * シリアライズされた文字列を元に戻す
	 *
	 * @access public
	 * @param string $value 対象
	 * @return mixed もとの値
	 */
	public function decode ($value) {
		return json_decode($value, true);
	}

	/**
	 * サフィックスを返す
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getSuffix () {
		return '.json';
	}
}

/* vim:set tabstop=4: */
