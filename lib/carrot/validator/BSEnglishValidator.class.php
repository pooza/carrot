<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * 英字項目バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSEnglishValidator extends BSRegexValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this->setParameter('match', true);
		$this->setParameter('match_error', '使用出来ない文字が含まれています。');
		$this->setParameter('pattern', "/^[[:print:]\n]*$/");
		return BSValidator::initialize($parameters);
	}
}

/* vim:set tabstop=4 ai: */
?>