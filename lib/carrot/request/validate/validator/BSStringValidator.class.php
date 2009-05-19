<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * 文字列バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSStringValidator extends BSValidator {
	const MAX_SIZE = 1024;

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['max'] = self::MAX_SIZE;
		$this['max_error'] = '長すぎます。';
		$this['min'] = null;
		$this['min_error'] = '短すぎます。';
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
		if (BSArray::isArray($value)) {
			return true;
		}

		if (!BSString::isBlank($min = $this['min']) && (BSString::getWidth($value) < $min)) {
			$this->error = $this['min_error'];
			return false;
		}
		if (!BSString::isBlank($max = $this['max']) && ($max < BSString::getWidth($value))) {
			$this->error = $this['max_error'];
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */
