<?php
/**
 * @package org.carrot-framework
 * @subpackage database.query
 */

/**
 * SQL生成に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSQL {

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * 文字列をクォート
	 *
	 * 極力使わない。
	 * BSCriteriaSet等、クォート処理をラップした機能を使用すること。
	 *
	 * @access public
	 * @param string $str クォートの対象
	 * @param BSDatabase $db データベース
	 * @return string クォートされた文字列
	 * @static
	 */
	static public function quote ($str, BSDatabase $db = null) {
		if (!$db) {
			$db = BSDatabase::getInstance();
		}
		return $db->quote($str);
	}

	/**
	 * SELECTクエリー文字列を返す
	 *
	 * @access public
	 * @param string[] $fields フィールド
	 * @param string[] $tables テーブル名の配列
	 * @param mixed $criteria 抽出条件
	 * @param mixed $order ソート順
	 * @param string $group グループ化
	 * @param integer $page ページ
	 * @param integer $pagesize ページサイズ
	 * @return string クエリー文字列
	 * @static
	 */
	static public function getSelectQueryString ($fields, $tables, $criteria = null, $order = null, $group = null, $page = null, $pagesize = null) {
		$query = array(
			'SELECT',
			self::getFieldsString($fields),
			'FROM ' . self::getFromString($tables),
		);

		if ($criteria) {
			$query[] = 'WHERE ' . self::getCriteriaString($criteria);
		}
		if ($group) {
			$query[] = 'GROUP BY ' . self::getGroupString($group);
		}
		if ($order) {
			$query[] = 'ORDER BY ' . self::getOrderString($order);
		}
		if ($page && $pagesize) {
			$query[] = self::getOffsetString($page, $pagesize);
		}

		return implode(' ', $query);
	}

	/**
	 * INSERTクエリー文字列を返す
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @param mixed $values フィールドの値
	 * @param BSDatabase $db 対象データベース
	 * @return string クエリー文字列
	 * @static
	 */
	static public function getInsertQueryString ($table, $values, BSDatabase $db = null) {
		if (!$db) {
			$db = BSDatabase::getInstance();
		}
		if (is_array($values)) {
			$values = new BSArray($values);
		} else if ($values instanceof BSParameterHolder) {
			$values = new BSArray($values->getParameters());
		}

		$quoted = new BSArray;
		foreach ($values as $value) {
			$quoted[] = $db->quote($value);
		}

		return sprintf(
			'INSERT INTO %s (%s) VALUES (%s)',
			$table,
			$values->getKeys()->join(', '),
			$quoted->join(', ')
		);
	}

	/**
	 * UPDATEクエリー文字列を返す
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @param mixed $values フィールドの値
	 * @param mixed $criteria 抽出条件
	 * @param BSDatabase $db 対象データベース
	 * @return string クエリー文字列
	 * @static
	 */
	static public function getUpdateQueryString ($table, $values, $criteria, BSDatabase $db = null) {
		if (!$db) {
			$db = BSDatabase::getInstance();
		}
		if (is_array($values)) {
			$values = new BSArray($values);
		} else if ($values instanceof BSParameterHolder) {
			$values = new BSArray($values->getParameters());
		}

		$fields = new BSArray;
		foreach ($values as $key => $value) {
			$fields[] = sprintf('%s=%s', $key, $db->quote($value));
		}

		return sprintf(
			'UPDATE %s SET %s WHERE %s',
			$table,
			$fields->join(', '),
			self::getCriteriaString($criteria)
		);
	}

	/**
	 * DELETEクエリー文字列を返す
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @param mixed $criteria 抽出条件
	 * @return string クエリー文字列
	 * @static
	 */
	static public function getDeleteQueryString ($table, $criteria) {
		return sprintf('DELETE FROM %s WHERE %s', $table, self::getCriteriaString($criteria));
	}

	/**
	 * CREATE TABLEクエリー文字列を返す
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @param string[] $fields フィールド定義等
	 * @static
	 */
	static public function getCreateTableQueryString ($table, $fields) {
		$fields = new BSArray($fields);
		foreach ($fields as $key => $field) {
			if (is_numeric($key)) {
				$fields[$key] = $field;
			} else {
				$fields[$key] = $key . ' ' . $field;
			}
		}
		return sprintf('CREATE TABLE %s (%s)', $table, $fields->join(','));
	}

	/**
	 * DROP TABLEクエリー文字列を返す
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @static
	 */
	static public function getDropTableQueryString ($table) {
		return sprintf('DROP TABLE %s', $table);
	}

	/**
	 * フィールドリスト文字列を返す
	 *
	 * @access public
	 * @param string[] $fields フィールドリストの配列
	 * @return string フィールドリスト文字列
	 * @static
	 */
	static public function getFieldsString ($fields = null) {
		if (!($fields instanceof BSTableFieldSet)) {
			$fields = new BSTableFieldSet($fields);
		}
		if (!$fields->count()) {
			$fields[] = '*';
		}
		return $fields->getContents();
	}

	/**
	 * テーブル文字列を返す
	 *
	 * @access public
	 * @param string[] $tables テーブルの配列
	 * @return string テーブル文字列
	 * @static
	 */
	static public function getFromString ($tables) {
		if (!($tables instanceof BSTableFieldSet)) {
			$tables = new BSTableFieldSet($tables);
		}
		return $tables->getContents();
	}

	/**
	 * 抽出条件文字列を返す
	 *
	 * @access private
	 * @param mixed $criteria 抽出条件の配列
	 * @return string 抽出条件文字列
	 * @static
	 */
	static private function getCriteriaString ($criteria) {
		if (!($criteria instanceof BSCriteriaSet)) {
			$criteria = new BSCriteriaSet($criteria);
		}
		return $criteria->getContents();
	}

	/**
	 * ソート順文字列を返す
	 *
	 * @access public
	 * @param string[] $order ソート順の配列
	 * @return string ソート順文字列
	 * @static
	 */
	static public function getOrderString ($order) {
		if (!($order instanceof BSTableFieldSet)) {
			$order = new BSTableFieldSet($order);
		}
		return $order->getContents();
	}

	/**
	 * グループ化文字列を返す
	 *
	 * @access public
	 * @param string[] $order グループ化の配列
	 * @return string グループ化文字列
	 * @static
	 */
	static public function getGroupString ($group) {
		if (!($group instanceof BSTableFieldSet)) {
			$group = new BSTableFieldSet($group);
		}
		return $group->getContents();
	}

	/**
	 * オフセット文字列を返す
	 *
	 * @access public
	 * @param integer $page ページ番号
	 * @param integer $pagesize ページサイズ
	 * @return string オフセット文字列
	 * @static
	 */
	static public function getOffsetString ($page, $pagesize) {
		return sprintf(
			'LIMIT %d OFFSET %d',
			$pagesize,
			($page - 1) * $pagesize
		);
	}
}

/* vim:set tabstop=4: */
