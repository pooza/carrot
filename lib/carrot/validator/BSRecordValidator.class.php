<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * レコードバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
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
		return parent::initialize($parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		$flag = $this->isExist($value);
		if ($this->getParameter('exist') && !$flag) {
			$this->error = $this->getParameter('exist_error');
			return false;
		} else if (!$this->getParameter('exist') && $flag) {
			$this->error = $this->getParameter('duplicate_error');
			return false;
		}
		return true;
	}

	private function isExist ($value) {
		try {
			$values = array($this->getParameter('field') => $value);
			if ($recordFound = $this->getTable()->getRecord($values)) {
				if ($this->getParameter('update')) {
					if ($record = $this->controller->getAction()->getRecord()) {
						return ($record->getID() != $recordFound->getID());
					} else {
						throw new BSValidatorException('レコードが見つかりません。');
					}
				} else {
					return true;
				}
			}
		} catch (Exception $e) {
		}
		return false;
	}

	private function getTable () {
		if (!$table = $this->getParameter('table')) {
			throw new BSValidatorException('テーブル名が定義されていません。');
		}
		$class = BSString::pascalize($table) . 'Handler';
		return new $class;
	}
}

/* vim:set tabstop=4 ai: */
?>