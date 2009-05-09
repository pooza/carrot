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
	 * 接続
	 *
	 * @access protected
	 * @name string $name データベース名
	 * @return BSMySQLDatabase インスタンス
	 * @static
	 */
	static protected function connect ($name) {
		$params = array();
		if ($file = self::getConfigFile()) {
			$params[self::MYSQL_ATTR_READ_DEFAULT_FILE] = $file->getPath();
		}

		$constants = BSConstantHandler::getInstance();
		$password = $constants['PDO_' . $name . '_PASSWORD'];
		foreach (array(BSCrypt::getInstance()->decrypt($password), $password) as $password) {
			try {
				$db = new self(
					$constants['PDO_' . $name . '_DSN'],
					$constants['PDO_' . $name . '_UID'],
					$password,
					$params
				);
				$db->setName($name);
				if (!self::getConfigFile() && !$db->isLegacy()) {
					$db->exec('SET NAMES ' . $db->getEncodingName());
				}
				return $db;
			} catch (Exception $e) {
			}
		}
		throw new BSDatabaseException('データベース "%s" に接続できません。', $name);
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
			foreach (array('my.cnf', 'my.cnf.ini', 'my.ini') as $name) {
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
		preg_match('/^mysql:host=([^;]+);dbname=([^;]+)$/', $this['dsn'], $matches);
		$this->attributes['host'] = new BSHost($matches[1]);
		$this->attributes['port'] = $this->getDefaultPort();
		$this->attributes['database_name'] = $matches[2];
		$this->attributes['encoding_name'] = $this->getEncodingName();
		$this->attributes['config_file'] = self::getConfigFile();
	}

	/**
	 * テーブル名のリストを配列で返す
	 *
	 * @access public
	 * @return BSArray テーブル名のリスト
	 */
	public function getTableNames () {
		if (!$this->tables) {
			$this->tables = new BSArray;
			foreach ($this->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM) as $row) {
				$this->tables[] = $row[0];
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
		$command->addValue('--host=' . $this['host']->getAddress());
		$command->addValue('--user=' . $this['user']);
		$command->addValue($this['database_name']);

		if ($password = $this['password']) {
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
		if ($this['version'] < 5.0) {
			return new BSMySQL40TableProfile($table, $this);
		} else {
			return parent::getTableProfile($table);
		}
	}

	/**
	 * バージョンを返す
	 *
	 * @access protected
	 * @return float バージョン
	 */
	protected function getVersion () {
		$result = PDO::query('SELECT version() AS ver')->fetch();
		return $result['ver'];
	}

	/**
	 * バージョンは4.0以前か？
	 *
	 * @access public
	 * @return boolean 4.0以前ならTrue
	 */
	public function isLegacy () {
		return ($this['version'] < 4.1);
	}

	/**
	 * データベースのエンコードを返す
	 *
	 * @access protected
	 * @return string PHPのエンコード名
	 */
	protected function getEncoding () {
		if ($this->isLegacy()) {
			$query = 'SHOW VARIABLES LIKE ' . $this->quote('character_set');
			$result = PDO::query($query)->fetch();
			if (!$encoding = self::getEncodings()->getParameter($result['Value'])) {
				throw new BSDatabaseException(
					'文字セット"%s"は使用できません。',
					$result['Value']
				);
			}
			return $encoding;
		} else {
			// 4.1以降のMySQLでは、クライアント側エンコードに固定。
			return 'utf-8';
		}
	}

	/**
	 * MySQLのエンコード名を返す
	 *
	 * @access private
	 * @return string MySQLのエンコード名
	 */
	private function getEncodingName () {
		return self::getEncodings()->getKeys()->getParameter($this['encoding']);
	}

	/**
	 * サポートしているエンコードを返す
	 *
	 * @access private
	 * @return BSArray PHPのエンコードの配列
	 * @static
	 */
	static private function getEncodings () {
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
	 */
	public function getDefaultPort () {
		return 3306;
	}
}

/* vim:set tabstop=4: */
