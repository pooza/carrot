<?php
/**
 * @package org.carrot-framework
 * @subpackage database.query
 */

/**
 * 抽出条件の集合
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCriteriaSet extends BSArray {
	private $glue = 'AND';
	private $db;

	/**
	 * 接続子を返す
	 *
	 * @access public
	 * @return string 接続子
	 */
	public function getGlue () {
		return $this->glue;
	}

	/**
	 * 接続子を設定
	 *
	 * @access public
	 * @param string $glue 接続子
	 */
	public function setGlue ($glue) {
		$this->glue = strtoupper($glue);
	}

	/**
	 * 対象データベースを返す
	 *
	 * @access public
	 * @return BSDatabase 対象データベース
	 */
	public function getDatabase () {
		if (!$this->db) {
			$this->db = BSDatabase::getInstance();
		}
		return $this->db;
	}

	/**
	 * 対象データベースを設定
	 *
	 * @access public
	 * @param BSDatabase $db 対象データベース
	 */
	public function setDatabase (BSDatabase $db) {
		$this->db = $db;
	}

	/**
	 * 条件を登録
	 *
	 * @access public
	 * @param string $key フィールド名
	 * @param mixed $value 値又はその配列
	 * @param string $operator 演算子
	 */
	public function register ($key, $value, $operator = '=') {
		$key = strtolower($key);
		$operator = strtoupper($operator);

		if ($operator == 'IN') {
			if (BSArray::isArray($value)) {
				$values = new BSArray($value);
				if ($values->count()) {
					$this[] = $key . ' IN (' . $this->quote($values)->join(',') . ')';
				}
				return;
			} else {
				$operator = '=';
			}
		}
		$this[] = $key . $operator . $this->quote($value);
	}

	/**
	 * 値をクォート
	 *
	 * @access public
	 * @param mixed $value 値又はその配列
	 * @param string $operator 演算子
	 * @return mixed クォートされた値
	 */
	public function quote ($value) {
		if (BSArray::isArray($value)) {
			$ids = new BSArray;
			foreach (new BSArray($value) as $item) {
				$ids[] = $this->quote($item);
			}
			return $ids;
		} else {
			return $this->getDatabase()->quote($value);
		}
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
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
