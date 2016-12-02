<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.sqlite
 */

/**
 * SQLiteデータベース
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSQLiteDatabase extends BSDatabase {

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
		$command = $this->createCommand();
		$command->push('.dump');
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
	 * @access protected
	 * @param string $command コマンド名
	 * @return BSCommandLine コマンドライン
	 */
	protected function createCommand ($command = 'sqlite3') {
		$command = new BSCommandLine('bin/' . $command);
		$command->setDirectory(BSFileUtility::getDirectory('sqlite3'));
		$command->push($this['file']->getPath());
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
	 * データベース関数を返す
	 *
	 * @access public
	 * @param string $name 関数名
	 * @param string $value 値
	 * @param boolean $quotes クォートする
	 * @return string 関数の記述
	 */
	public function getFunction ($name, $value, $quotes = false) {
		switch ($name) {
			case 'year':
				$func = new BSStringFormat('strftime(\'%%Y\', %s)');
				break;
			case 'month':
				$func = new BSStringFormat('strftime(\'%%m\', %s)');
				break;
			default:
				return parent::getFunction($name, $value, $quotes);
		}

		if (!!$quotes) {
			$func[] = $this->quote($value);
		} else {
			$func[] = $value;
		}
		return $func->getContents();
	}

	/**
	 * 外部キーが有効か？
	 *
	 * @access public
	 * @return boolean 有効ならTrue
	 */
	public function hasForeignKey () {
		return false;
	}

	/**
	 * バージョンを返す
	 *
	 * @access public
	 * @return float バージョン
	 */
	public function getVersion () {
		if (!$this->version && extension_loaded('sqlite3')) {
			$ver = SQLite3::version();
			$this->version = $ver['versionString'];
		}
		return $this->version;
	}
}

/* vim:set tabstop=4: */
