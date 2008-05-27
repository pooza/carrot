<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.postgresql
 */

/**
 * PostgreSQL接続
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSPostgreSQL extends BSDatabase {
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
			foreach (array('dsn') as $key) {
				if (!defined($const = strtoupper('bs_pdo_' . $name . '_' . $key))) {
					throw new BSDatabaseException('"%s"が未定義です。', $const);
				}
				$$key = constant($const);
			}
			try {
				self::$instances[$name] = new BSPostgreSQL($dsn);
				self::$instances[$name]->setName($name);
			} catch (Exception $e) {
				$e = new BSDatabaseException('DB接続エラーです。 (%s)', $e->getMessage());
				$e->sendAlert();
				throw $e;
			}
		}
		return self::$instances[$name];
	}

	/**
	 * DSNをパースしてプロパティに格納する
	 *
	 * @access protected
	 */
	protected function parseDSN () {
		parent::parseDSN();
		preg_match('/^pgsql:(.+)$/', $this->getDSN(), $matches);
		$this->attributes['port'] = self::getDefaultPort();

		foreach (preg_split('/ +/', $matches[1]) as $config) {
			$config = explode('=', $config);
			$value = $config[1];
			switch ($name = $config[0]) {
				case 'host':
					$this->attributes[$name] = new BSHost($value);
					break;
				case 'dbname':
					$this->attributes['name'] = $value;
					break;
				default:
					$this->attributes[$name] = $value;
					break;
			}
		}
	}

	/**
	 * 命名規則に従い、シーケンス名を返す
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @param string $field 主キーフィールド名
	 * @return string シーケンス名
	 */
	public function getSequenceName ($table, $field = 'id') {
		return implode('_', array($table, $field, 'seq'));
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
				'schemaname=' . $this->quote('public')
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
		$command = array();
		$command[] = '/usr/bin/env pg_dump';
		$command[] = '--host=' . $this->getAttribute('host')->getName();
		$command[] = '--user=' . $this->getAttribute('user');
		$command[] = $this->getName();
		$contents = shell_exec(implode(' ', $command));

		if (!$dir) {
			$dir = BSController::getInstance()->getDirectory('sql');
		}
		$file = $dir->createEntry($filename);
		$file->setContents($contents);
		return $file;
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
		$command = array();
		$command[] = '/usr/bin/env pg_dump';
		$command[] = '--host=' . $this->getAttribute('host')->getName();
		$command[] = '--user=' . $this->getAttribute('user');
		$command[] = '--schema-only';
		$command[] = $this->getName();
		$contents = shell_exec(implode(' ', $command));

		if (!$dir) {
			$dir = BSController::getInstance()->getDirectory('sql');
		}
		$file = $dir->createEntry($filename);
		$file->setContents($contents);
		return $file;
	}

	/**
	 * 最適化する
	 *
	 * @access public
	 */
	public function optimize () {
		$this->exec('VACUUM');
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 * @static
	 */
	static public function getDefaultPort () {
		foreach (array('postgresql', 'postgres', 'pgsql') as $service) {
			if ($port = BSNetworkService::getPort($service)) {
				return $port;
			}
		}
	}
}

/* vim:set tabstop=4 ai: */
?>