<?php
/**
 * @package org.carrot-framework
 * @subpackage session.storage.database
 */

/**
 * データベースセッションストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDatabaseSessionStorage implements BSSessionStorage {
	const TABLE_NAME = 'stored_session';
	private $table;

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		try {
			parent::initialize();
			$table = $this->getTable();
			if (!$table->getDatabase()->getTableNames()->isIncluded($table->getName())) {
				$fields = array(
					'id' => 'varchar(128) NOT NULL PRIMARY KEY',
					'update_date' => 'timestamp NOT NULL',
					'data' => 'TEXT',
				);
				$query = BSSQL::getCreateTableQueryString($table->getName(), $fields);
				$table->getDatabase()->exec($query);
			}
			return session_set_save_handler(
				array($table, 'open'),
				array($table, 'close'),
				array($table, 'getAttribute'),
				array($table, 'setAttribute'),
				array($table, 'removeAttribute'),
				array($table, 'clean')
			);
		} catch (BSDatabaseException $e) {
			return false;
		}
	}

	/**
	 * テーブルを返す
	 *
	 * @access public
	 * @return BSTableHandler テーブル
	 */
	public function getTable () {
		if (!$this->table) {
			$this->table = BSTableHandler::getInstance(self::TABLE_NAME);
		}
		return $this->table;
	}
}

/* vim:set tabstop=4: */
