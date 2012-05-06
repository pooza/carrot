<?php
/**
 * @package org.carrot-framework
 * @subpackage serialize.storage
 */

/**
 * シリアライズストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSSerializeStorage {

	/**
	 * @access public
	 * @param BSSerializer $serializer
	 */
	public function __construct (BSSerializer $serializer = null);

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize ();

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 * @return string シリアライズされた値
	 */
	public function setAttribute ($name, $value);

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name);

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param BSDate $date 比較する日付 - この日付より古い属性値は破棄
	 * @return mixed 属性値
	 */
	public function getAttribute ($name, BSDate $date = null);

	/**
	 * 属性の更新日を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @return BSDate 更新日
	 */
	public function getUpdateDate ($name);

	/**
	 * クリア
	 *
	 * @access public
	 */
	public function clear ();
}

/* vim:set tabstop=4: */
