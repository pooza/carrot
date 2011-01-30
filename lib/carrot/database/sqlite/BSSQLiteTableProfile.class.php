<?php
/**
 * @package org.carrot-framework
 * @subpackage database.sqlite
 */

/**
 * SQLiteテーブルのプロフィール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSQLiteTableProfile extends BSTableProfile {

	/**
	 * テーブルのフィールドリストを配列で返す
	 *
	 * @access public
	 * @return BSArray フィールドのリスト
	 */
	public function getFields () {
		if (!$this->fields) {
			$this->fields = new BSArray;
			$query = 'PRAGMA table_info(' . $this->getName() . ')';
			foreach ($this->getDatabase()->query($query) as $row) {
				$this->fields[$row['name']] = array(
					'column_name' => $row['name'],
					'data_type' => BSString::toLower($row['type']),
					'is_nullable' => $row['notnull'],
					'column_default' => $row['dflt_value'],
				);
			}
		}
		return $this->fields;
	}

	/**
	 * テーブルの制約リストを配列で返す
	 *
	 * @access public
	 * @return BSArray 制約のリスト
	 */
	public function getConstraints () {
		if (!$this->constraints) {
			$this->constraints = new BSArray;
			$query = 'PRAGMA index_list(' . $this->getName() . ')';
			foreach ($this->getDatabase()->query($query) as $rowKey) {
				$key = array(
					'name' => $rowKey['name'],
					'fields' => array(),
				);
				$query = 'PRAGMA index_info(' . $rowKey['name'] . ')';
				foreach ($this->getDatabase()->query($query) as $rowField) {
					$key['fields'][] = array('column_name' => $rowField['name']);
				}
				$this->constraints[$rowKey['name']] = $key;
			}
		}
		return $this->constraints;
	}
}

/* vim:set tabstop=4: */
