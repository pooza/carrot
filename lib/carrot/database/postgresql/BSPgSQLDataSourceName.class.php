<?php
/**
 * @package org.carrot-framework
 * @subpackage database.postgresql
 */

/**
 * PostgreSQL用データソース名
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPgSQLDataSourceName extends BSDataSourceName {

	/**
	 * @access public
	 * @param mixed[] $params 要素の配列
	 */
	public function __construct ($contents, $name = 'default') {
		parent::__construct($contents, $name);

		mb_ereg('^pgsql:(.+)$', $contents, $matches);
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

	/**
	 * データベースに接続して返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function connect () {
		$db = new BSPostgreSQLDatabase($this->getContents());
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
		return 'PostgreSQL';
	}
}

/* vim:set tabstop=4: */
