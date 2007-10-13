<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage session
 */

/**
 * ストアドセッションレコード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSStoredSession extends BSRecord {
	private $dateUpdate;

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
	 * 更新日付を返す
	 *
	 * @access protected
	 * @return BSDate 更新日付
	 */
	function getUpdateDate () {
		if (!$this->dateUpdate) {
			$this->dataUpdate = new BSDate;
			$this->dataUpdate->setTimeStamp($this->getAttribute('time'));
		}
		return $this->dataUpdate;
	}
}
?>