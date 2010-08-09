<?php
/**
 * @package org.carrot-framework
 * @subpackage database.postgresql
 */

/**
 * PostgreSQL用データソース名
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPgSQLDataSourceName extends BSDataSourceName {

	/**
	 * データベースに接続して返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		return new BSPostgreSQLDatabase($this->getContents());
	}

	/**
	 * DSNをパースしてパラメータに格納
	 *
	 * @access protected
	 */
	protected function parse () {
		parent::parse();

		mb_ereg('^pgsql:(.+)$', $this->getContents(), $matches);
		foreach (mb_split(' +', $matches[1]) as $config) {
			$config = BSString::explode('=', $config);
			switch ($config[0]) {
				case 'host':
					$this['host'] = new BSHost($config[1]);
					break;
				case 'dbname':
					$this['database_name'] = $config[1];
					break;
				case 'user':
					$this['uid'] = $config[1];
					break;
			}
		}
	}
}

/* vim:set tabstop=4: */
