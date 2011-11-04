<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * 感染バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSInfectionValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = array()) {
		$this['infection_error'] = '感染の疑いがあります。';
		return BSValidator::initialize($params);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		$file = new BSFile($value['tmp_name']);
		if ($file->isInfected()) {
			$message = new BSStringFormat('%s (%s)');
			$message[] = $this['infection_error'];
			$message[] = $file->getError();
			$this->error = $message->getContents();
		}
		return BSString::isBlank($this->error);
	}
}

/* vim:set tabstop=4: */
