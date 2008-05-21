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
	private $table;

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
	 * テーブルを返す
	 *
	 * @access public
	 * @return BSTableHandler テーブル
	 */
	public function getTable () {
		if (!$this->table && $this->getRecordClassName()) {
			$name = $this->getRecordClassName() . 'Handler';
			$this->table = new $name;
		}
		return $this->table;
	}

	/**
	 * 編集中レコードを返す
	 *
	 * @access public
	 * @return BSRecord 編集中レコード
	 */
	public function getRecord () {
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
	 * @access public
	 * @return integer カレントレコードID
	 */
	public function getRecordID () {
		if ($id = $this->request->getParameter('id')) {
			if (get_class($this) != 'CreateAction') {
				$this->setRecordID($id);
			}
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
		if (BSArray::isArray($id)) {
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