<?php
/**
 * @package org.carrot-framework
 * @subpackage database.mysql
 */

/**
 * MySQLデータベース
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMySQLDatabase extends BSDatabase {
	private $version;

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
	 * DSNを設定
	 *
	 * @access public
	 * @param BSDataSourceName $dsn DSN
	 */
	public function setDSN (BSDataSourceName $dsn) {
		parent::setDSN($dsn);
		$this->dsn['encoding_name'] = $this->getEncodingName();
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
		$command->push('--host=' . $this['host']->getAddress());
		$command->push('--user=' . $this['uid']);
		$command->push($this['database_name']);
		if (!BSString::isBlank($password = $this['password'])) {
			$command->push('--password=' . $password);
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
		if ($this->getVersion() < 5.0) {
			return new BSMySQL4TableProfile($table, $this);
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
		if (!$this->version) {
			$result = PDO::query('SELECT version() AS ver')->fetch();
			$this->version = $result['ver'];
		}
		return $this->version;
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
	 * @access public
	 * @return string MySQLのエンコード名
	 */
	public function getEncodingName () {
		$names = self::getEncodings()->getFlipped();
		return $names[$this->getEncoding()];
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
}

/* vim:set tabstop=4: */
