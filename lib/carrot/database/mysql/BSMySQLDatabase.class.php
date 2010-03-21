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
		$constants = BSConstantHandler::getInstance();
		$params = array();
		if ($constants['PDO::MYSQL_ATTR_READ_DEFAULT_FILE'] && ($file = self::getConfigFile())) {
			$params[PDO::MYSQL_ATTR_READ_DEFAULT_FILE] = $file->getPath();
		}

		foreach (self::getPasswords($name) as $password) {
			try {
				$db = new self(
					$constants['PDO_' . $name . '_DSN'],
					$constants['PDO_' . $name . '_UID'],
					$password,
					$params
				);
				$db->setName($name);
				if (!$params) {
					$db->exec('SET NAMES ' . $db->getEncodingName());
				}
				return $db;
			} catch (Exception $e) {
			}
		}

		$message = new BSStringFormat('データベース "%s" に接続できません。');
		$message[] = $name;
		throw new BSDatabaseException($message);
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
			$dir = BSFileUtility::getDirectory('config');
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
		mb_ereg('^mysql:host=([^;]+);dbname=([^;]+)$', $this['dsn'], $matches);
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
	 * ダンプ実行
	 *
	 * @access protected
	 * @return string 結果
	 */
	protected function dump () {
		$command = $this->getCommandLine('mysqldump');
		if ($command->hasError()) {
			throw new BSDatabaseException($command->getResult());
		}
		return $command->getResult()->join("\n");
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
		$command->setDirectory(BSFileUtility::getDirectory('mysql'));
		$command->addValue('--host=' . $this['host']->getAddress());
		$command->addValue('--user=' . $this['uid']);
		$command->addValue($this['database_name']);
		if (!BSString::isBlank($password = $this['password'])) {
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
		$this->putLog($this . 'を最適化しました。');
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
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		if ($this->isLegacy()) {
			$query = 'SHOW VARIABLES LIKE ' . $this->quote('character_set');
			$result = PDO::query($query)->fetch();
			if (!$encoding = self::getEncodings()->getParameter($result['Value'])) {
				$message = new BSStringFormat('文字セット "%s" は使用できません。');
				$message[] = $result['Value'];
				throw new BSDatabaseException($message);
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
		$names = self::getEncodings()->getFlipped();
		return $names[$this['encoding']];
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
