<?php
/**
 * @package org.carrot-framework
 * @subpackage database.query
 */

/**
 * SELECTクエリー
 *
 * 未実装
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSelectQuery {
	private $db;
	private $fields;
	private $tables;
	protected $contents;

	public function __construct ($tables) {
		$this->setFields('*');
		$this->setTables($tables);
	}

	public function getDatabase () {
		if (!$this->db) {
			$this->db = BSDatabase::getInstance();
		}
		return $this->db;
	}

	public function setDatabase (BSDatabase $db) {
		$this->db = $db;
		$this->contents = null;
	}

	public function getFields () {
		return $this->fields;
	}

	public function setFields ($fields) {
		if ($fields instanceof BSArray) {
		} else if (is_array($fields)) {
			$tables = new BSArray($fields);
		} else {
			$tables = BSString::explode(',', $fields);
		}
		$this->fields = $fields;
		$this->contents = null;
	}

	public function getTables () {
		return $this->tables;
	}

	public function setTables ($tables) {
		if ($tables instanceof BSArray) {
		} else if (is_array($tables)) {
			$tables = new BSArray($tables);
		} else {
			$tables = BSString::explode(',', $tables);
		}
		$this->tables = $tables;
		$this->contents = null;
	}
}

/* vim:set tabstop=4: */
