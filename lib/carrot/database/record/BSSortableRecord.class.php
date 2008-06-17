<?php
/**
 * @package jp.co.b-shock.carrot
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

	/**
	 * 更新
	 *
	 * @access public
	 * @param string[] $values 更新する値
	 */
	public function update ($values) {
		$values['update_date'] = BSDate::getNow('Y-m-d H:i:s');
		parent::update($values);
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
		return ($this->getAttribute('status') == 'show');
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
	 * 順位を変更する
	 *
	 * @access public
	 * @param string $option (up|down)
	 */
	public function setOrder ($option) {
		$ids = array();

		foreach ($this->getAlikeRecords() as $record) {
			$ids[] = $record->getID();
			if ($record->getID() == $this->getID()) {
				$rank = count($ids) - 1;
			}
		}

		switch (strtolower($option)) {
			case 'up':
				if (isset($ids[$rank - 1])) {
					$ids[$rank] = $ids[$rank - 1];
					$ids[$rank - 1] = $this->getID();
				}
				break;
			case 'down':
				if (isset($ids[$rank + 1])) {
					$ids[$rank] = $ids[$rank + 1];
					$ids[$rank + 1] = $this->getID();
				}
				break;
		}

		$this->getAlikeRecords()->clearRanks();
		$rank = 0;
		foreach ($ids as $id) {
			$rank ++;
			$this->getAlikeRecords()->getRecord($id)->setRank($rank);
		}
	}

	/**
	 * 順位を設定する
	 *
	 * @access public
	 * @param integer $rank 順位
	 */
	public function setRank ($rank) {
		$this->update(
			array($this->getTable()->getRankField() => $rank)
		);
	}
}

/* vim:set tabstop=4 ai: */
?>