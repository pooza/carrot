<?php
/**
 * @package org.carrot-framework
 * @subpackage database.record
 */

/**
 * ソート可能なテーブルのレコード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSSortableRecord extends BSRecord {
	const RANK_UP = 'up';
	const RANK_DOWN = 'down';

	/**
	 * 更新
	 *
	 * @access public
	 * @param string[] $values 更新する値
	 * @param integer $flags フラグのビット列
	 *   BSDatabase::WITH_LOGGING ログを残さない
	 */
	public function update ($values, $flags = BSDatabase::WITH_LOGGING) {
		$values['update_date'] = BSDate::getNow('Y-m-d H:i:s');
		parent::update($values, $flags);
	}

	/**
	 * 更新可能か？
	 *
	 * @access protected
	 * @return boolean 更新可能ならTrue
	 */
	protected function isUpdatable () {
		return true;
	}

	/**
	 * 削除可能か？
	 *
	 * @access protected
	 * @return boolean 削除可能ならTrue
	 */
	protected function isDeletable () {
		return true;
	}

	/**
	 * 表示して良いか？
	 *
	 * @access public
	 * @return boolean 表示して良いならTrue
	 */
	public function isVisible () {
		return ($this['status'] == 'show');
	}

	/**
	 * 表示して良いか？
	 *
	 * isVisibleのエイリアス
	 *
	 * @access public
	 * @return boolean 表示して良いならTrue
	 * @final
	 */
	final public function isShowable () {
		return $this->isVisible();
	}

	/**
	 * 同種のレコードを返す
	 *
	 * @access public
	 * @return SortableTableHandler テーブル
	 * @abstract
	 */
	abstract public function getAlikeRecords ();

	/**
	 * 順位を変更
	 *
	 * @access public
	 * @param string $option (self::RANK_UP|self::RANK_DOWN)
	 */
	public function setOrder ($option) {
		$rank = 0;
		foreach ($ids = $this->getAlikeRecords()->getIDs() as $id) {
			if ($id == $this->getID()) {
				break;
			}
			$rank ++;
		}

		if (($option == self::RANK_UP) && $ids[$rank - 1]) {
			$ids[$rank] = $ids[$rank - 1];
			$ids[$rank - 1] = $this->getID();
		} else if (($option == self::RANK_DOWN) && $ids[$rank + 1]) {
			$ids[$rank] = $ids[$rank + 1];
			$ids[$rank + 1] = $this->getID();
		}

		$rank = 0;
		foreach ($ids as $id) {
			$rank ++;
			$this->getAlikeRecords()->getRecord($id)->setRank($rank);
		}
	}

	/**
	 * 順位を設定
	 *
	 * @access protected
	 * @param integer $rank 順位
	 */
	protected function setRank ($rank) {
		$this->update(
			array($this->getTable()->getRankField() => $rank),
			null
		);
	}
}

/* vim:set tabstop=4: */
