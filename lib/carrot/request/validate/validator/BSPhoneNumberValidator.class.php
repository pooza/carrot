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
 */
class BSPhoneNumberValidator extends BSValidator {
	const PATTERN = '^[[:digit:]]{2,4}-[[:digit:]]{2,4}-[[:digit:]]{3,4}$';
	const PATTERN_LOOSE = '^[[:digit:]]{8,12}$';

	/**
	 * 対象文字列から電話番号を返す
	 *
	 * fiedlsパラメータが設定されている時はそちらを利用し、対象文字列を無視。
	 *
	 * @access private
	 * @param string $value 対象文字列
	 * @return string 電話番号
	 */
	private function getPhoneNumber ($value) {
		if ($fields = $this['fields']) {
			$value = new BSArray;
			foreach ($fields as $field) {
				$value[] = $this->request[$field];
			}
			$value = $value->join('-');
		}
		return $value;
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['fields'] = array();
		$this['invalid_error'] = '正しい電話番号ではありません。';
		$this['loose'] = false;
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
		if ($this['loose'] && !mb_ereg(self::PATTERN_LOOSE, $value)) {
			if (!mb_ereg(self::PATTERN, $this->getPhoneNumber($value))) {
				$this->error = $this['invalid_error'];
				return false;
			}
		}
		return true;
	}
}

/* vim:set tabstop=4: */
