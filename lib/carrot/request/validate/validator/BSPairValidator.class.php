<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * 一致バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPairValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = array()) {
		$this['field'] = null;
		$this['equal'] = true;
		$this['equal_error'] = '一致しません。';
		$this['lesser'] = false;
		$this['lesser_error'] = '小さすぎます。';
		$this['greater'] = false;
		$this['greater_error'] = '大きすぎます。';
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
		if (BSString::isBlank($name = $this['field'])) {
			throw new BSConfigException(get_class($this) . 'の対象フィールドが未定義です。');
		}
		if ($this['equal'] && ($value !== $this->request[$name])) {
			$this->error = $this['equal_error'];
			return false;
		}
		if (!BSString::isBlank($this->request[$name])) {
			if ($this['lesser'] && ($this->request[$name] < $value)) {
				$this->error = $this['lesser_error'];
				return false;
			}
			if ($this['greater'] && ($value < $this->request[$name])) {
				$this->error = $this['greater_error'];
				return false;
			}
		}
		return true;
	}
}

/* vim:set tabstop=4: */
