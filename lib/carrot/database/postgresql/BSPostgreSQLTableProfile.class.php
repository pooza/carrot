<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * PostgreSQLテーブルのプロフィール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSPostgreSQLTableProfile.class.php 230 2008-04-22 04:31:27Z pooza $
 */
class BSPostgreSQLTableProfile extends BSTableProfile {
	private $id;
	private $types = array();

	/**
	 * テーブルのフィールドリストを配列で返す
	 *
	 * @access public
	 * @return string[][] フィールドのリスト
	 */
	public function getFields () {
		if (!$this->fields) {
			$query = BSSQL::getSelectQueryString(
				array('attname', 'atttypid', 'attnotnull', 'atttypmod'),
				'pg_attribute',
				array('attrelid=' . BSSQL::quote($this->getID()), 'attnum>0')
			);
			foreach ($this->database->query($query) as $row) {
				$this->fields[] = array(
					'name' => $row['attname'],
					'type' => $this->getType($row['atttypid'], $row['atttypmod']),
					'notnull' => $row['attnotnull'],
				);
			}
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
			$this->keys = array();
		}
		return $this->keys;
	}

	private function getID () {
		if (!$this->id) {
			$query = BSSQL::getSelectQueryString(
				'oid', 'pg_class', 'relname=' . BSSQL::quote($this->getName())
			);
			$row = $this->database->query($query)->fetch();
			$this->id = $row['oid'];
		}
		return $this->id;
	}

	private function getType ($id, $mod = null) {
		$types = $this->getTypes();
		if (isset($types[$id])) {
			$type = $types[$id];
			if ($mod != -1) {
				$type .= '(' . $mod . ')';
			}
			return $type;
		}
	}

	private function getTypes () {
		if (!$this->types) {
			$query = BSSQL::getSelectQueryString(array('oid', 'typname'), 'pg_type');
			foreach ($this->database->query($query) as $row) {
				$this->types[$row['oid']] = $row['typname'];
			}
		}
		return $this->types;
	}
}

/* vim:set tabstop=4 ai: */
?>