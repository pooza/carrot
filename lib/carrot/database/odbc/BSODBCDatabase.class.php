<?php
/**
 * @package org.carrot-framework
 * @subpackage database.odbc
 */

/**
 * DOBCデータベース接続
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSODBCDatabase extends BSDatabase {

	/**
	 * 接続
	 *
	 * @access protected
	 * @name string $name データベース名
	 * @return BSODBCDatabase インスタンス
	 * @static
	 */
	static protected function connect ($name) {
		foreach (self::getPasswords($name) as $password) {
			try {
				$db = new BSODBCDatabase(
					$constants['PDO_' . $name . '_DSN'],
					$constants['PDO_' . $name . '_UID'],
					$password
				);
				$db->setName($name);
				return $db;
			} catch (Exception $e) {
			}
		}

		$message = new BSStringFormat('データベース "%s" に接続できません。');
		$message[] = $name;
		throw new BSDatabaseException($message);
	}

	/**
	 * テーブル名のリストを配列で返す
	 *
	 * @access public
	 * @return BSArray テーブル名のリスト
	 */
	public function getTableNames () {
		return new BSArray;
	}
}

/* vim:set tabstop=4: */
