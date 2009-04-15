<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * 一致バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPairValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['field'] = null;
		$this['equal'] = true;
		$this['equal_error'] = '一致しません。';
		$this['lesser'] = false;
		$this['lesser_error'] = '小さすぎます。';
		$this['greater'] = false;
		$this['greater_error'] = '大きすぎます。';
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
		if (BSString::isBlank($name = $this['field'])) {
			return true;
		}

		if ($this['equal'] && ($value != BSRequest::getInstance()->getParameter($name))) {
			$this->error = $this['equal_error'];
			return false;
		}
		if ($this['lesser'] && (BSRequest::getInstance()->getParameter($name) < $value)) {
			$this->error = $this['lesser_error'];
			return false;
		}
		if ($this['greater'] && ($value < BSRequest::getInstance()->getParameter($name))) {
			$this->error = $this['greater_error'];
			return false;
		}

		return true;
	}
}

/* vim:set tabstop=4: */
