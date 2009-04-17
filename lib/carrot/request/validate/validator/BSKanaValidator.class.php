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
class BSKanaValidator extends BSValidator {
	const PATTERN = '/^[ぁ-んァ-ンヴー0-9]*$/u';

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['invalid_error'] = '使用出来ない文字が含まれています。';
		return parent::initialize($parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		if (!preg_match(self::PATTERN, $value)) {
			$this->error = $this['invalid_error'];
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */
