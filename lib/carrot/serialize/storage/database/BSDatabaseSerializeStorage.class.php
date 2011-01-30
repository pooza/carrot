<?php
/**
 * @package org.carrot-framework
 * @subpackage serialize.storage.database
 */

/**
 * データベースシリアライズストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>

 */
class BSDatabaseSerializeStorage implements BSSerializeStorage {
	const TABLE_NAME = 'serialize_entry';
	private $table;

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		try {
			$this->table = BSTableHandler::getInstance(self::TABLE_NAME);
			return true;
		} catch (BSDatabaseException $e) {
			return false;
		}
	}

	/**
	 * シリアライザーを返す
	 *
	 * @access private
	 * @param BSSerializer シリアライザー
	 */
	private function getSerializer () {
		return BSSerializeHandler::getInstance()->getSerializer();
	}

	/**
	 * テーブルを返す
	 *
	 * @access public
	 * @return BSTableHandler テーブル
	 */
	public function getTable () {
		return $this->table;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 * @return string シリアライズされた値
	 */
	public function setAttribute ($name, $value) {
		$serialized = $this->getSerializer()->encode($value);
		$values = array(
			'id' => $name,
			'data' => $serialized,
			'update_date' => BSDate::getNow('Y-m-d H:i:s'),
		);

		if ($record = $this->getTable()->getRecord($name)) {
			$record->update($values);
		} else {
			$this->getTable()->createRecord($values);
		}

		return $serialized;
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		if ($record = $this->getTable()->getRecord($name)) {
			$record->delete();
		}
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param BSDate $date 比較する日付 - この日付より古い属性値は破棄
	 * @return mixed 属性値
	 */
	public function getAttribute ($name, BSDate $date = null) {
		if (!$record = $this->getTable()->getRecord($name)) {
			return null;
		}
		if ($date && $record->getUpdateDate()->isPast($date)) {
			$record->delete();
			return null;
		}
		return $this->getSerializer()->decode($record['data']);
	}

	/**
	 * 属性の更新日を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @return BSDate 更新日
	 */
	public function getUpdateDate ($name) {
		if (!$record = $this->getTable()->getRecord($name)) {
			return null;
		}
		return $record->getUpdateDate();
	}
}

/* vim:set tabstop=4: */
