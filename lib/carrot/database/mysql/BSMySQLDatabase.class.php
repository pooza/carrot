<?php
/**
 * @package org.carrot-framework
 * @subpackage database.mysql
 */

/**
 * MySQLデータベース
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMySQLDatabase extends BSDatabase {
	static private $configFile;

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
			if ($file = self::getConfigFile()) {
				$db = new BSMySQLDatabase(
					$constants['PDO_' . $name . '_DSN'],
					$constants['PDO_' . $name . '_UID'],
					$constants['PDO_' . $name . '_PASSWORD'],
					array(self::MYSQL_ATTR_READ_DEFAULT_FILE => $file->getPath())
				);
			} else {
				$db = new BSMySQLDatabase(
					$constants['PDO_' . $name . '_DSN'],
					$constants['PDO_' . $name . '_UID'],
					$constants['PDO_' . $name . '_PASSWORD']
				);
				if (!$db->isLegacy()) {
					$db->exec('SET NAMES ' . $db->getEncodingName());
				}
			}
			$db->setName($name);
		} catch (Exception $e) {
			$e = new BSDatabaseException('DB接続エラーです。 (%s)', $e->getMessage());
			$e->sendAlert();
			throw $e;
		}
		return $db;
	}

	/**
	 * 設定ファイルを返す
	 *
	 * @access private
	 * @return BSConfigFile 設定ファイル
	 * @static
	 */
	static private function getConfigFile () {
		if (!self::$configFile) {
			$dir = BSController::getInstance()->getDirectory('config');
			foreach (array('my.cnf', 'my.cnf.ini', 'my.cnf') as $name) {
				if (self::$configFile = $dir->getEntry($name, 'BSConfigFile')) {
					break;
				}
			}
		}
		return self::$configFile;
	}

	/**
	 * DSNをパースしてプロパティに格納
	 *
	 * @access protected
	 */
	protected function parseDSN () {
		parent::parseDSN();
		preg_match('/^mysql:host=([^;]+);dbname=([^;]+)$/', $this->getDSN(), $matches);
		$this->attributes['host'] = new BSHost($matches[1]);
		$this->attributes['port'] = self::getDefaultPort();
		$this->attributes['name'] = $matches[2];
		$this->attributes['config_file'] = self::getConfigFile();
	}

	/**
	 * テーブル名のリストを配列で返す
	 *
	 * @access public
	 * @return string[] テーブル名のリスト
	 */
	public function getTableNames () {
		if (!$this->tables) {
			$column = 'Tables_in_' . $this->getAttribute('name');
			foreach ($this->query('SHOW TABLES') as $row) {
				$this->tables[] = $row[$column];
			}
		}
		return $this->tables;
	}

	/**
	 * クエリーをエンコード
	 *
	 * @access protected
	 * @param string $query クエリー文字列
	 * @return string エンコードされたクエリー
	 */
	protected function encodeQuery ($query) {
		if ($this->isLegacy()) {
			return parent::encodeQuery($query);
		} else {
			return $query;
		}
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
		if ($this->isLegacy()) {
			$engine = 'HEAP';
		} else {
			$engine = 'MEMORY';
		}

		$table = new $class;
		$query = sprintf(
			'CREATE TEMPORARY TABLE %s (%s) Engine=%s',
			$table->getName(),
			implode(',', $details),
			$engine
		);
		$this->exec($query);
		return $table;
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
		$command = $this->getCommandLine('mysqldump');
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
		$command = $this->getCommandLine('mysqldump');
		$command->addValue('--no-data');
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
	private function getCommandLine ($command = 'mysql') {
		$command = new BSCommandLine('bin/' . $command);
		$command->setDirectory(BSController::getInstance()->getDirectory('mysql'));
		$command->addValue('--host=' . $this->getAttribute('host')->getAddress());
		$command->addValue('--user=' . $this->getAttribute('user'));
		$command->addValue($this->getAttribute('name'));

		if ($password = $this->getAttribute('password')) {
			$command->addValue('--password=' . $password);
		}

		return $command;
	}

	/**
	 * 最適化
	 *
	 * @access public
	 */
	public function optimize () {
		foreach ($this->getTableNames() as $name) {
			$this->exec('OPTIMIZE TABLE ' . $name);
		}
	}

	/**
	 * テーブルのプロフィールを返す
	 *
	 * @access public
	 * @param string $table テーブルの名前
	 * @return BSTableProfile テーブルのプロフィール
	 */
	public function getTableProfile ($table) {
		if ($this->getVersion() < 5.0) {
			return new BSMySQL40TableProfile($table, $this);
		} else {
			return parent::getTableProfile($table);
		}
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
	 * バージョンは4.0以前か？
	 *
	 * @access public
	 * @return boolean 4.0以前ならTrue
	 */
	public function isLegacy () {
		return ($this->getVersion() < 4.1);
	}

	/**
	 * データベースのエンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		if (!isset($this->attributes['encoding'])) {
			if ($this->isLegacy()) {
				$query = 'SHOW VARIABLES LIKE ' . $this->quote('character_set');
				$result = PDO::query($query)->fetch();
				if (!$encoding = self::getEncodings()->getParameter($result['Value'])) {
					throw new BSDatabaseException(
						'文字セット"%s"は使用できません。',
						$result['Value']
					);
				}
				$this->attributes['encoding'] = $encoding;
			} else {
				// 4.1以降のMySQLでは、クライアント側エンコードに固定。
				$this->attributes['encoding'] = 'utf-8';
			}
		}
		return $this->attributes['encoding'];
	}

	/**
	 * MySQLのエンコード名を返す
	 *
	 * @access private
	 * @return string MySQLのエンコード名
	 */
	private function getEncodingName () {
		return self::getEncodings()->getKeys()->getParameter($this->getEncoding());
	}

	/**
	 * サポートしているエンコードを返す
	 *
	 * @access public
	 * @return BSArray PHPのエンコードの配列
	 * @static
	 */
	static public function getEncodings () {
		$encodings = new BSArray;
		$encodings['sjis'] = 'sjis';
		$encodings['ujis'] = 'euc-jp';
		$encodings['utf8'] = 'utf-8';
		return $encodings;
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 * @static
	 */
	static public function getDefaultPort () {
		return BSNetworkService::getPort('mysql');
	}
}

/* vim:set tabstop=4 ai: */
?>