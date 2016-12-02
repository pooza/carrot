<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.query
 */

/**
 * 抽出条件の集合
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
		$this->glue = BSString::toUpper($glue);
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
		$key = trim(BSString::toLower($key));
		$operator = trim(BSString::toUpper($operator));

		switch ($operator) {
			case 'BETWEEN':
				$values = new BSArray($value);
				if ($values->count() != 2) {
					throw new InvalidArgumentException('BETWEEN演算子に与える引数は2個です。');
				}
				$this[] = $key . ' BETWEEN ' . $this->quote($values)->join(' AND ');
				break;
			case 'NOT IN':
				$values = new BSArray($value);
				if ($values->count()) {
					$values->uniquize();
					$this[] = $key . ' NOT IN (' . $this->quote($values)->join(',') . ')';
				}
				break;
			default:
				if ($value === null) {
					$this[] = $key . ' IS NULL';
				} else if ($value instanceof BSArray) {
					$values = $value;
					if ($values->count()) {
						$values->uniquize();
						$this[] = $key . ' IN (' . $this->quote($values)->join(',') . ')';
					} else {
						$this[] = $key . ' IS NULL';
					}
				} else if ($value instanceof BSRecord) {
					$this[] = $key . ' ' . $operator . ' ' . $this->quote($value->getID());
				} else {
					$this[] = $key . ' ' . $operator . ' ' . $this->quote($value);
				}
				break;
		}
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
		if (is_array($value) || ($value instanceof BSParameterHolder)) {
			$ids = new BSArray;
			foreach (BSArray::create($value) as $item) {
				$ids[] = $this->getDatabase()->quote($item);
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
