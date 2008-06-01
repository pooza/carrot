<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.mysql
 */

/**
 * MySQLテーブルのプロフィール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSMySQLTableProfile extends BSTableProfile {

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return string[][] 全ての属性
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = array(
				'dsn' => $this->getDatabase()->getDSN(),
				'name' => $this->getName(),
			);

			$fields = array('Engine', 'Row_format', 'Collation');
			$query = sprintf(
				'SHOW TABLE STATUS LIKE %s',
				$this->getDatabase()->quote($this->getName())
			);
			foreach ($this->getDatabase()->query($query)->fetch() as $key => $value) {
				if (in_array($key, $fields)) {
					$this->attributes[strtolower($key)] = $value;
				}
			}
		}
		return $this->attributes;
	}

	/**
	 * テーブルのフィールドリストを配列で返す
	 *
	 * @access public
	 * @return string[][] フィールドのリスト
	 */
	public function getFields () {
		if (!$this->fields) {
			$query = 'DESC ' . $this->getName();
			foreach ($this->getDatabase()->query($query) as $row) {
				$fields[] = array(
					'name' => $row['Field'],
					'type' => $row['Type'],
					'notnull' => ($row['Null'] == 'NO'),
					'default' => $row['Default'],
					'primarykey' => ($row['Key'] == 'PRI'),
					'extra' => $row['Extra'],
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
			$query = 'SHOW KEYS FROM ' . $this->getName();
			foreach ($this->getDatabase()->query($query) as $row) {
				$this->keys[$row['Key_name']]['name'] = $row['Key_name'];
				$this->keys[$row['Key_name']]['fields'][] = $row['Column_name'];
				$this->keys[$row['Key_name']]['unique'] = ($row['Non_unique'] == 0);
			}
		}
		return $this->keys;
	}
}

/* vim:set tabstop=4 ai: */
?>