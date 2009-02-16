<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * レコードバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSRecordValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['table'] = null;
		$this['class'] = null;
		$this['field'] = 'id';
		$this['exist'] = true;
		$this['update'] = false;
		$this['exist_error'] = '登録されていません。';
		$this['duplicate_error'] = '重複します。';
		$this['valid_values'] = array();
		return parent::initialize($parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象（レコードのID、又はその配列）
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		foreach ((array)$value as $id) {
			if ($this->isExist($id)) {
				if (!$this['exist']) {
					$this->error = $this['duplicate_error'];
					return false;
				} else if ($this['valid_values'] && !$this->validateValues($id)) {
					return false;
				}
			} else {
				if ($this['exist']) {
					$this->error = $this['exist_error'];
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * 該当するレコードが存在するか
	 *
	 * @access private
	 * @param integer $id 対象ID
	 * @return boolean 該当するレコードが存在するならTrue
	 */
	private function isExist ($id) {
		if ($recordFound = $this->getRecord($id)) {
			if ($this['update']) {
				$module = $this->controller->getModule();
				if ($record = $module->getRecord()) {
					return ($record->getID() != $recordFound->getID());
				} else {
					throw new BSValidateException('%sにレコードが見つかりません。', $module);
				}
			} else {
				return true;
			}
		}
		return false;
	}

	/**
	 * 該当するレコードのフィールド値が適切か
	 *
	 * @access private
	 * @param integer $id 対象ID
	 * @return boolean 該当するレコードのフィールド値が適切ならTrue
	 */
	private function validateValues ($id) {
		$record = $this->getRecord($id);
		foreach ($this['valid_values'] as $fieldName => $validValue) {
			$fieldValue = $record->getAttribute($fieldName);
			$message = sprintf(
				'%sが正しくありません。',
				BSTranslateManager::getInstance()->execute($fieldName)
			);
			if (is_array($validValue)) {
				if (!in_array($fieldValue, $validValue)) {
					$this->error = $message;
					return false;
				}
			} else {
				if ($fieldValue != $validValue) {
					$this->error = $message;
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * 該当するレコードを返す
	 *
	 * @access private
	 * @param integer $id 対象ID
	 * @return BSRecord 該当するレコード
	 */
	private function getRecord ($id) {
		try {
			$values = array($this['field'] => $id);
			return $this->getTable()->getRecord($values);
		} catch (Exception $e) {
		}
	}

	/**
	 * 対象テーブルを返す
	 *
	 * @access private
	 * @return BSTableHandler 対象テーブル
	 */
	private function getTable () {
		if (!$class = $this['class']) {
			$class = $this['table'];
		}
		return BSTableHandler::getClassName($class);
	}
}

/* vim:set tabstop=4: */
