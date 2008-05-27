<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.mysql
 */

/**
 * MySQL接続
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSMySQL extends BSDatabase {
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
			foreach (array('dsn', 'uid', 'password') as $key) {
				if (!defined($const = strtoupper('bs_pdo_' . $name . '_' . $key))) {
					throw new BSDatabaseException('"%s"が未定義です。', $const);
				}
				$$key = constant($const);
			}
			try {
				self::$instances[$name] = new BSMySQL($dsn, $uid, $password);
				self::$instances[$name]->setName($name);
				self::$instances[$name]->exec('SET NAMES ' . self::getEncoding());
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
		preg_match('/^mysql:host=([^;]+);dbname=([^;]+)$/', $this->getDSN(), $matches);
		$this->attributes['host'] = new BSHost($matches[1]);
		$this->attributes['port'] = self::getDefaultPort();
		$this->attributes['name'] = $matches[2];
	}

	/**
	 * テーブル名のリストを配列で返す
	 *
	 * @access public
	 * @return string[] テーブル名のリスト
	 */
	public function getTableNames () {
		if (!$this->tables) {
			$nameColumn = 'Tables_in_' . $this->getAttribute('name');
			foreach ($this->query('SHOW TABLES') as $row) {
				$this->tables[] = $row[$nameColumn];
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
			'CREATE TEMPORARY TABLE %s (%s) Engine=MEMORY',
			$table->getName(),
			implode(',', $details)
		);
		$this->exec($query);
		return $table;
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
		$command[] = '/usr/bin/env mysqldump';
		$command[] = '--host=' . $this->getAttribute('host')->getAddress();
		$command[] = '--user=' . $this->getAttribute('user');
		if ($password = $this->getAttribute('password')) {
			$command[] = '--password=' . $password;
		}
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
		$command[] = '/usr/bin/env mysqldump';
		$command[] = '--host=' . $this->getAttribute('host')->getAddress();
		$command[] = '--user=' . $this->getAttribute('user');
		if ($password = $this->getAttribute('password')) {
			$command[] = '--password=' . $password;
		}
		$command[] = '--no-data';
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
		foreach ($this->getTableNames() as $name) {
			$this->exec('OPTIMIZE TABLE ' . $name);
		}
	}

	/**
	 * キャラクターセットを返す
	 *
	 * @access public
	 * @return string キャラクターセット
	 * @static
	 */
	static public function getEncoding () {
		$charsets = array(
			'sjis' => 'sjis',
			'euc-jp' => 'ujis',
			'utf-8' => 'utf8',
		);
		return $charsets[BSString::SCRIPT_ENCODING];
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