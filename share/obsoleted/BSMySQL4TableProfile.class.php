<?php
/**
 * @package org.carrot-framework
 * @subpackage database.mysql
 */

/**
 * MySQL4.x以前のテーブルのプロフィール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMySQL4TableProfile extends BSMySQLTableProfile {

	/**
	 * テーブルのフィールドリストを配列で返す
	 *
	 * @access public
	 * @return BSArray フィールドのリスト
	 */
	public function getFields () {
		if (!$this->fields) {
			$this->fields = new BSArray;
			$query = 'DESC `' . $this->getName() . '`';
			foreach ($this->database->query($query) as $row) {
				$this->fields[$row['Field']] = array(
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
	 * @return BSArray 制約のリスト
	 */
	public function getConstraints () {
		if (!$this->constraints) {
			$this->constraints = new BSArray;
			$query = 'SHOW KEYS FROM `' . $this->getName() . '`';
			foreach ($this->database->query($query) as $row) {
				$name = $row['Key_name'];
				if (!$this->constraints[$name]) {
					$this->constraints[$name] = new BSArray;
					$this->constraints[$name]['name'] = $name;
				}
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

/* vim:set tabstop=4: */
