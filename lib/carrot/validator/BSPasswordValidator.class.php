<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * パスワードバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
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
		$parameters['match'] = 'Yes';
		$parameters['match_error'] = $parameters['digits'] . '桁以上の英数字を入力して下さい。';
		$parameters['pattern'] = '/[[:print:]]{' . $parameters['digits'] . ',}/';

		return BSValidator::initialize($parameters);
	}
}

/* vim:set tabstop=4 ai: */
?>