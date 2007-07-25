<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * PDOのSQLite用ラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSSQLite.class.php 261 2007-01-03 13:25:18Z pooza $
 */
class BSSQLite extends BSDatabase {
	private static $instance;

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSSQLite インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			try {
				$db = new BSSQLite(self::DSN);
				$db->dsn = self::DSN;
				self::$instance = $db;
			} catch (Exception $e) {
				$e = new BSDatabaseException('DB接続エラーです。 (%s)', $e->getMessage());
				$e->sendNotify();
				throw $e;
			}
		}
		return self::$instance;
	}

	/**
	 * テーブル名のリストを配列で返す
	 *
	 * @access public
	 * @return string[] テーブル名のリスト
	 */
	public function getTableNames () {
		if (!$this->tables) {
			$query = BSSQL::getSelectQueryString(
				'name',
				'sqlite_master',
				'name NOT LIKE \'sqlite_%\'' //システムが使用するテーブルは含めない。
			);
			foreach ($this->query($query) as $row) {
				$this->tables[] = $row['name'];
			}
		}
		return $this->tables;
	}
}

/* vim:set tabstop=4 ai: */
?>