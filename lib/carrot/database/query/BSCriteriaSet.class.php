<?php
/**
 * @package org.carrot-framework
 * @subpackage database.query
 */

/**
 * 抽出条件の集合
 *
 * 未実装
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCriteriaSet extends BSArray {
	private $glue = 'AND';
	private $db;

	public function getGlue () {
		return $this->glue;
	}

	public function setGlue ($glue) {
		$this->glue = strtoupper($glue);
	}

	public function getDatabase () {
		if (!$this->db) {
			$this->db = BSDatabase::getInstance();
		}
		return $this->db;
	}

	public function setDatabase (BSDatabase $db) {
		$this->db = $db;
	}

	public function register ($key, $value, $operator = '=') {
		$this[] = strtolower($key) . strtoupper($operator) . $this->quote($value);
	}

	public function quote ($value) {
		return $this->getDatabase()->quote($value);
	}

	public function getContents () {
		$contents = new BSArray;
		foreach ($this as $criteria) {
			if ($criteria instanceof BSCriteriaSet) {
				$contents[] = '(' . $criteria->getContents() . ')';
			} else {
				$contents[] = '(' . $criteria . ')';
			}
		}
		return $contents->join(' ' . $this->getGlue() . ' ');
	}
}

/* vim:set tabstop=4: */
