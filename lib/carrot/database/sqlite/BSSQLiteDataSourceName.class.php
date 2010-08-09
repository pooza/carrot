<?php
/**
 * @package org.carrot-framework
 * @subpackage database.sqlite
 */

/**
 * SQLite用データソース名
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSQLiteDataSourceName extends BSDataSourceName {

	/**
	 * データベースに接続して返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		return new BSSQLiteDatabase($this->getContents());
	}

	/**
	 * DSNをパースしてパラメータに格納
	 *
	 * @access protected
	 */
	protected function parse () {
		parent::parse();
		mb_ereg('^sqlite:(.+)$', $this->getContents(), $matches);
		$this['file'] = new BSFile($matches[1]);
	}
}

/* vim:set tabstop=4: */
