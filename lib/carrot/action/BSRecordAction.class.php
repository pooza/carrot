<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage action
 */

/**
 * 詳細画面用 アクションひな形
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSRecordAction extends BSAction {
	private $record;

	/**
	 * 更新レコードのフィールド値を配列で返す
	 *
	 * @access protected
	 * @return mixed[] フィールド値の連想配列
	 */
	protected function getRecordValues () {
		return $this->getRecord()->getAttributes();
	}

	/**
	 * 編集中レコードを返す
	 *
	 * @access protected
	 * @return BSRecord 編集中レコード
	 */
	protected function getRecord () {
		if (!$this->record) {
			if ($id = $this->getRecordID()) {
				$this->record = $this->getTable()->getRecord($id);
			}
		}
		return $this->record;
	}

	/**
	 * カレントレコードIDを返す
	 *
	 * @access protected
	 */
	protected function getRecordID () {
		if ($id = $this->request->getParameter('id')) {
			$this->setRecordID($id);
		}
		return $this->user->getAttribute($this->getRecordClassName() . 'ID');
	}

	/**
	 * カレントレコードIDを設定する
	 *
	 * @access protected
	 * @param integer $id カレントレコードID
	 */
	protected function setRecordID ($id) {
		if (is_array($id)) {
			if (isset($id['id'])) {
				$id = $id['id'];
			} else {
				$id = $id[strtolower($this->getRecordClassName()) . '.id'];
			}
		}
		$this->user->setAttribute($this->getRecordClassName() . 'ID', $id);
	}
}

/* vim:set tabstop=4 ai: */
?>