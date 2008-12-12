<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * メールアドレスバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMailAddressValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['unique'] = false;
		$this['unique_error'] = '重複します。';
		$this['domain'] = false;
		$this['domain_error'] = '正しいドメインではない様です。';
		$this['mobile_allowed'] = true;
		$this['mobile_allowed_error'] = '携帯電話用のアドレスは使用出来ません。';
		$this['table'] = 'account';
		$this['field'] = 'email';
		$this['invalid_error'] = '正しいメールアドレスではありません。';
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
			$email = new BSMailAddress($value);
		} catch (BSMailException $e) {
			$this->error = $this['invalid_error'];
			return false;
		}

		if ($this['domain'] && !$email->isValidDomain()) {
			$this->error = $this['domain_error'];
			return false;
		}

		if (!$this['mobile_allowed'] && $email->isMobile()) {
			$this->error = $this['mobile_allowed_error'];
			return false;
		}

		if ($this['unique']) {
			$class = BSString::pascalize($this['table']) . 'Handler';
			$table = new $class;
			$values = array($this['field'] => $value);
			if ($record = $table->getRecord($values)) {
				if ($id = $this->controller->getModule()->getRecordID()) {
					if ($id != $record->getID()) {
						$this->error = $this['unique_error'];
						return false;
					}
				} else {
					$this->error = $this['unique_error'];
					return false;
				}
			}
		}

		return true;
	}
}

/* vim:set tabstop=4: */
