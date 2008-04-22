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
 * @version $Id: BSMailAddressValidator.class.php 199 2008-04-19 04:12:14Z pooza $
 */
class BSMailAddressValidator extends Validator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = array()) {
		$this->setParameter('unique', false);
		$this->setParameter('unique_error', '重複します。');
		$this->setParameter('table', 'account');
		$this->setParameter('field', 'email');
		$this->setParameter('invalid_error', '正しいメールアドレスではありません。');
		return parent::initialize($context, $parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value バリデーション対象
	 * @param string $error エラーメッセージ代入先
	 * @return boolean 正しいメールアドレスならばTrue
	 */
	public function execute (&$value, &$error) {
		try {
			$email = new BSMailAddress($value);
		} catch (BSMailException $e) {
			$error = $this->getParameter('invalid_error');
			return false;
		}

		if ($this->getParameter('unique')) {
			$class = BSString::pascalize($this->getParameter('table')) . 'Handler';
			$table = new $class;
			$values = array($this->getParameter('field') => $value);
			if ($record = $table->getRecord($values)) {
				$controller = BSController::getInstance();
				$action = $controller->getActionStack()->getLastEntry()->getActionInstance();
				if ($id = $action->getRecordID()) {
					if ($id != $record->getID()) {
						$error = $this->getParameter('unique_error');
						return false;
					}
				} else {
					$error = $this->getParameter('unique_error');
					return false;
				}
			}
		}

		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>