<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * 文字列バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSStringValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['max'] = 1024;
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
		$min = $this['min'];
		if (($min != null) && (BSString::getWidth($value) < $min)) {
			$this->error = $this['min_error'];
			return false;
		}

		$max = $this['max'];
		if (($max != null) && ($max < BSString::getWidth($value))) {
			$this->error = $this['max_error'];
			return false;
		}

		return true;
	}
}

/* vim:set tabstop=4: */
