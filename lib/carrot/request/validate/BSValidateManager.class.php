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
	const VALIDATE_REQUIRED = 1;
	const VALIDATE_FILE = 2;
	const VALIDATE_VIRTUAL = 4;

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
		foreach ($this as $name => $info) {
			if ($info['is_file']) {
				$value = $this->request->getFile($name);
				$value['is_file'] = true;
			} else {
				$value = $this->request[$name];
			}
			$enable = (!BSEmptyValidator::isEmpty($value) || $info['is_virtual']);

			foreach ($info['validators'] as $validator) {
				if ($enable || ($validator instanceof BSEmptyValidator)) {
					if (!$validator->execute($value)) {
						$this->request->setError($name, $validator->getError());
						break;
					}
				}
			}
		}
		return !$this->request->hasErrors();
	}

	/**
	 * フィールドを登録
	 *
	 * @access public
	 * @param string $name フィールド名
	 * @param integer $flags フラグのビット列
	 *   VALIDATE_REQUIRED 必須項目
	 *   VALIDATE_FILE     アップロードファイル項目
	 *   VALIDATE_VIRTUAL  仮想項目（パラメータに含まれない項目）
	 * @param string $message 空欄時エラーメッセージ
	 */
	public function register ($name, $flags = null, $message = null) {
		$values = array(
			'name' => $name,
			'is_file' => ($flags & self::VALIDATE_FILE),
			'is_virtual' => ($flags & self::VALIDATE_VIRTUAL),
			'validators' => new BSArray,
		);
		$this->fields[$name] = new BSArray($values);

		if ($flags & self::VALIDATE_REQUIRED) {
			$validator = new BSEmptyValidator;
			$params = array();
			if ($message) {
				$params['required_msg'] = $message;
			}
			$validator->initialize($params);
			$this->registerValidator($name, $validator);
		}
		if ($flags & self::VALIDATE_FILE) {
			$validator = new BSFileValidator;
			$validator->initialize();
			$this->registerValidator($name, $validator);
		}
	}

	/**
	 * フィールドにバリデータを登録
	 *
	 * @access public
	 * @param string $name フィールド名
	 * @param BSValidator $validator バリデータ
	 */
	public function registerValidator ($name, BSValidator $validator) {
		if (!$this->fields->hasParameter($name)) {
			throw new BSValidateException('フィールド "%s" は登録されていません。', $name);
		}
		$this->fields[$name]['validators'][] = $validator;
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

