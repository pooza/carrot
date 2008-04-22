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
 * @version $Id: BSPasswordValidator.class.php 199 2008-04-19 04:12:14Z pooza $
 */
class BSPasswordValidator extends RegexValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = array()) {
		if (!isset($parameters['digits'])) {
			$parameters['digits'] = 6;
		}
		$parameters['match'] = 'Yes';
		$parameters['match_error'] = $parameters['digits'] . '桁以上の英数字を入力して下さい。';
		$parameters['pattern'] = '/[[:print:]]{' . $parameters['digits'] . ',}/';

		return parent::initialize($context, $parameters);
	}
}
/* vim:set tabstop=4 ai: */
?>