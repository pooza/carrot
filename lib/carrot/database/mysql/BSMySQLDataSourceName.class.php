<?php
/**
 * @package org.carrot-framework
 * @subpackage database.mysql
 */

/**
 * MySQL用データソース名
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMySQLDataSourceName extends BSDataSourceName {
	private $file;

	/**
	 * @access public
	 * @param mixed[] $params 要素の配列
	 */
	public function __construct ($contents, $name = 'default') {
		parent::__construct($contents, $name);
		mb_ereg('^mysql:host=([^;]+);dbname=([^;]+)$', $contents, $matches);
		$this['host'] = new BSHost($matches[1]);
		$this['database_name'] = $matches[2];
		$this['config_file'] = $this->getFile();
	}

	/**
	 * データベースに接続して返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		$constants = BSConstantHandler::getInstance();
		$params = array();
		if ($constants['PDO::MYSQL_ATTR_READ_DEFAULT_FILE'] && ($file = $this->getFile())) {
			$params[PDO::MYSQL_ATTR_READ_DEFAULT_FILE] = $file->getPath();
		}

		foreach ($this->getPasswords() as $password) {
			try {
				$db = new BSMySQLDatabase($this->getContents(), $this['uid'], $password, $params);
				if (!$params) {
					$db->exec('SET NAMES ' . $db->getEncodingName());
				}
				return $db;
			} catch (Exception $e) {
			}
		}
		$message = new BSStringFormat('データベース "%s" に接続できません。');
		$message[] = $this->getName();
		throw new BSDatabaseException($message);
	}

	/**
	 * 設定ファイルを返す
	 *
	 * @access private
	 * @return BSConfigFile 設定ファイル
	 */
	private function getFile () {
		if (!$this->file) {
			$dir = BSFileUtility::getDirectory('config');
			foreach (array('my.cnf.ini', 'my.cnf', 'my.ini') as $name) {
				if ($this->file = $dir->getEntry($name, 'BSConfigFile')) {
					break;
				}
			}
		}
		return $this->file;
	}
}

/* vim:set tabstop=4: */