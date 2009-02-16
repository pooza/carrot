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
			if (!$db = BSDatabase::getInstance('session')) {
				throw new BSSessionException('セッションデータベースが正しくありません。');
			}
			if (!$db->getTableNames()->isIncluded(self::TABLE_NAME)) {
				$fields = array(
					'id varchar(128) NOT NULL PRIMARY KEY',
					'update_date timestamp NOT NULL',
					'data TEXT',
				);
				$query = BSSQL::getCreateTableQueryString(self::TABLE_NAME, $fields);
				$db->exec($query);
			}
			$class = BSTableHandler::getClassName(self::TABLE_NAME);
			$this->table = new $class;
		}
		return $this->table;
	}
}

/* vim:set tabstop=4: */
