<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log.database
 */

/**
 * ログテーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSLogEntryHandler extends BSTableHandler {

	/**
	 * レコード追加可能か？
	 *
	 * @access protected
	 * @return boolean レコード追加可能ならTrue
	 */
	protected function isInsertable () {
		return true;
	}

	/**
	 * オートインクリメントのテーブルか？
	 *
	 * @access public
	 * @return boolean オートインクリメントならTrue
	 */
	public function isAutoIncrement () {
		return true;
	}

	/**
	 * テーブル名を返す
	 *
	 * @access public
	 * @return string テーブル名
	 */
	public function getName () {
		return BSDatabaseLogger::TABLE_NAME;
	}

	/**
	 * データベースを返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		foreach (array('log', 'default') as $db) {
			if ($db = BSDatabase::getInstance($db)) {
				return $db;
			}
		}
	}

	/**
	 * 月の配列を返す
	 *
	 * @access public
	 * @return BSArray 月の降順配列
	 */
	public function getMonths () {
		$query = BSSQL::getSelectQueryString(
			'strftime(\'%Y-%m\',date) AS month',
			$this->getName(),
			$this->getCriteria(),
			'month DESC',
			'month'
		);

		$months = new BSArray;
		foreach ($this->getDatabase()->query($query) as $row) {
			$months[$row['month']] = $row['month'];
		}
		return $months;
	}

	/**
	 * エントリーを抽出して返す
	 *
	 * @access public
	 * @param string $month yyyy-mm形式の月
	 * @return BSLogEntryHandler 抽出済みテーブル
	 */
	public function getEntries ($month) {
		$table = clone $this;
		$criteria = sprintf(
			'strftime(%s,date)=%s',
			$this->getDatabase()->quote('%Y-%m'),
			$this->getDatabase()->quote($month)
		);
		$table->setCriteria($criteria);
		return $table;
	}
}
?>