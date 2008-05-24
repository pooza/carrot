<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.sqlite
 */

/**
 * SQLiteテーブルのプロフィール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSQLiteTableProfile extends BSTableProfile {

	/**
	 * テーブルのフィールドリストを配列で返す
	 *
	 * @access public
	 * @return string[][] フィールドのリスト
	 */
	public function getFields () {
		if (!$this->fields) {
			$fields = array();
			$query = 'PRAGMA table_info(' . $this->getName() . ')';
			foreach ($this->database->query($query) as $row) {
				$fields[] = array(
					'name' => $row['name'],
					'type' => strtolower($row['type']),
					'notnull' => $row['notnull'],
					'default' => $row['dflt_value'],
					'primarykey' => $row['pk'],
					'extra' => null,
				);
			}
			$this->fields = $fields;
		}
		return $this->fields;
	}

	/**
	 * テーブルのキーリストを配列で返す
	 *
	 * @access public
	 * @return string[][] キーのリスト
	 */
	public function getKeys () {
		if (!$this->keys) {
			$query = 'PRAGMA index_list(' . $this->getName() . ')';
			foreach ($this->database->query($query) as $rowKey) {
				$key = array(
					'name' => $rowKey['name'],
					'fields' => array(),
					'unique' => $rowKey['unique'],
				);
				$query = 'PRAGMA index_info(' . $rowKey['name'] . ')';
				foreach ($this->database->query($query) as $rowField) {
					$key['fields'][] = $rowField['name'];
				}
				$this->keys[] = $key;
			}
		}
		return $this->keys;
	}
}

/* vim:set tabstop=4 ai: */
?>