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
		$this->setParameter('table', null);
		$this->setParameter('field', 'id');
		$this->setParameter('exist', true);
		$this->setParameter('update', false);
		$this->setParameter('exist_error', '登録されていません。');
		$this->setParameter('duplicate_error', '重複します。');
		$this->setParameter('valid_values', array());
		return parent::initialize($parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $id バリデート対象（レコードのID）
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($id) {
		if ($this->isExist($id)) {
			if (!$this->getParameter('exist')) {
				$this->error = $this->getParameter('duplicate_error');
				return false;
			} else if ($this->getParameter('valid_values') && !$this->validateValues($id)) {
				return false;
			}
		} else {
			if ($this->getParameter('exist')) {
				$this->error = $this->getParameter('exist_error');
				return false;
			}
		}
		return true;
	}

	private function isExist ($id) {
		if ($recordFound = $this->getRecord($id)) {
			if ($this->getParameter('update')) {
				$action = $this->controller->getAction();
				if ($record = $action->getRecord()) {
					return ($record->getID() != $recordFound->getID());
				} else {
					throw new BSValidateException('%sにレコードが見つかりません。', $action);
				}
			} else {
				return true;
			}
		}
		return false;
	}

	private function validateValues ($id) {
		$record = $this->getRecord($id);
		foreach ($this->getParameter('valid_values') as $fieldName => $validValue) {
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

	private function getRecord ($id) {
		try {
			$values = array($this->getParameter('field') => $id);
			return $this->getTable()->getRecord($values);
		} catch (Exception $e) {
		}
	}

	private function getTable () {
		if (!$table = $this->getParameter('table')) {
			throw new BSValidateException('テーブル名が定義されていません。');
		}
		$class = BSString::pascalize($table) . 'Handler';
		return new $class;
	}
}

/* vim:set tabstop=4 ai: */
?>