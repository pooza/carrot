<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.validate
 */

/**
 * バリデートマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSValidateManager implements IteratorAggregate {
	use BSSingleton, BSBasicObject;
	private $fields;

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this->fields = new BSArray;
	}

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		foreach ($this as $field => $validators) {
			foreach ($validators as $validator) {
				if (!BSEmptyValidator::isEmpty($this->request[$field])
					|| ($validator['fields'])
					|| ($validator instanceof BSEmptyValidator)) {

					if (!$validator->execute($this->request[$field])) {
						$this->request->setError($field, $validator->getError());
						break;
					}
				}
			}
		}
		return !$this->request->hasErrors();
	}

	/**
	 * フィールドにバリデータを登録
	 *
	 * @access public
	 * @param string $name フィールド名
	 * @param BSValidator $validator バリデータ
	 */
	public function register ($name, BSValidator $validator) {
		if (!$this->fields[$name]) {
			$this->fields[$name] = new BSArray;
		}
		$this->fields[$name][$validator->getName()] = $validator;
	}

	/**
	 * フィールド名を返す
	 *
	 * @access public
	 * @return BSArray フィールド名
	 */
	public function getFieldNames () {
		return $this->fields->getKeys();
	}

	/**
	 * フィールド値を返す
	 *
	 * @access public
	 * @return BSArray フィールド値
	 */
	public function getFieldValues () {
		$values = new BSArray;
		foreach ($this->getFieldNames() as $name) {
			$values[$name] = $this->request[$name];
		}
		return $values;
	}

	/**
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return $this->fields->getIterator();
	}
}

