<?php
/**
 * @package org.carrot-framework
 * @subpackage serialize
 */

/**
 * シリアライズ可能なオブジェクトへのインターフェース
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSSerializable {

	/**
	 * シリアライズのダイジェストを返す
	 *
	 * @access public
	 * @return string 属性名
	 */
	public function digestSerialized ();

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized ();

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize ();

	/**
	 * @access public
	 */
	public function __toString ();
}

/* vim:set tabstop=4: */
