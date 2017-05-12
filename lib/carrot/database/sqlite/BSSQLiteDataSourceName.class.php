<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.sqlite
 */

/**
 * SQLite用データソース名
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSQLiteDataSourceName extends BSDataSourceName {

	/**
	 * @access public
	 * @param mixed[] $params 要素の配列
	 */
	public function __construct ($contents, $name = 'default') {
		parent::__construct($contents, $name);
		mb_ereg('^sqlite:(.+)$', $contents, $matches);
		$this['file'] = new BSFile($matches[1]);
	}

	/**
	 * データベースに接続して返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function connect () {
		$db = new BSSQLiteDatabase($this->getContents());
		$db->setDSN($this);
		$this['version'] = $db->getVersion();
		return $db;
	}

	/**
	 * DBMS名を返す
	 *
	 * @access public
	 * @return string DBMS名
	 */
	public function getDBMS () {
		return 'SQLite';
	}
}

