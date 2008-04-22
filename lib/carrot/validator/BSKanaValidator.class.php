<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * フリガナ項目バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSKanaValidator.class.php 199 2008-04-19 04:12:14Z pooza $
 */
class BSKanaValidator extends Validator {
	const PATTERN = '^[ぁ-んァ-ンヴー0-9]*$';

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = array()) {
		$this->setParameter('invalid_error', 'フリガナとして使用出来ない文字が含まれています。');
		return parent::initialize($context, $parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value 検査対象
	 * @param string $error エラーメッセージ
	 * @return boolean 結果
	 */
	public function execute (&$value, &$error) {
		if (!mb_ereg(self::PATTERN, $value)) {
			$error = $this->getParameter('invalid_error');
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>