<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.mysql
 */

/**
 * MySQLデータベース
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMySQLDatabase extends BSDatabase {

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
	 * ダンプ実行
	 *
	 * @access protected
	 * @return string 結果
	 */
	protected function dump () {
		$command = $this->createCommand('mysqldump');
		$command->setStderrRedirectable(true);
		if ($command->hasError()) {
			throw new BSDatabaseException($command->getResult()->join(' '));
		}
		return $command->getResult()->join("\n");
	}

	/**
	 * コマンドラインを返す
	 *
	 * @access protected
	 * @param string $command コマンド名
	 * @return BSCommandLine コマンドライン
	 */
	protected function createCommand ($command = 'mysql') {
		$command = new BSCommandLine('bin/' . $command);
		$command->setDirectory(BSFileUtility::getDirectory('mysql'));
		$command->push('--host=' . $this['host']->getAddress());
		$command->push('--user=' . $this['uid']);
		$command->push($this['database_name']);

		if (!BSString::isBlank($password = $this->dsn['password'])) {
			$password = BSCrypt::getInstance()->decrypt($password);
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
		foreach ($this->getTableNames() as $table) {
			if ($this->getTableProfile($table)->isOptimizable()) {
				$this->exec('OPTIMIZE TABLE ' . $table);
			} else {
				$this->log($table . 'テーブルは最適化できません。');
			}
		}
		$this->log($this . 'を最適化しました。');
	}

	/**
	 * バージョンを返す
	 *
	 * @access public
	 * @return float バージョン
	 */
	public function getVersion () {
		if (!$this->version) {
			$result = PDO::query('SELECT version() AS ver')->fetch();
			$this->version = $result['ver'];
		}
		return $this->version;
	}

	/**
	 * 旧式か
	 *
	 * @access public
	 * @return boolean 旧式ならTrue
	 */
	public function isLegacy () {
		return ($this->getVersion() < 5);
	}
}

/* vim:set tabstop=4: */
