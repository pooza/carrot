<?php
/**
 * @package org.carrot-framework
 * @subpackage database.mysql
 */

/**
 * MySQL4.0テーブルのプロフィール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSMySQL40TableProfile extends BSMySQLTableProfile {

	/**
	 * テーブルのフィールドリストを配列で返す
	 *
	 * @access public
	 * @return string[][] フィールドのリスト
	 */
	public function getFields () {
		if (!$this->fields) {
			$query = 'DESC `' . $this->getName() . '`';
			foreach ($this->database->query($query) as $row) {
				$this->fields[] = array(
					'column_name' => $row['Field'],
					'data_type' => $row['Type'],
					'is_nullable' => $row['Null'],
					'column_default' => $row['Default'],
				);
			}
		}
		return $this->fields;
	}

	/**
	 * テーブルの制約リストを配列で返す
	 *
	 * @access public
	 * @return string[][] 制約のリスト
	 */
	public function getConstraints () {
		if (!$this->constraints) {
			$query = 'SHOW KEYS FROM `' . $this->getName() . '`';
			foreach ($this->database->query($query) as $row) {
				$name = $row['Key_name'];
				$this->constraints[$name]['name'] = $name;
				$this->constraints[$name]['fields'][] = array(
					'column_name' => $row['Column_name'],
				);

				if ($name == 'PRIMARY') {
					$this->constraints[$name]['type'] = 'PRIMARY KEY';
				} else if (!$row['Non_unique']) {
					$this->constraints[$name]['type'] = 'UNIQUE';
				}
			}
		}
		return $this->constraints;
	}
}

/* vim:set tabstop=4 ai: */
?>