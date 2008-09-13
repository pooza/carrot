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
	 * インスタンスを生成して返す
	 *
	 * @access public
	 * @name string $name データベース名
	 * @return BSDatabase インスタンス
	 * @static
	 */
	static public function getInstance ($name = 'default') {
		try {
			$constants = BSConstantHandler::getInstance();
			$db = new BSODBCDatabase(
				$constants['PDO_' . $name . '_DSN'],
				$constants['PDO_' . $name . '_UID'],
				$constants['PDO_' . $name . '_PASSWORD']
			);
			$db->setName($name);
		} catch (Exception $e) {
			$e = new BSDatabaseException('DB接続エラーです。 (%s)', $e->getMessage());
			$e->sendAlert();
			throw $e;
		}
		return $db;
	}

	/**
	 * 文字列をクォート
	 *
	 * @access public
	 * @param string $string 対象文字列
	 * @param string $type クォートのタイプ
	 * @return string クォート後の文字列
	 */
	public function quote ($string, $type = PDO::PARAM_STR) {
		return '\'' . addslashes($string) . '\'';
	}

	/**
	 * テーブル名のリストを配列で返す
	 *
	 * @access public
	 * @return string[] テーブル名のリスト
	 */
	public function getTableNames () {
		// ODBC接続では、テーブル名のリストを返せない
		return array();
	}
}

/* vim:set tabstop=4 ai: */
?>