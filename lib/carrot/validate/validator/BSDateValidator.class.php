<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * 日付バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSDateValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this->setParameter('invalid_error', '日付が正しくありません。');
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
		try {
			$date = new BSDate($value);
		} catch (BSDateException $e) {
			$this->error = $this->getParameter('invalid_error');
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>