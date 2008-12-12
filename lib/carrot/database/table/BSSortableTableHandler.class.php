<?php
/**
 * @package org.carrot-framework
 * @subpackage database.table
 */

/**
 * ソート可能なテーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSSortableTableHandler extends BSTableHandler {

	/**
	 * @access public
	 * @param string $criteria 抽出条件
	 * @param string $order ソート順
	 */
	public function __construct ($criteria = null, $order = null) {
		if (!$order) {
			$order = array(
				$this->getRankField(),
				$this->getKeyField(),
			);
			$order = implode(',', $order);
		}
		parent::__construct($criteria, $order);
	}

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
	 * レコード追加
	 *
	 * @access public
	 * @param mixed[] $values 値
	 * @param integer $flag フラグのビット列
	 * @return string レコードの主キー
	 */
	public function createRecord ($values, $flag = BSDatabase::WITH_LOGGING) {
		$values['create_date'] = BSDate::getNow('Y-m-d H:i:s');
		$values['update_date'] = BSDate::getNow('Y-m-d H:i:s');
		return parent::createRecord($values, $flag);
	}

	/**
	 * 順位フィールド名
	 *
	 * @access public
	 * @return string 順位フィールド名
	 */
	public function getRankField () {
		return 'rank';
	}

	/**
	 * 状態フィールド名
	 *
	 * @access public
	 * @return string 状態フィールド名
	 */
	public function getStatusField () {
		return 'status';
	}

	/**
	 * 順位をクリア
	 *
	 * @access public
	 */
	public function clearRanks () {
		if (!$criteria = $this->getCriteria()) {
			$criteria = $this->getKeyField() . ' IS NOT NULL';
		}

		$sql = BSSQL::getUpdateQueryString(
			$this->getName(),
			array($this->getRankField() => 0),
			$criteria
		);
		BSDatabase::getInstance()->exec($sql);
	}

	/**
	 * 全ステータスを返す
	 *
	 * @access public
	 * @param mixed[] $values 値
	 * @static
	 */
	static public function getStatusOptions () {
		return BSTranslateManager::getInstance()->getHash(
			array('show', 'hide')
		);
	}
}

/* vim:set tabstop=4: */

