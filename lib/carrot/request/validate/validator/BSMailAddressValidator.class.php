<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * メールアドレスバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMailAddressValidator extends BSValidator {

	/**
	 * 対象テーブルを返す
	 *
	 * @access private
	 * @return BSTableHandler 対象テーブル
	 */
	private function getTable () {
		if (!$class = $this['class']) {
			$class = $this['table'];
		}
		return BSTableHandler::getInstance($class);
	}

	/**
	 * 登録済みのアドレスか
	 *
	 * @access private
	 * @param BSMailAddress $email メールアドレス
	 * @return boolean 登録済みならTrue
	 */
	private function isRegisteredAddress (BSMailAddress $email) {
		$values = array($this['field'] => $email->getContents());
		if ($record = $this->getTable()->getRecord($values)) {
			if ($id = $this->controller->getModule()->getRecordID()) {
				if ($id != $record->getID()) {
					return true;
				}
			} else {
				return true;
			}
		}
		return false;
	}

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
		$this['class'] = null;
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

		if ($this['unique'] && $this->isRegisteredAddress($email)) {
			$this->error = $this['unique_error'];
			return false;
		}

		return true;
	}
}

/* vim:set tabstop=4: */
