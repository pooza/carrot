<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.validate.validator
 */

/**
 * 郵便番号バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSZipcodeValidator extends BSRegexValidator {
	const PATTERN = '^([[:digit:]]{3})-([[:digit:]]{4})$';

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = []) {
		$this['match'] = true;
		$this['match_error'] = '正しくありません。';
		$this['pattern'] = self::PATTERN;
		$this['fields'] = [];
		return BSValidator::initialize($params);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		if ($fields = $this['fields']) {
			$values = new BSArray;
			foreach ($fields as $field) {
				$values[] = $this->request[$field];
			}
			if (BSString::isBlank($values->join(''))) {
				return true;
			}
			$value = $values->join('-');
		}
		return parent::execute($value);
	}
}

/* vim:set tabstop=4: */
