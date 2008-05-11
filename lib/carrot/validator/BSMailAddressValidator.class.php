<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * メールアドレスバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
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
		$this->setParameter('unique', false);
		$this->setParameter('unique_error', '重複します。');
		$this->setParameter('domain', false);
		$this->setParameter('domain_error', '正しいドメインではない様です。');
		$this->setParameter('table', 'account');
		$this->setParameter('field', 'email');
		$this->setParameter('invalid_error', '正しいメールアドレスではありません。');
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
			$this->error = $this->getParameter('invalid_error');
			return false;
		}

		if (!$email->isValidDomain()) {
			$this->error = $this->getParameter('domain_error');
			return false;
		}

		if ($this->getParameter('unique')) {
			$class = BSString::pascalize($this->getParameter('table')) . 'Handler';
			$table = new $class;
			$values = array($this->getParameter('field') => $value);
			if ($record = $table->getRecord($values)) {
				if ($id = $this->controller->getAction()->getRecordID()) {
					if ($id != $record->getID()) {
						$this->error = $this->getParameter('unique_error');
						return false;
					}
				} else {
					$this->error = $this->getParameter('unique_error');
					return false;
				}
			}
		}

		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>