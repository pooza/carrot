<?php
/**
 * @package org.carrot-framework
 * @subpackage validator
 */

/**
 * URLバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
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
		$this->setParameter('match', true);
		$this->setParameter('match_error', '正しくありません。');
		$this->setParameter('pattern', BSURL::PATTERN);
		return BSValidator::initialize($parameters);
	}
}

/* vim:set tabstop=4 ai: */
?>