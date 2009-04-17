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

	/**
	 * 対象文字列から郵便番号を返す
	 *
	 * fiedlsパラメータが設定されている時はそちらを利用し、対象文字列を無視。
	 *
	 * @access private
	 * @param string $value 対象文字列
	 * @return BSZipcode 郵便番号
	 */
	private function getZipcode ($value) {
		if ($fields = $this['fields']) {
			$value = new BSArray;
			foreach ($fields as $field) {
				$value[] = $this->request[$field];
			}
			$value = $value->join('-');
		}

		try {
			return new BSZipcode($value);
		} catch (BSZipcodeException $e) {
		}
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['fields'] = array();
		$this['address'] = true;
		$this['invalid_error'] = '正しい郵便番号ではありません。';
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
		if (!$zipcode = $this->getZipcode($value)) {
			$this->error = $this['invalid_error'];
			return false;
		}

		if ($this['address']) {
			try {
				$address = $zipcode->getAddress();
			} catch (BSZipcodeException $e) {
				$this->error = $this['invalid_error'];
				return false;
			}
		}

		return true;
	}
}

/* vim:set tabstop=4: */
