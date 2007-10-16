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
				self::$instance = new BSPostgreSQL(self::DSN);
			} catch (Exception $e) {
				$e = new BSDatabaseException('DB接続エラーです。 (%s)', $e->getMessage());
				$e->sendAlert();
				throw $e;
			}
		}
		return self::$instance;
	}

	/**
	 * DSNをパースしてプロパティに格納する
	 *
	 * @access protected
	 */
	protected function parseDSN () {
		parent::parseDSN();
		preg_match('/^pgsql:(.+)$/', $this->getAttribute('dsn'), $matches);
	
		$values = array(
			'host' => null,
			'dbname' => null,
			'user' => null,
			'password' => null,
			'port' => self::getDefaultPort(),
		);
		foreach (preg_split('/ +/', $matches[1]) as $config) {
			$config = explode('=', $config);
			$values[$config[0]] = $config[1];
		}
	
		$this->attributes['host'] = new BSHost($values['host']);
		$this->attributes['port'] = $values['port'];
		$this->attributes['name'] = $values['dbname'];
		$this->attributes['user'] = $values['user'];
		$this->attributes['password'] = $values['password'];
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
	 * ダンプ生成コマンドを返す
	 *
	 * @access protected
	 * @return string ダンプ生成コマンド
	 */
	protected function getDumpCommand () {
		return sprintf(
			'/usr/bin/env pg_dump --host=%s --user=%s %s > %s/%s_%s.sql',
			$this->getAttribute('host')->getName(),
			$this->getAttribute('user'),
			$this->getName(),
			BSController::getInstance()->getPath('dump'),
			$this->getName(),
			BSDate::getNow('Y-m-d')
		);
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