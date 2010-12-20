<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * 郵便番号バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSZipcodeValidator extends BSValidator {
	const PATTERN = '^([[:digit:]]{3})-([[:digit:]]{4})$';

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = array()) {
		$this['fields'] = array();
		$this['invalid_error'] = '正しい郵便番号ではありません。';
		return parent::initialize($params);
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
			$value = new BSArray;
			foreach ($fields as $field) {
				$value[] = $this->request[$field];
			}
			$value = $value->join('-');
		}
		if (!mb_ereg(self::PATTERN, $value)) {
			$this->error = $this['invalid_error'];
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */
