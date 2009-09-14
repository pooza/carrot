<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * フリガナ項目バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSKanaValidator extends BSRegexValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['match'] = true;
		$this['match_error'] = '使用出来ない文字が含まれています。';
		$this['pattern'] = "^[ぁ-んァ-ンヴー\\n[:digit:]]*$";
		return BSValidator::initialize($parameters);
	}
}

/* vim:set tabstop=4: */
