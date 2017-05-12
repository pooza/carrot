<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.validate.validator
 */

/**
 * 英字項目バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSEnglishValidator extends BSRegexValidator {
	const PATTERN = '^[\\n[:ascii:]]*$';

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = []) {
		$this['match'] = true;
		$this['match_error'] = '使用出来ない文字が含まれています。';
		$this['pattern'] = self::PATTERN;
		return BSValidator::initialize($params);
	}
}

