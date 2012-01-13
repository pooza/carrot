<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * メールアドレスバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMailAddressValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = array()) {
		$this['mobile_allowed'] = true;
		$this['mobile_allowed_error'] = 'ケータイ用のアドレスは使用できません。';
		$this['invalid_error'] = '正しいメールアドレスではありません。';
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
		if (!$email = BSMailAddress::create($value)) {
			$this->error = $this['invalid_error'];
			return false;
		}
		if (!$this['mobile_allowed'] && $email->isMobile()) {
			$this->error = $this['mobile_allowed_error'];
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */
