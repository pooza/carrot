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
		$this->setParameter('table', 'account');
		$this->setParameter('field', 'email');
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
			$error = '正しいメールアドレスではありません。';
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
						$error = '重複します。';
						return false;
					}
				} else {
					$error = '重複します。';
					return false;
				}
			}
		}

		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>