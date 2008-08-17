<?php
/**
 * @package org.carrot-framework
 * @subpackage validator
 */

/**
 * 文字列バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
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
		$this->setParameter('max', 1024);
		$this->setParameter('max_error', '長すぎます。');
		$this->setParameter('min', null);
		$this->setParameter('min_error', '短すぎます。');
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
		$min = $this->getParameter('min');
		if (($min != null) && (strlen($value) < $min)) {
			$this->error = $this->getParameter('min_error');
			return false;
		}

		$max = $this->getParameter('max');
		if (($max != null) && ($max < strlen($value))) {
			$this->error = $this->getParameter('max_error');
			return false;
		}

		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>