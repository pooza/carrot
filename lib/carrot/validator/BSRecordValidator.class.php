<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * レコードバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSRecordValidator.class.php 200 2008-04-19 06:55:55Z pooza $
 */
class BSRecordValidator extends Validator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = array()) {
		$this->setParameter('table', null);
		$this->setParameter('field', 'id');
		$this->setParameter('exist', true);
		$this->setParameter('exist_error', '存在しません。');
		$this->setParameter('duplicate_error', '重複します。');
		return parent::initialize($context, $parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value バリデーション対象
	 * @param string $error エラーメッセージ代入先
	 * @return boolean そのIDを持ったレコードが存在すればTrue
	 */
	public function execute (&$value, &$error) {
		try {
			$class = BSString::pascalize($this->getParameter('table')) . 'Handler';
			$table = new $class;
			$values = array($this->getParameter('field') => $value);
			$isExist = ($table->getRecord($values) != null);
		} catch (Exception $e) {
			$isExist = false;
		}

		if ($this->getParameter('exist') && !$isExist) {
			$error = $this->getParameter('exist_error');
			return false;
		} else if (!$this->getParameter('exist') && $isExist) {
			$error = $this->getParameter('duplicate_error');
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>