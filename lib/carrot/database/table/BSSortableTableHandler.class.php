<?php
/**
 * @package jp.co.b-shock.carrot
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
	 */
	public function createRecord ($values) {
		$values['create_date'] = BSDate::getNow('Y/m/d H:i:s');
		$values['update_date'] = BSDate::getNow('Y/m/d H:i:s');
		return parent::createRecord($values);
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
	 * ソート順文字列を返す
	 *
	 * @access public
	 * @return string ソート順文字列
	 */
	public function getOrder () {
		$order = array(
			//$this->getStatusField(),
			$this->getRankField(),
			$this->getKeyField(),
		);
		return implode(',', $order);
	}

	/**
	 * 順位をクリアする
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
	public static function getStatusOptions () {
		return BSTranslator::getInstance()->getHash(
			array('show', 'hide')
		);
	}
}

/* vim:set tabstop=4 ai: */
?>