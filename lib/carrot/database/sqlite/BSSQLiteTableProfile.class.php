<?php
/**
 * @package jp.co.b-shock.carrot
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
			$this->fields = BSArray::create();
			$query = 'PRAGMA table_info(' . $this->getName() . ')';
			foreach ($this->getDatabase()->query($query) as $row) {
				$this->fields[$row['name']] = [
					'column_name' => $row['name'],
					'data_type' => BSString::toLower($row['type']),
					'is_nullable' => $row['notnull'],
					'column_default' => $row['dflt_value'],
				];
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
			$this->constraints = BSArray::create();
			$query = 'PRAGMA index_list(' . $this->getName() . ')';
			foreach ($this->getDatabase()->query($query) as $rowKey) {
				$key = [
					'name' => $rowKey['name'],
					'fields' => [],
				];
				$query = 'PRAGMA index_info(' . $rowKey['name'] . ')';
				foreach ($this->getDatabase()->query($query) as $rowField) {
					$key['fields'][] = ['column_name' => $rowField['name']];
				}
				$this->constraints[$rowKey['name']] = $key;
			}
		}
		return $this->constraints;
	}
}

