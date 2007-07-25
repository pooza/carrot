<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage action
 */

/**
 * 詳細画面用 アクションひな形
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: BSRecordAction.class.php 361 2007-07-15 12:42:45Z pooza $
 * @abstract
 */
abstract class BSRecordAction extends BSAction {
	private $record;

	/**
	 * 更新レコードのフィールド値を配列で返す
	 *
	 * @access protected
	 * @return mixed[] フィールド値の連想配列
	 * @abstract
	 */
	abstract protected function getRecordValues ();

	/**
	 * 編集中レコードを返す
	 *
	 * @access protected
	 * @return BSRecord 編集中レコード
	 */
	protected function getRecord () {
		if (!$id = $this->getRecordID()) {
			return null;
		}
		if (!$this->record) {
			$this->record = $this->getTable()->getRecord($id);
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

	public function initialize ($context) {
		parent::initialize($context);

		// フリガナをカタカナに変換
		foreach ($this->request->getParameters() as $key => $value) {
			if (preg_match('/_read/', $key)) {
				$value = BSString::convertKana($value, 'KVC');
				$this->request->setParameter($key, $value);
			}
		}

		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>