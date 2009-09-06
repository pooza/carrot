<?php
/**
 * @package org.carrot-framework
 * @subpackage database.mysql
 */

/**
 * MySQLテーブルのプロフィール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMySQLTableProfile extends BSTableProfile {

	/**
	 * @access public
	 * @param string $table テーブル名
	 */
	public function __construct ($table, BSDatabase $database = null) {
		if (mb_ereg('^`([a-z0-9_]+)`$', $table, $matches)) {
			$table = $matches[1];
		}
		parent::__construct($table, $database);
	}

	/**
	 * テーブルのフィールドリストを配列で返す
	 *
	 * @access public
	 * @return string[][] フィールドのリスト
	 */
	public function getFields () {
		if (!$this->fields) {
			$query = BSSQL::getSelectQueryString(
				'column_name,data_type,character_maximum_length,is_nullable,column_default,extra',
				'information_schema.columns',
				$this->getCriteria(),
				'ordinal_position'
			);
			$this->fields = $this->database->query($query)->fetchAll();
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
			$query = BSSQL::getSelectQueryString(
				'constraint_name AS name,constraint_type AS type',
				'information_schema.table_constraints',
				$this->getCriteria(),
				'constraint_type=' . $this->database->quote('PRIMARY KEY') . ',name'
			);
			foreach ($this->database->query($query)->fetchAll() as $row) {
				$criteria = $this->getCriteria();
				$criteria[] = 'constraint_name=' . $this->database->quote($row['name']);
				$query = BSSQL::getSelectQueryString(
					'column_name,referenced_table_name,referenced_column_name',
					'information_schema.key_column_usage',
					$criteria,
					'ordinal_position'
				);
				$row['fields'] = $this->database->query($query)->fetchAll();
				$this->constraints[$row['name']] = $row;
			}
		}
		return $this->constraints;
	}

	/**
	 * 抽出条件を返す
	 *
	 * @access protected
	 * @return string[] 抽出条件
	 */
	protected function getCriteria () {
		return array(
			'table_schema=' . $this->database->quote($this->database['database_name']),
			'table_name=' . $this->database->quote($this->getName()),
		);
	}
}

/* vim:set tabstop=4: */
