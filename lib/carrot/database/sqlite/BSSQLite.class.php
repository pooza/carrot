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
 * @version $Id: BSSQLite.class.php 230 2008-04-22 04:31:27Z pooza $
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
				self::$instance = new BSSQLite(self::DSN);
			} catch (Exception $e) {
				$e = new BSDatabaseException('DB接続エラーです。 (%s)', $e->getMessage());
				$e->sendAlert();
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
				'name NOT LIKE ' . BSSQL::quote('sqlite_%')
			);
			foreach ($this->query($query) as $row) {
				$this->tables[] = $row['name'];
			}
		}
		return $this->tables;
	}

	/**
	 * 一時テーブルを生成して返す
	 *
	 * @access public
	 * @param string[] $details フィールド定義等
	 * @param string $class クラス名
	 * @return BSTemporaryTableHandler 一時テーブル
	 */
	public function getTemporaryTable ($details, $class = 'BSTemporaryTableHandler') {
		$table = new $class;
		$query = sprintf(
			'CREATE TEMPORARY TABLE %s (%s)',
			$table->getName(),
			implode(',', $details)
		);
		$this->exec($query);
		return $table;
	}

	/**
	 * 最適化する
	 *
	 * @access public
	 */
	public function optimize () {
		$this->exec('VACUUM');
	}
}

/* vim:set tabstop=4 ai: */
?>