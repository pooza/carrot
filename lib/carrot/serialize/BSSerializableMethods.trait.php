<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.table
 */

/**
 * シリアライズ可能なオブジェクト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
trait BSSerializableMethods {

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized () {
		return $this->controller->getAttribute($this);
	}

	/**
	 * シリアライズされたキャッシュを削除
	 *
	 * @access public
	 */
	public function removeSerialized () {
		$this->controller->removeAttribute($this);
	}
}

