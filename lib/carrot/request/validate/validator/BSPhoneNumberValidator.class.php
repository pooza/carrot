<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * 電話番号バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @link http://www.soumu.go.jp/main_sosiki/joho_tsusin/top/tel_number/ 電話番号の桁数について
 */
class BSPhoneNumberValidator extends BSRegexValidator {
	const PATTERN = '^[[:digit:]]{2,4}-[[:digit:]]{2,4}-[[:digit:]]{3,4}$';
	const PATTERN_LOOSE = '^[-[:digit:]]{10,14}$';

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = array()) {
		if (!isset($params['loose'])) {
			$params['loose'] = false;
		}

		$this['match'] = true;
		$this['invalid_error'] = '正しくありません。';
		$this['fields'] = array();
		BSValidator::initialize($params);

		if (!!$this['loose']) {
			$this['pattern'] = self::PATTERN_LOOSE;
		} else {
			$this['pattern'] = self::PATTERN;
		}
		return true;
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		if ($fields = $this['fields']) {
			$values = new BSArray;
			foreach ($fields as $field) {
				$values[] = $this->request[$field];
			}
			$value = $values->join('-');
		}
		return parent::execute($value);
	}
}

/* vim:set tabstop=4: */
