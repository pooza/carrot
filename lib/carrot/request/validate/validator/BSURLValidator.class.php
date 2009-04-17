<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * URLバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSURLValidator extends BSRegexValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['match'] = true;
		$this['match_error'] = '正しくありません。';
		$this['pattern'] = BSURL::PATTERN;
		return BSValidator::initialize($parameters);
	}
}

/* vim:set tabstop=4: */
