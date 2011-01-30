<?php
/**
 * @package org.carrot-framework
 * @subpackage database.query
 */

/**
 * SQL生成に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSQL {

	/**
	 * @access private
	 */
	private function __construct () {
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
		$query = new BSArray;
		$query[] = 'SELECT';
		$query[] = self::getFieldsString($fields);
		$query[] = self::getFromString($tables);
		$query[] = self::getCriteriaString($criteria);
		$query[] = self::getGroupString($group);
		$query[] = self::getOrderString($order);
		$query[] = self::getOffsetString($page, $pagesize);
		return $query->trim()->join(' ');
	}

	/**
	 * INSERTクエリー文字列を返す
	 *
	 * @access public
	 * @param mixed $table テーブル名又はテーブル
	 * @param mixed $values フィールドの値
	 * @param BSDatabase $db 対象データベース
	 * @return string クエリー文字列
	 * @static
	 */
	static public function getInsertQueryString ($table, $values, BSDatabase $db = null) {
		if (!$db) {
			$db = BSDatabase::getInstance();
		}
		if ($table instanceof BSTableHandler) {
			$table = $table->getName();
		}
		if (is_array($values)) {
			$values = new BSArray($values);
		} else if ($values instanceof BSParameterHolder) {
			$values = new BSArray($values->getParameters());
		}
		$values = $db->quote($values);

		return sprintf(
			'INSERT INTO %s (%s) VALUES (%s)',
			$table,
			$values->getKeys()->join(', '),
			$values->join(', ')
		);
	}

	/**
	 * UPDATEクエリー文字列を返す
	 *
	 * @access public
	 * @param mixed $table テーブル名又はテーブル
	 * @param mixed $values フィールドの値
	 * @param mixed $criteria 抽出条件
	 * @param BSDatabase $db 対象データベース
	 * @return string クエリー文字列
	 * @static
	 */
	static public function getUpdateQueryString ($table, $values, $criteria, BSDatabase $db = null) {
		if (BSString::isBlank($criteria = self::getCriteriaString($criteria))) {
			throw new BSDatabaseException('抽出条件がありません。');
		}
		if (!$db) {
			$db = BSDatabase::getInstance();
		}
		if ($table instanceof BSTableHandler) {
			$table = $table->getName();
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

		return sprintf('UPDATE %s SET %s %s', $table, $fields->join(', '), $criteria);
	}

	/**
	 * DELETEクエリー文字列を返す
	 *
	 * @access public
	 * @param mixed $table テーブル名又はテーブル
	 * @param mixed $criteria 抽出条件
	 * @return string クエリー文字列
	 * @static
	 */
	static public function getDeleteQueryString ($table, $criteria) {
		if (BSString::isBlank($criteria = self::getCriteriaString($criteria))) {
			throw new BSDatabaseException('抽出条件がありません。');
		}
		if ($table instanceof BSTableHandler) {
			$table = $table->getName();
		}
		return sprintf('DELETE %s %s', self::getFromString($table), $criteria);
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
	 * @param mixed $table テーブル名又はテーブル
	 * @static
	 */
	static public function getDropTableQueryString ($table) {
		if ($table instanceof BSTableHandler) {
			$table = $table->getName();
		}
		return sprintf('DROP TABLE %s', $table);
	}

	/**
	 * フィールドリスト文字列を返す
	 *
	 * @access private
	 * @param string[] $fields フィールドリストの配列
	 * @return string フィールドリスト文字列
	 * @static
	 */
	static private function getFieldsString ($fields = null) {
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
	 * @access private
	 * @param string[] $tables テーブルの配列
	 * @return string テーブル文字列
	 * @static
	 */
	static private function getFromString ($tables) {
		if (!($tables instanceof BSTableFieldSet)) {
			$tables = new BSTableFieldSet($tables);
		}
		return 'FROM ' . $tables->getContents();
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
		if ($criteria->count()) {
			return 'WHERE ' . $criteria->getContents();
		}
	}

	/**
	 * ソート順文字列を返す
	 *
	 * @access private
	 * @param string[] $order ソート順の配列
	 * @return string ソート順文字列
	 * @static
	 */
	static private function getOrderString ($order) {
		if (!($order instanceof BSTableFieldSet)) {
			$order = new BSTableFieldSet($order);
		}
		if ($order->count()) {
			return 'ORDER BY ' . $order->getContents();
		}
	}

	/**
	 * グループ化文字列を返す
	 *
	 * @access private
	 * @param string[] $order グループ化の配列
	 * @return string グループ化文字列
	 * @static
	 */
	static private function getGroupString ($group) {
		if (!($group instanceof BSTableFieldSet)) {
			$group = new BSTableFieldSet($group);
		}
		if ($group->count()) {
			return 'GROUP BY ' . $group->getContents();
		}
	}

	/**
	 * オフセット文字列を返す
	 *
	 * @access private
	 * @param integer $page ページ番号
	 * @param integer $pagesize ページサイズ
	 * @return string オフセット文字列
	 * @static
	 */
	static private function getOffsetString ($page, $pagesize) {
		if ($page && $pagesize) {
			return sprintf('LIMIT %d OFFSET %d', $pagesize, ($page - 1) * $pagesize);
		}
	}
}

/* vim:set tabstop=4: */
