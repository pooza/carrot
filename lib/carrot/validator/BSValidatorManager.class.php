<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * バリデータマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSValidatorManager implements IteratorAggregate {
	private $fields;
	static private $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$this->fields = new BSArray;
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSValidatorManager インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSValidatorManager();
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
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
			$isEmpty = BSEmptyValidator::isEmpty($value);

			foreach ($info['validators'] as $validator) {
				if (($validator instanceof BSEmptyValidator) || !$isEmpty) {
					if (!$validator->execute($value)) {
						BSRequest::getInstance()->setError($name, $validator->getError());
						break;
					}
				}
			}
		}
		return (count(BSRequest::getInstance()->getErrors()) < 1);
	}

	/**
	 * フィールドを登録
	 *
	 * @access public
	 * @param string $name フィールド名
	 * @param boolean $required 必須項目ならTrue
	 * @param string $message 空欄時エラーメッセージ
	 * @param boolean $isFile ファイルならTrue
	 */
	public function register ($name, $required = false, $message = null, $isFile = false) {
		$values = array(
			'name' => $name,
			'is_file' => $isFile,
			'validators' => new BSArray,
		);
		$this->fields[$name] = new BSArray($values);

		if ($required) {
			$validator = new BSEmptyValidator();
			$params = array();
			if ($message) {
				$params['required_msg'] = $message;
			}
			$validator->initialize($params);
			$this->registerValidator($name, $validator);
		}

		if ($isFile) {
			$validator = new BSFileValidator();
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
			throw new BSValidatorException('フィールド "%s" は登録されていません。', $name);
		}
		$this->fields[$name]['validators'][] = $validator;
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return ArrayIterator 配列イテレータ
	 */
	public function getIterator () {
		return $this->fields->getIterator();
	}
}

/* vim:set tabstop=4 ai: */
?>
