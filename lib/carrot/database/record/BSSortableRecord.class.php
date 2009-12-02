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
	const RANK_TOP = 'top';
	const RANK_BOTTOM = 'bottom';

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

		switch ($option) {
			case self::RANK_UP:
				if ($ids[$rank - 1]) {
					$ids[$rank] = $ids[$rank - 1];
					$ids[$rank - 1] = $this->getID();
				}
				break;
			case self::RANK_DOWN:
				if ($ids[$rank + 1]) {
					$ids[$rank] = $ids[$rank + 1];
					$ids[$rank + 1] = $this->getID();
				}
				break;
			case self::RANK_TOP:
				$ids->removeParameter($rank);
				$ids->unshift($this->getID());
				break;
			case self::RANK_BOTTOM:
				$ids->removeParameter($rank);
				$ids[] = $this->getID();
				break;
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
