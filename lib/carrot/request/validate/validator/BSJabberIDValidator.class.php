<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * JabberIDバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJabberIDValidator extends BSRegexValidator {
	const PATTERN = '^([-_.[:alnum:]]+)@(([-_.[:alnum:]]+)+[[:alpha:]]+)(/([-_[:alnum:]]+))?$';

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = []) {
		$this['match'] = true;
		$this['match_error'] = '正しいJabberIDではありません。';
		$this['pattern'] = self::PATTERN;
		return BSValidator::initialize($params);
	}
}

/* vim:set tabstop=4: */
