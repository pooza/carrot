<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * PDOのPostgreSQL用ラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSPostgreSQL extends BSDatabase {
	private static $instance;

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSMySQL インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			try {
				$db = new BSPostgreSQL(self::DSN);
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
				'tablename',
				'pg_tables',
				'schemaname=' . BSSQL::quote('public')
			);
			foreach ($this->query($query) as $row) {
				$this->tables[] = $row['tablename'];
			}
		}
		return $this->tables;
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 * @static
	 */
	public static function getDefaultPort () {
		foreach (array('postgresql', 'postgres', 'pgsql') as $service) {
			if ($port = BSServiceList::getPort($service)) {
				return $port;
			}
		}
	}

	/**
	 * DSNスキーマを返す
	 *
	 * @access public
	 * @return string DSNスキーマ
	 */
	public function getScheme () {
		return 'pgsql';
	}
}

/* vim:set tabstop=4 ai: */
?>