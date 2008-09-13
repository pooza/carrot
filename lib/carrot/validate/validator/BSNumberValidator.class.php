<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * 数値バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSNumberValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this->setParameter('max', null);
		$this->setParameter('max_error', '値が大きすぎます。');
		$this->setParameter('min', null);
		$this->setParameter('min_error', '値が小さすぎます。');
		$this->setParameter('nan_error', '数値を入力して下さい。');
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
		if (!is_numeric($value)) {
			$this->error = $this->getParameter('nan_error');
			return false;
		}

		$min = $this->getParameter('min');
		if (($min != null) && ($value < $min)) {
			$this->error = $this->getParameter('min_error');
			return false;
		}

		$max = $this->getParameter('max');
		if (($max != null) && ($max < $value)) {
			$this->error = $this->getParameter('max_error');
			return false;
		}

		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>