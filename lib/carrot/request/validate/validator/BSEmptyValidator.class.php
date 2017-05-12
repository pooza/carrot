<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.validate.validator
 */

/**
 * 必須バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSEmptyValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = []) {
		$this['required_msg'] = '空欄です。';
		return parent::initialize($params);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		if (self::isEmpty($value)) {
			$this->error = $this['required_msg'];
			return false;
		}
		return true;
	}

	/**
	 * フィールド値は空欄か？
	 *
	 * @access public
	 * @return boolean フィールド値が空欄ならばTrue
	 * @static
	 */
	static public function isEmpty ($value) {
		if (is_array($value) || ($value instanceof BSParameterHolder)) {
			$value = new BSArray($value);
			if ($value['is_file']) {
				return BSString::isBlank($value['name']);
			} else {
				$value->trim();
				return !$value->count();
			}
		} else {
			return BSString::isBlank($value);
		}
	}
}

