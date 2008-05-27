<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.odbc
 */

/**
 * DOBCデータベース接続
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSODBCDatabase extends BSDatabase {
	static private $instances;

	/**
	 * フライウェイトインスタンスを返す
	 *
	 * @access public
	 * @name string $name データベース名
	 * @return BSDatabase インスタンス
	 * @static
	 */
	static public function getInstance ($name = 'default') {
		if (!self::$instances) {
			self::$instances = new BSArray;
		}
		if (!self::$instances[$name]) {
			foreach (array('dsn', 'uid', 'password') as $key) {
				if (!defined($const = strtoupper('bs_pdo_' . $name . '_' . $key))) {
					throw new BSDatabaseException('"%s"が未定義です。', $const);
				}
				$$key = constant($const);
			}
			try {
				self::$instances[$name] = new BSODBCDatabase($dsn, $uid, $password);
				self::$instances[$name]->setName($name);
			} catch (Exception $e) {
				$e = new BSDatabaseException(
					'DB接続エラーです。 (%s)',
					BSString::convertEncoding($e->getMessage())
				);
				throw $e;
			}
		}
		return self::$instances[$name];
	}

	/**
	 * 文字列をクォートする
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