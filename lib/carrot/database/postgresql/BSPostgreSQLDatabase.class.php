<?php
/**
 * @package org.carrot-framework
 * @subpackage database.postgresql
 */

/**
 * PostgreSQLデータベース
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPostgreSQLDatabase extends BSDatabase {

	/**
	 * 接続
	 *
	 * @access protected
	 * @name string $name データベース名
	 * @return BSPostgreSQLDatabase インスタンス
	 * @static
	 */
	static protected function connect ($name) {
		$constants = BSConstantHandler::getInstance();
		$db = new BSPostgreSQLDatabase($constants['PDO_' . $name . '_DSN']);
		$db->setName($name);
		return $db;
	}

	/**
	 * DSNをパースしてプロパティに格納
	 *
	 * @access protected
	 */
	protected function parseDSN () {
		parent::parseDSN();
		preg_match('/^pgsql:(.+)$/', $this->getDSN(), $matches);
		foreach (preg_split('/ +/', $matches[1]) as $config) {
			$config = BSString::explode('=', $config);
			if ($config[0] == 'host') {
				$this->attributes['host'] = new BSHost($config[1]);
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
	 * ダンプファイルを生成
	 *
	 * @access public
	 * @param string $suffix ファイル名サフィックス
	 * @param BSDirectory $dir 出力先ディレクトリ
	 * @return BSFile ダンプファイル
	 */
	public function createDumpFile ($suffix = '_init', BSDirectory $dir = null) {
		$command = $this->getCommandLine('pg_dump');
		if ($command->hasError()) {
			throw new BSDatabaseException($command->getResult());
		}

		if (!$dir) {
			$dir = BSController::getInstance()->getDirectory('sql');
		}
		$file = $dir->createEntry($this->getName() . $suffix . '.sql');
		$file->setContents($command->getResult());
		return $file;
	}

	/**
	 * スキーマファイルを生成
	 *
	 * @access public
	 * @param string $suffix ファイル名サフィックス
	 * @param BSDirectory $dir 出力先ディレクトリ
	 * @return BSFile スキーマファイル
	 */
	public function createSchemaFile ($suffix = '_schema', BSDirectory $dir = null) {
		$command = $this->getCommandLine('pg_dump');
		$command->addValue('--schema-only');
		if ($command->hasError()) {
			throw new BSDatabaseException($command->getResult());
		}

		if (!$dir) {
			$dir = BSController::getInstance()->getDirectory('sql');
		}
		$file = $dir->createEntry($this->getName() . $suffix . '.sql');
		$file->setContents($command->getResult());
		return $file;
	}

	/**
	 * コマンドラインを返す
	 *
	 * @access private
	 * @param string $command コマンド名
	 * @return BSCommandLine コマンドライン
	 */
	private function getCommandLine ($command = 'psql') {
		$command = new BSCommandLine('bin/' . $command);
		$command->setDirectory(BSController::getInstance()->getDirectory('pgsql'));
		$command->addValue('--host=' . $this->getAttribute('host')->getAddress());
		$command->addValue('--user=' . $this->getAttribute('user'));
		$command->addValue($this->getAttribute('name'));
		return $command;
	}

	/**
	 * 最適化
	 *
	 * @access public
	 */
	public function optimize () {
		$this->exec('VACUUM');
	}

	/**
	 * バージョンを返す
	 *
	 * @access public
	 * @return float バージョン
	 */
	public function getVersion () {
		if (!isset($this->attributes['version'])) {
			$result = PDO::query('SELECT version() AS ver')->fetch();
			$this->attributes['version'] = $result['ver'];
		}
		return $this->attributes['version'];
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

/* vim:set tabstop=4: */
