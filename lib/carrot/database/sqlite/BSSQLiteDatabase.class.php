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
		preg_match('/^sqlite:(.+)$/', $this->getDSN(), $matches);
		$this->attributes['file'] = new BSFile($matches[1]);
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
			'CREATE TEMPORARY TABLE %s (%s)',
			$table->getName(),
			implode(',', $details)
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
		$command = $this->getCommandLine();
		$command->addValue('.dump');
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
		$command = $this->getCommandLine();
		$command->addValue('.schema');
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
	private function getCommandLine ($command = 'sqlite3') {
		$command = new BSCommandLine('bin/' . $command);
		$command->setDirectory(BSController::getInstance()->getDirectory('sqlite3'));
		$command->addValue($this->getAttribute('file')->getPath());
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
		return '3.x'; //取得方法不明
	}
}

/* vim:set tabstop=4: */
