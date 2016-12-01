<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * パスワードバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPasswordValidator extends BSRegexValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = []) {
		if (!isset($params['digits'])) {
			$params['digits'] = 6;
		}
		$params['match'] = true;
		$params['match_error'] = $params['digits'] . '桁以上の英数字を入力して下さい。';
		$params['pattern'] = '[[:ascii:]]{' . $params['digits'] . ',}';

		return BSValidator::initialize($params);
	}
}

/* vim:set tabstop=4: */
