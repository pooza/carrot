<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * PDOのDOBCデータベース用ラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSODBCDatabase extends BSDatabase {
	private static $instance;

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSODBCDatabase インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			try {
				self::$instance = new BSODBCDatabase(self::DSN, self::UID, self::PASSWORD);
			} catch (Exception $e) {
				$e = new BSDatabaseException(
					'DB接続エラーです。DSN:[%s] (%s)',
					BSString::convertEncoding($e->getMessage()),
					self::DSN
				);
				throw $e;
			}
		}
		return self::$instance;
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