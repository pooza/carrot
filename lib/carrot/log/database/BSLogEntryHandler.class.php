<?php
/**
 * @package org.carrot-framework
 * @subpackage log.database
 */

/**
 * ログテーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
	 * 日付の配列を返す
	 *
	 * @access public
	 * @return BSArray 月の降順配列
	 */
	public function getDates () {
		$query = BSSQL::getSelectQueryString(
			'strftime(\'%Y-%m-%d\',date) AS date',
			$this->getName(),
			$this->getCriteria(),
			'date DESC',
			'date'
		);

		$dates = new BSArray;
		foreach ($this->getDatabase()->query($query) as $row) {
			$dates[$row['date']] = $row['date'];
		}
		return $dates;
	}

	/**
	 * エントリーを抽出して返す
	 *
	 * @access public
	 * @param BSDate $date 対象日付
	 * @return BSLogEntryHandler 抽出済みテーブル
	 */
	public function getEntries (BSDate $date) {
		$table = clone $this;
		$criteria = sprintf(
			'strftime(%s,date)=%s',
			$this->getDatabase()->quote('%Y-%m-%d'),
			$this->getDatabase()->quote($date->format('Y-m-d'))
		);
		$table->setCriteria($criteria);
		return $table;
	}
}

/* vim:set tabstop=4: */
