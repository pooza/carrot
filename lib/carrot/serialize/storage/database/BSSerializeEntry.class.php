<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage serialize.storage.database
 */

/**
 * ストアドシリアライズレコード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSerializeEntry extends BSRecord {

	/**
	 * 更新可能か？
	 *
	 * @access protected
	 * @return boolean 更新可能ならTrue
	 */
	protected function isUpdatable () {
		return true;
	}

	/**
	 * 更新
	 *
	 * @access public
	 * @param mixed $values 更新する値
	 * @param integer $flags フラグのビット列
	 *   BSDatabase::WITHOUT_LOGGING ログを残さない
	 */
	public function update ($values, $flags = BSDatabase::WITHOUT_LOGGING) {
		parent::update($values, $flags);
	}

	/**
	 * 削除可能か？
	 *
	 * @access protected
	 * @return boolean 削除可能ならTrue
	 */
	protected function isDeletable () {
		return true;
	}

	/**
	 * シリアライズするか？
	 *
	 * @access public
	 * @return boolean シリアライズするならTrue
	 * @final
	 */
	final function isSerializable () {
		return false;
	}
}

