<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * SQL生成に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSQL {

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// インスタンス化は禁止
	}

	/**
	 * 文字列をクォート
	 *
	 * @access public
	 * @param string $str クォートの対象
	 * @param boolean $convert 半角/全角標準化を行うか
	 * @return string クォートされた文字列
	 * @static
	 */
	public static function quote ($str, $convert = true) {
		return BSDatabase::getInstance()->quote($str, $convert);
	}

	/**
	 * SELECTクエリー文字列を返す
	 *
	 * @access public
	 * @param string[] $fields フィールド
	 * @param string[] $tables テーブル名の配列
	 * @param string[] $criteria 抽出条件
	 * @param string $order ソート順
	 * @param string $group グループ化
	 * @param integer $page ページ
	 * @param integer $pagesize ページサイズ
	 * @return string クエリー文字列
	 * @static
	 */
	public static function getSelectQueryString ($fields, $tables,
		$criteria = null, $order = null, $group = null, $page = null, $pagesize = null) {

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
	 * @param mixed[] $values フィールドの値
	 * @return string クエリー文字列
	 * @static
	 */
	public static function getInsertQueryString ($table, $values) {
		$fields = array();
		$valuesQuoted = array();
		foreach ($values as $key => $value) {
			$fields[] = $key;
			$valuesQuoted[] = self::quote($value);
		}

		return sprintf(
			'INSERT INTO %s (%s) VALUES (%s)',
			$table,
			implode(', ', $fields),
			implode(', ', $valuesQuoted)
		);
	}

	/**
	 * UPDATEクエリー文字列を返す
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @param mixed[] $values フィールドの値
	 * @param mixed $criteria 抽出条件
	 * @return string クエリー文字列
	 * @static
	 */
	public static function getUpdateQueryString ($table, $values, $criteria) {
		$fields = array();
		foreach ($values as $key => $value) {
			$fields[] = sprintf('%s=%s', $key, self::quote($value));
		}

		return sprintf(
			'UPDATE %s SET %s WHERE %s',
			$table,
			implode(', ', $fields),
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
	public static function getDeleteQueryString ($table, $criteria) {
		return sprintf('DELETE FROM %s WHERE %s', $table, self::getCriteriaString($criteria));
	}

	/**
	 * CREATE TABLEクエリー文字列を返す
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @param string[] $details フィールド定義等
	 * @static
	 */
	public static function getCreateTableQueryString ($table, $details) {
		return sprintf('CREATE TABLE %s (%s)', $table, implode(',', $details));
	}

	/**
	 * DROP TABLEクエリー文字列を返す
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @static
	 */
	public static function getDropTableQueryString ($table) {
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
	public static function getFieldsString ($fields = null) {
		if (!$fields) {
			return '*';
		} if (!BSArray::isArray($fields)) {
			return $fields;
		}

		return implode(', ', $fields);
	}

	/**
	 * テーブル文字列を返す
	 *
	 * @access public
	 * @param string[] $tables テーブルの配列
	 * @return string テーブル文字列
	 * @static
	 */
	public static function getFromString ($tables) {
		if (!BSArray::isArray($tables)) {
			return $tables;
		}

		$i = 0;
		foreach ($tables as $table) {
			$i ++;
			switch ($i) {
				case 1:
					$from = $table;
					break;
				case 2:
					$from .= ' ' . $table;
					break;
				default:
					$from = sprintf('(%s) %s', $from, $table);
					break;
			}
		}
		return $from;
	}

	/**
	 * 抽出条件文字列を返す
	 *
	 * @access public
	 * @param string[] $criteria 抽出条件の配列
	 * @param string $glue 結合子
	 * @return string 抽出条件文字列
	 * @static
	 */
	public static function getCriteriaString ($criteria, $glue = ' AND ') {
		if (!BSArray::isArray($criteria)) {
			return $criteria;
		}
		$criteriaFormed = array();
		foreach ($criteria as $item) {
			$criteriaFormed[] = sprintf('(%s)', $item);
		}
		return implode($glue, $criteriaFormed);
	}

	/**
	 * ソート順文字列を返す
	 *
	 * @access public
	 * @param string[] $order ソート順の配列
	 * @return string ソート順文字列
	 * @static
	 */
	public static function getOrderString ($order) {
		if (!BSArray::isArray($order)) {
			return $order;
		}
		return implode(', ', $order);
	}

	/**
	 * グループ化文字列を返す
	 *
	 * @access public
	 * @param string[] $order グループ化の配列
	 * @return string グループ化文字列
	 * @static
	 */
	public static function getGroupString ($group) {
		if (!BSArray::isArray($group)) {
			return $group;
		}
		return implode(', ', $group);
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
	public static function getOffsetString ($page, $pagesize) {
		return sprintf(
			'LIMIT %d OFFSET %d',
			$pagesize,
			($page - 1) * $pagesize
		);
	}
}

/* vim:set tabstop=4 ai: */
?>