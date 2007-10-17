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
	 * ダンプファイルを生成する
	 *
	 * @access public
	 * @param string $filename ファイル名
	 * @param BSDirectory $dir 出力先ディレクトリ
	 * @return BSFile ダンプファイル
	 */
	public function createDumpFile ($filename = 'init', BSDirectory $dir = null) {
		if (!$dir) {
			$dir = BSController::getInstance()->getDirectory('sql');
		}

		$command = array();
		$command[] = '/usr/bin/env pg_dump';
		$command[] = '--host=' . $this->getAttribute('host')->getAddress();
		$command[] = '--user=' . $this->getAttribute('user');
		$command[] = $this->getName();
		$command[] = '>';
		$command[] = $dir->getPath() . '/' . $filename . $dir->getSuffix();
		$command = implode(' ', $command);
		shell_exec($command);

		return $dir->getEntry($filename);
	}

	/**
	 * スキーマファイルを生成する
	 *
	 * @access public
	 * @param string $filename ファイル名
	 * @param BSDirectory $dir 出力先ディレクトリ
	 * @return BSFile スキーマファイル
	 */
	public function createSchemaFile ($filename = 'schema', BSDirectory $dir = null) {
		if (!$dir) {
			$dir = BSController::getInstance()->getDirectory('sql');
		}

		$command = array();
		$command[] = '/usr/bin/env pg_dump';
		$command[] = '--host=' . $this->getAttribute('host')->getAddress();
		$command[] = '--user=' . $this->getAttribute('user');
		$command[] = '--schema-only';
		$command[] = $this->getName();
		$command[] = '>';
		$command[] = $dir->getPath() . '/' . $filename . $dir->getSuffix();
		$command = implode(' ', $command);
		shell_exec($command);

		return $dir->getEntry($filename);
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