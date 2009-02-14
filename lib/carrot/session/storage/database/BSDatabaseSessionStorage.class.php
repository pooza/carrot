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
	 */
	public function initialize () {
		session_set_save_handler(
			array($this->getTable(), 'open'),
			array($this->getTable(), 'close'),
			array($this->getTable(), 'getAttribute'),
			array($this->getTable(), 'setAttribute'),
			array($this->getTable(), 'removeAttribute'),
			array($this->getTable(), 'clean')
		);
	}

	/**
	 * ストレージテーブルを返す
	 *
	 * テーブルが存在しなければ、作成しようとする。
	 *
	 * @access public
	 * @return BSTableHandler ストレージテーブル
	 */
	public function getTable () {
		if (!$this->table) {
			if (!$this->getDatabase()->getTableNames()->isIncluded(self::TABLE_NAME)) {
				$fields = array(
					'id varchar(128) NOT NULL PRIMARY KEY',
					'update_date timestamp NOT NULL',
					'data TEXT',
				);
				$query = BSSQL::getCreateTableQueryString(self::TABLE_NAME, $fields);
				$this->getDatabase()->exec($query);
			}
			$class = 'BS' . BSTableHandler::getClassName(self::TABLE_NAME);
			$this->table = new $class;
		}
		return $this->table;
	}

	/**
	 * セッションデータベースを返す
	 *
	 * @access public
	 * @return BSDatabase セッションデータベース
	 */
	public function getDatabase () {
		foreach (array('session', 'default') as $db) {
			if ($db = BSDatabase::getInstance($db)) {
				return $db;
			}
		}
		throw new BSSessionException('セッションデータベースが正しくありません。');
	}
}

/* vim:set tabstop=4: */
