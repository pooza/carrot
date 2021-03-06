<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.record
 */

/**
 * ソート可能なテーブルのレコード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
trait BSSortableRecord {
	protected $next;
	protected $prev;
	protected $similars;

	/**
	 * 前レコードを返す
	 *
	 * @access public
	 * @return BSSortableRecord 前レコード
	 */
	public function getPrev () {
		if (!$this->prev) {
			$iterator = $this->getSimilars()->getIterator();
			foreach ($iterator as $record) {
				if ($this->getID() == $record->getID()) {
					return $this->prev = $iterator->prev();
				}
			}
		}
		return $this->prev;
	}

	/**
	 * 次レコードを返す
	 *
	 * @access public
	 * @return BSSortableRecord 次レコード
	 */
	public function getNext () {
		if (!$this->next) {
			$iterator = $this->getSimilars()->getIterator();
			foreach ($iterator as $record) {
				if ($this->getID() == $record->getID()) {
					return $this->next = $iterator->next();
				}
			}
		}
		return $this->next;
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
	 * 同種のレコードを返す
	 *
	 * @access protected
	 * @return BSSortableTableHandler テーブル
	 */
	protected function getSimilars () {
		if (!$this->similars) {
			$this->similars = BSTableHandler::create(get_class($this));
			if ($record = $this->getParent()) {
				$this->similars->getCriteria()->register(
					$record->getTable()->getName() . '_id',
					$record
				);
			}
		}
		return $this->similars;
	}

	/**
	 * 順位を変更
	 *
	 * @access public
	 * @param string $option (up|down|top|bottom)
	 */
	public function setOrder ($option) {
		$rank = 0;
		foreach ($ids = $this->getSimilars()->getIDs() as $id) {
			if ($id == $this->getID()) {
				break;
			}
			$rank ++;
		}

		switch ($option) {
			case 'up':
				if ($ids[$rank - 1]) {
					$ids[$rank] = $ids[$rank - 1];
					$ids[$rank - 1] = $this->getID();
				}
				break;
			case 'down':
				if ($ids[$rank + 1]) {
					$ids[$rank] = $ids[$rank + 1];
					$ids[$rank + 1] = $this->getID();
				}
				break;
			case 'top':
				$ids->removeParameter($rank);
				$ids->unshift($this->getID());
				break;
			case 'bottom':
				$ids->removeParameter($rank);
				$ids[] = $this->getID();
				break;
		}

		$rank = 0;
		foreach ($ids as $id) {
			$rank ++;
			if ($record = $this->getSimilars()->getRecord($id)) {
				$record->setRank($rank);
			}
		}
	}

	/**
	 * 順位を設定
	 *
	 * $this->update()を使用すると非常に重くなるので、SQLを直接実行する。
	 *
	 * @access protected
	 * @param integer $rank 順位
	 */
	protected function setRank ($rank) {
		$record = $this;
		$values = [$record->getTable()->getRankField() => $rank];
		while (true) {
			$this->getDatabase()->exec(BSSQL::getUpdateQueryString(
				$record->getTable(),
				$values,
				$record->getCriteria()
			));
			if (!$record = $record->getParent()) {
				break;
			}
			$values = [
				$record->getTable()->getUpdateDateField() => BSDate::getNow('Y-m-d H:i:s'),
			];
		}
	}
}

