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
	const TABLE_NAME = 'session_entry';
	private $table;

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		session_write_close();
		try {
			return session_set_save_handler(
				array($this->getTable(), 'open'),
				array($this->getTable(), 'close'),
				array($this->getTable(), 'getAttribute'),
				array($this->getTable(), 'setAttribute'),
				array($this->getTable(), 'removeAttribute'),
				array($this->getTable(), 'clean')
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
