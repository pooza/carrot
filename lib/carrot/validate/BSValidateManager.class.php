<?php
/**
 * @package org.carrot-framework
 * @subpackage validate
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
			self::$instance = new BSValidateManager;
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
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		foreach ($this as $name => $info) {
			if ($info['is_file']) {
				$value = BSRequest::getInstance()->getFile($name);
				$value['is_file'] = true;
			} else {
				$value = BSRequest::getInstance()->getParameter($name);
			}
			$enable = (!BSEmptyValidator::isEmpty($value) || $info['is_virtual']);

			foreach ($info['validators'] as $validator) {
				if ($enable || ($validator instanceof BSEmptyValidator)) {
					if (!$validator->execute($value)) {
						BSRequest::getInstance()->setError($name, $validator->getError());
						break;
					}
				}
			}
		}
		return !BSRequest::getInstance()->hasErrors();
	}

	/**
	 * フィールドを登録
	 *
	 * @access public
	 * @param string $name フィールド名
	 * @param integer $option オプションのビット列
	 *   VALIDATE_REQUIRED 必須項目
	 *   VALIDATE_FILE     アップロードファイル項目
	 *   VALIDATE_VIRTUAL  仮想項目（パラメータに含まれない項目）
	 * @param string $message 空欄時エラーメッセージ
	 */
	public function register ($name, $option = null, $message = null) {
		$values = array(
			'name' => $name,
			'is_file' => ($option & self::VALIDATE_FILE),
			'is_virtual' => ($option & self::VALIDATE_VIRTUAL),
			'validators' => new BSArray,
		);
		$this->fields[$name] = new BSArray($values);

		if ($option & self::VALIDATE_REQUIRED) {
			$validator = new BSEmptyValidator;
			$params = array();
			if ($message) {
				$params['required_msg'] = $message;
			}
			$validator->initialize($params);
			$this->registerValidator($name, $validator);
		}
		if ($option & self::VALIDATE_FILE) {
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

/* vim:set tabstop=4 ai: */
?>
