<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate
 */

/**
 * バリデートマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSValidateManager implements IteratorAggregate {
	private $fields;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->fields = new BSArray;
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSValidateManager インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'request':
				return BSRequest::getInstance();
			default:
				throw new BSMagicMethodException('仮想プロパティ"%s"は未定義です。', $name);
		}
	}

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		foreach ($this as $field => $validators) {
			if ($validators['BSFileValidator']) {
				$value = $this->request->getFile($field);
				$value['is_file'] = true;
			} else {
				$value = $this->request[$field];
			}

			foreach ($validators as $validator) {
				if (!BSEmptyValidator::isEmpty($value)
					|| ($validator['fields'])
					|| ($validator instanceof BSEmptyValidator)) {

					if (!$validator->execute($value)) {
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
		return $this->fields->getKeys(BSArray::WITHOUT_KEY);
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
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return $this->fields->getIterator();
	}
}

/* vim:set tabstop=4: */

