<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.postgresql
 */

/**
 * PostgreSQLデータベース
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPostgreSQLDatabase extends BSDatabase {

	/**
	 * 命名規則に従い、シーケンス名を返す
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @param string $field 主キーフィールド名
	 * @return string シーケンス名
	 */
	public function getSequenceName ($table, $field = 'id') {
		return implode('_', [$table, $field, 'seq']);
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
	 * ダンプ実行
	 *
	 * @access protected
	 * @return string 結果
	 */
	protected function dump () {
		$command = $this->createCommand('pg_dump');
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
	protected function createCommand ($command = 'psql') {
		$command = new BSCommandLine('bin/' . $command);
		$command->setDirectory(BSFileUtility::getDirectory('pgsql'));
		$command->push('--host=' . $this['host']->getAddress());
		$command->push('--user=' . $this['user']);
		$command->push($this['database_name']);
		return $command;
	}

	/**
	 * 最適化
	 *
	 * @access public
	 */
	public function optimize () {
		$this->exec('VACUUM');
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
}

/* vim:set tabstop=4: */
