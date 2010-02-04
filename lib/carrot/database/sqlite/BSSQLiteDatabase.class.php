<?php
/**
 * @package org.carrot-framework
 * @subpackage database.sqlite
 */

/**
 * SQLiteデータベース
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSQLiteDatabase extends BSDatabase {

	/**
	 * 接続
	 *
	 * @access protected
	 * @name string $name データベース名
	 * @return BSSQLiteDatabase インスタンス
	 * @static
	 */
	static protected function connect ($name) {
		$constants = BSConstantHandler::getInstance();
		$db = new BSSQLiteDatabase($constants['PDO_' . $name . '_DSN']);
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
		mb_ereg('^sqlite:(.+)$', $this['dsn'], $matches);
		$this->attributes['file'] = new BSFile($matches[1]);
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
				'name',
				'sqlite_master',
				'name NOT LIKE ' . $this->quote('sqlite_%')
			);
			foreach ($this->query($query) as $row) {
				$this->tables[] = $row['name'];
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
		$command = $this->getCommandLine();
		$command->addValue('.dump');
		if ($command->hasError()) {
			throw new BSDatabaseException($command->getResult());
		}
		return $command->getResult()->join("\n");
	}

	/**
	 * バックアップ対象ファイルを返す
	 *
	 * @access public
	 * @return BSFile バックアップ対象ファイル
	 */
	public function getBackupTarget () {
		return $this['file'];
	}

	/**
	 * コマンドラインを返す
	 *
	 * @access private
	 * @param string $command コマンド名
	 * @return BSCommandLine コマンドライン
	 */
	private function getCommandLine ($command = 'sqlite3') {
		$command = new BSCommandLine('bin/' . $command);
		$command->setDirectory(BSFileUtility::getDirectory('sqlite3'));
		$command->addValue($this['file']->getPath());
		return $command;
	}

	/**
	 * 最適化
	 *
	 * @access public
	 */
	public function optimize () {
		$this->exec('VACUUM');
		$this->putLog($this . 'を最適化しました。');
	}

	/**
	 * バージョンを返す
	 *
	 * @access protected
	 * @return float バージョン
	 */
	protected function getVersion () {
		if (version_compare(PHP_VERSION, '5.3', '>=') && extension_loaded('sqlite3')) {
			$ver = SQLite3::version();
			return $ver['versionString'];
		} else {
			return '3.x';
		}
	}
}

/* vim:set tabstop=4: */
