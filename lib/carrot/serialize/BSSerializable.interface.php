<?php
/**
 * @package org.carrot-framework
 * @subpackage serialize
 */

/**
 * シリアライズ可能なオブジェクトへのインターフェース
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
interface BSSerializable {

	/**
	 * シリアライズ時の属性名を返す
	 *
	 * @access public
	 * @return string シリアライズ時の属性名
	 */
	public function getSerializedName ();

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
}

/* vim:set tabstop=4: */
