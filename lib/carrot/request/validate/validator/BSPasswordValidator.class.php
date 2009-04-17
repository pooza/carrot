<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * パスワードバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPasswordValidator extends BSRegexValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		if (!isset($parameters['digits'])) {
			$parameters['digits'] = 6;
		}
		$parameters['match'] = true;
		$parameters['match_error'] = $parameters['digits'] . '桁以上の英数字を入力して下さい。';
		$parameters['pattern'] = '/[[:print:]]{' . $parameters['digits'] . ',}/';

		return BSValidator::initialize($parameters);
	}
}

/* vim:set tabstop=4: */
