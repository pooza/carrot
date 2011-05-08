<?php
/**
 * @package org.carrot-framework
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
		$file = new BSFile($matches[1]);
		$this['file'] = $file->getShortPath();
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
}

/* vim:set tabstop=4: */
